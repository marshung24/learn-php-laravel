<?php

/**
 * Web 路由定義
 *
 * 這裡定義所有的 Web 路由（回傳 HTML 頁面）
 * 這些路由會套用 web 中介層群組（session、CSRF 保護等）
 */

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
