<?php

/**
 * API 路由定義
 *
 * 這裡定義所有的 API 路由（回傳 JSON）
 * 這些路由會自動加上 /api 前綴
 */

use App\Http\Controllers\Api\BookApiController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'OK']);
});

// 書籍管理 REST API
Route::get('/books', [BookApiController::class, 'index']);
Route::get('/books/{id}', [BookApiController::class, 'show']);
Route::post('/books', [BookApiController::class, 'store']);
Route::put('/books/{id}', [BookApiController::class, 'update']);
Route::delete('/books/{id}', [BookApiController::class, 'destroy']);
