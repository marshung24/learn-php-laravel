<?php

/**
 * Laravel 應用程式配置
 *
 * 這是 Laravel 12 的應用程式入口點，使用流暢的配置 API
 * 一個檔案完成路由、中介層、例外處理的設定
 */

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 中介層設定
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // API 請求的 ModelNotFoundException 回傳 404 JSON
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Resource not found',
                ], 404);
            }
        });
    })->create();
