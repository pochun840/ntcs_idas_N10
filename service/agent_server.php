<?php
$server = new swoole_websocket_server("0.0.0.0", 9501);

// ✅ WebSocket 不要把 max_request 設太低（否則長連線會被切）
$server->set([
    'worker_num' => 2,
    'max_request' => 0,                 
    'dispatch_mode' => 3,
    'heartbeat_check_interval' => 30,
    'heartbeat_idle_time' => 60,
]);

// ✅ 第二個埠也要明確啟用 WebSocket 協定
$port2 = $server->addlistener("0.0.0.0", 9502, SWOOLE_SOCK_TCP);
$port2->set([
    'open_http_protocol' => true,
    'open_websocket_protocol' => true,
]);

$port2->on('open', function (swoole_websocket_server $server, $request) {
    echo "Client connected on 9502: {$request->fd}\n";
    if ($server->isEstablished($request->fd)) {
        $server->push($request->fd, "Welcome! You are on port 9502");
    }
});

$port2->on('message', function (swoole_websocket_server $server, $frame) {
    echo "Received on 9502: {$frame->data}\n";
    if ($server->isEstablished($frame->fd)) {
        $server->push($frame->fd, "You sent: {$frame->data}");
    }
});

$port2->on('close', function ($server, $fd) {
    echo "Client disconnected from 9502: {$fd}\n";
});



$server->on('open', function (swoole_websocket_server $server, $request) {
    if ($server->isEstablished($request->fd)) {
        $server->push($request->fd, "Welcome to the server!");
    }

    foreach ($server->connections as $fd) {
        if ($server->isEstablished($fd)) {
            $server->push($fd, "Client {$request->fd} connected");
        }
    }
});

$server->on('message', function (swoole_websocket_server $server, $frame) {
    // 廣播訊息
    foreach ($server->connections as $fd) {
        if ($server->isEstablished($fd)) {
            $server->push($fd, "Client {$frame->fd} said: {$frame->data}");
        }
    }
});

$server->on('close', function (swoole_websocket_server $server, $fd) {
    $closedFd = $fd; 
    foreach ($server->connections as $toFd) {
        if ($server->isEstablished($toFd)) {
            $server->push($toFd, "Client {$closedFd} disconnected");
        }
    }
});

$server->start();
