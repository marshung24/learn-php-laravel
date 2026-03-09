<?php

/**
 * Laravel 應用程式入口點
 *
 * 所有 HTTP 請求都會經過這個檔案
 * Web 伺服器應該把所有請求導向這裡
 */

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 判斷應用是否處於維護模式
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// 註冊 Composer autoloader
require __DIR__.'/../vendor/autoload.php';

// 啟動 Laravel 並處理請求
/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
