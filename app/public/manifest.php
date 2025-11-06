<?php
// 導入config檔
include('../app/config/config.php');

// 設定header，告訴瀏覽器這是一個JSON文件
header('Content-Type: application/json');

// 假設這些變數是你從其他地方獲得的
$themeColor = '#FFFFFF';
$appName = SUBTITLE_INDEX;

// 根據 PHP 變數生成JSON
$manifest = [
    "name" => $appName,
    "short_name" => "iDAS",
    // "start_url" => "/",
    "display" => "standalone",
    // "background_color" => $themeColor,
    // "theme_color" => $themeColor,
    "icons" => [
        [
            "src" => ICON_NORMAL_APPLE,
            "sizes" => "60x60",
            "type" => "image/png"
        ],
        [
            "src" => ICON_NORMAL,
            "sizes" => "192x192",
            "type" => "image/png"
        ]
        
    ]
];

// 輸出JSON
echo json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
?>
