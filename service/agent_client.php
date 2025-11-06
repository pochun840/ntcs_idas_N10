<?php
use Swoole\Coroutine;
use Swoole\Coroutine\Http\Client as WsClient;
use function Swoole\Coroutine\run;

// ───────── 基本設定：讀 iDAS 設定 ─────────
$db_iDas = new PDO('sqlite:/var/www/html/database/das.db');
$result = $db_iDas->query("SELECT * FROM config WHERE config_name = 'agent_type'");
$rows   = $result->fetch(PDO::FETCH_ASSOC);
$agent_type = $rows['config_value'] ?? 1;

$result = $db_iDas->query("SELECT * FROM config WHERE config_name = 'agent_server_ip'");
$rows   = $result->fetch(PDO::FETCH_ASSOC);
$agent_ip = $rows['config_value'] ?? '127.0.0.1';

$db_iDas = null;

if ((string)$agent_type === '2') {
    $agent_ip = '127.0.0.1';
}
define('AGENT_IP', $agent_ip);

// ───────── 通用 WebSocket 推送器 ─────────
/**
 * 啟動一個 WS 推送器（自動連線、送資料、斷線重連）
 * @param string   $ip
 * @param int      $port
 * @param callable $getMessage   回傳「字串(JSON)」的函式
 * @param int      $interval     推送間隔秒數
 * @param string   $path         WS 路徑（預設 "/"）
 */
function startWsPusher(string $ip, int $port, callable $getMessage, int $interval = 1, string $path = '/')
{
    go(function() use ($ip, $port, $getMessage, $interval, $path) {
        $client    = new WsClient($ip, $port);
        $connected = $client->upgrade($path);

        while (true) {
            if ($connected) {
                // 已連線：固定頻率送資料
                while (true) {
                    $msg = $getMessage();              // 建議回傳 json_encode(...) 的字串
                    $ok  = $client->push($msg);
                    if ($ok === false) {
                        $connected = false;            // 視為斷線，跳出進入重連
                        break;
                    }
                    Coroutine::sleep($interval);
                }
            } else {
                // 未連線：重新嘗試升級
                unset($client);
                $client    = new WsClient($ip, $port);
                $connected = $client->upgrade($path);
                Coroutine::sleep(30);                 // 重連間隔(秒) — 註解與數值一致
            }
        }
    });
}

// ─────────DB 最新結果 ─────────
function GetLastResult(){

    $path = '/home/kls/NTCS7/ntcs_data.db';
    if (!file_exists($path)) {
        return json_encode(['message' => 'data db not found'], JSON_UNESCAPED_UNICODE);
    }

    try {
        $db_data = new PDO('sqlite:' . $path);
        $res     = $db_data->query("SELECT * FROM ntcs_data ORDER BY rowid DESC LIMIT 1");
        $row     = $res ? $res->fetch(PDO::FETCH_ASSOC) : null;

        if (!$row) {
            return json_encode(['message' => 'ntcs_data table is empty'], JSON_UNESCAPED_UNICODE);
        }

        // 補 device_name（若檔案存在）
        $idasPath = '/var/www/html/database/ntcs_device_IDAS.db';
        if (file_exists($idasPath)) {
            $db_tcscon   = new PDO('sqlite:' . $idasPath);
            $result2     = $db_tcscon->query("SELECT * FROM ntcs_device_test");
            $device_info = $result2 ? $result2->fetch(PDO::FETCH_ASSOC) : null;
            $row['device_name'] = $device_info['device_name'] ?? 'unknown';
        } else {
            $row['device_name'] = 'unknown';
        }

        $row['client_ip'] = getIp();
        return json_encode($row, JSON_UNESCAPED_UNICODE);
    } catch (\Throwable $e) {
        return json_encode(['error' => 'GetLastResult failed', 'detail' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
}

// ─────────IDAS 自定義 ─────────
function Getcustomize(){

    $csvPath = '/var/www/html/temp/customize.csv';
    if (!file_exists($csvPath)) {
        return json_encode(['message' => 'customize.csv not found'], JSON_UNESCAPED_UNICODE);
    }

    $fp = @fopen($csvPath, 'r');
    if (!$fp) {
        return json_encode(['message' => 'cannot open customize.csv'], JSON_UNESCAPED_UNICODE);
    }

    $first = fgetcsv($fp);
    if ($first === false) {
        fclose($fp);
        return json_encode(['message' => 'customize.csv is empty'], JSON_UNESCAPED_UNICODE);
    }
    if (isset($first[0])) {
        $first[0] = preg_replace('/^\xEF\xBB\xBF/', '', $first[0]); // 去 BOM
    }

    $norm = fn($s) => strtolower(trim((string)$s));
    $headMap = [];
    foreach ($first as $i => $h) $headMap[$norm($h)] = $i;

    $possibleHeaders = ['no','read','read_position','input','input_position','result','read_pos','input_pos'];
    $hasHeader = count(array_intersect(array_keys($headMap), $possibleHeaders)) > 0;

    $getIdx = function($names) use ($headMap) {
        foreach ((array)$names as $n) if (array_key_exists($n, $headMap)) return $headMap[$n];
        return null;
    };

    if ($hasHeader) {
        $idxNo     = $getIdx(['no','index','#']);
        $idxRead   = $getIdx(['read_position','read','read_pos']);
        $idxInput  = $getIdx(['input_position','input','input_pos']);
        $idxResult = $getIdx(['result','value']);
    } else {
        $idxNo = 0; $idxRead = 1; $idxInput = 2; $idxResult = 3;
        $rowsRaw = [$first]; // 第一行當資料
    }

    if (!isset($rowsRaw)) $rowsRaw = [];
    while (($r = fgetcsv($fp)) !== false) $rowsRaw[] = $r;
    fclose($fp);

    $rows = [];
    foreach ($rowsRaw as $r) {
        $val = fn($i) => ($i !== null && isset($r[$i])) ? trim((string)$r[$i]) : '';

        $no     = $val($idxNo);
        $read   = $val($idxRead);
        $input  = $val($idxInput);
        $result = $val($idxResult);

        if ($no === '' && $read === '' && $input === '' && $result === '') continue;
        if ($no !== '' && ctype_digit($no)) $no = (int)$no;

        $rows[] = [
            'no'             => $no,
            'read_position'  => $read,
            'input_position' => $input,
            'result'         => $result,
        ];
    }

    return json_encode(['rows' => $rows], JSON_UNESCAPED_UNICODE);
}

// ───────── 取得本機 IP（挑第一個非 127.*） ─────────
function getIp(): string
{
    if (PHP_OS_FAMILY === 'Linux') {
        $ips = trim(shell_exec("/sbin/ip -o -4 addr list | awk '{print \$4}' | cut -d/ -f1"));
        $list = array_filter(explode(PHP_EOL, $ips), fn($ip) => $ip && strpos($ip, '127.') !== 0);
        $first = reset($list);
        return strtoupper($first ?: '127.0.0.1');
    } else {
        $host = gethostname();
        $ip   = gethostbyname($host);
        return strtoupper($ip);
    }
}

// ───────── 啟動兩個推送器：9501 / 9502 ─────────
run(function () {
    // 9501：送 DB 最新結果（每 1 秒）
    startWsPusher(AGENT_IP, 9501, function () {
        return GetLastResult();
    }, 1);

    // 9502：送 customize.csv（每 1 秒；可視需求改成 2 或 5 秒）
    startWsPusher(AGENT_IP, 9502, function () {
        return Getcustomize('/var/www/html/temp/customize.csv');
    }, 1);

    // 主 coroutine 常駐
    while (true) { Coroutine::sleep(3600); }
});

