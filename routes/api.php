<?php

/**
 * API 路由定義
 *
 * 這裡定義所有的 API 路由（回傳 JSON）
 * 這些路由會自動加上 /api 前綴
 */

use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'OK']);
});
