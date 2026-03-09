<?php

/**
 * Web 路由定義
 *
 * 這裡定義所有的 Web 路由（回傳 HTML 頁面）
 * 這些路由會套用 web 中介層群組（session、CSRF 保護等）
 */

use App\Http\Controllers\BookController;
use App\Http\Controllers\HelloController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Hello 端點
Route::get('/hello', [HelloController::class, 'index']);
Route::get('/whoami', [HelloController::class, 'whoami']);
Route::get('/health', [HelloController::class, 'health']);

// 書籍管理（MVC 頁面）
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
Route::post('/books', [BookController::class, 'store'])->name('books.store');
Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');
