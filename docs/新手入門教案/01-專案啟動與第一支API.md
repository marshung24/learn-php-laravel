# U01｜專案啟動與第一支 API

> 能啟動專案並用瀏覽器呼叫自訂端點 ｜ 90 min ｜ 前置依賴：無

---

## 為什麼先教這個？

讓學員在第一堂課就看到「程式跑起來」的成就感。沒有什麼比親手讓一個 Web 應用回應你的請求更能建立學習信心。

---

## 對應檔案

| 檔案 | 角色 |
|------|------|
| `composer.json` | 依賴管理與建置設定（定義專案用到的所有套件） |
| `bootstrap/app.php` | Laravel 應用程式進入點與配置 |
| `routes/web.php` | Web 路由定義 |
| `app/Http/Controllers/HelloController.php` | 最簡易的 Controller |

---

## 核心觀念

### 專案標準目錄架構

```
learn-php-laravel/
├── app/                              ← 應用程式碼
│   ├── Http/
│   │   ├── Controllers/              ← Controller
│   │   ├── Middleware/               ← 中介層
│   │   └── Requests/                 ← Form Request 驗證
│   ├── Models/                       ← Eloquent Model
│   └── Services/                     ← Service 層
├── bootstrap/
│   └── app.php                       ← 應用程式入口
├── config/                           ← 設定檔
├── database/
│   ├── migrations/                   ← Migration 檔案
│   └── seeders/                      ← Seeder 檔案
├── resources/
│   └── views/                        ← Blade 模板
├── routes/
│   ├── web.php                       ← Web 路由
│   └── api.php                       ← API 路由
├── tests/                            ← 測試
├── composer.json                     ← 依賴管理
└── docker-compose.yml                ← 容器編排
```

### `bootstrap/app.php` — 應用程式配置

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 中介層設定
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 例外處理設定
    })->create();
```

Laravel 12 使用流暢的配置 API，一個檔案完成路由、中介層、例外處理的設定。

### 路由定義

```php
// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelloController;

Route::get('/hello', [HelloController::class, 'index']);
Route::get('/whoami', [HelloController::class, 'whoami']);
```

`Route::get()` 把 URL 對應到 Controller 方法。

### Controller — 最簡易的端點

```php
// app/Http/Controllers/HelloController.php
namespace App\Http\Controllers;

class HelloController extends Controller
{
    public function index()
    {
        return 'Hello, Laravel!';
    }

    public function whoami()
    {
        return response()->json([
            'name' => '你的名字',
        ]);
    }
}
```

### Composer 做了什麼

`composer.json` 的 require 區塊定義了所有用到的套件：

```json
{
    "require": {
        "php": "^8.4",
        "laravel/framework": "^12.0",
        "darkaonline/l5-swagger": "^8.0",
        "predis/predis": "^2.0"
    }
}
```

`composer install` 下載依賴、產生 autoload 檔案，讓我們可以直接使用所有套件的類別。

### 內嵌開發伺服器

```bash
php artisan serve
```

不用另外裝 Apache/Nginx，`artisan serve` 就能跑。預設監聽 `http://localhost:8000`。

---

## 動手做

### 必做

**1. 啟動專案**

```bash
# 啟動 MySQL 與 Redis（後續單元會用到）
docker compose up -d

# 複製環境設定
cp .env.example .env

# 產生 Application Key
php artisan key:generate

# 啟動 Laravel 開發伺服器
php artisan serve
```

瀏覽器打開 `http://localhost:8000/hello`，確認看到 `Hello, Laravel!`。

**2. 新增自訂端點**

在 `routes/web.php` 新增路由：

```php
Route::get('/whoami', [HelloController::class, 'whoami']);
```

在 `HelloController` 新增方法：

```php
public function whoami()
{
    return response()->json([
        'name' => '你的名字',
    ]);
}
```

用瀏覽器或 curl 測試：

```bash
curl http://localhost:8000/whoami
# 預期：{"name":"你的名字"}
```

### 延伸挑戰

1. 觀察 `composer.json` 的 require 區塊，寫下每個套件的用途
2. 嘗試用 `php artisan serve --port=9000` 改變監聽埠號

---

## 踩坑提示

| 現象 | 原因 | 解法 |
|------|------|------|
| `artisan serve` 後報 port 衝突 | Port 8000 被其他程式占用 | `lsof -i :8000` 找出占用程式並關閉；或 `php artisan serve --port=8080` |
| 修改 PHP 後瀏覽器沒變化 | 瀏覽器有快取或忘了儲存 | Ctrl+Shift+R 強制重新整理；確認檔案已儲存 |
| Class not found | autoload 未更新 | `composer dump-autoload` |
| APP_KEY 為空 | 忘了執行 key:generate | `php artisan key:generate` |
| 500 錯誤但沒有 log | `.env` 中 `APP_DEBUG=false` | 設為 `APP_DEBUG=true` 以顯示錯誤訊息 |

---

## 驗收標準（DoD）

- [ ] 能用瀏覽器或 `curl http://localhost:8000/whoami` 呼叫自訂端點並看到正確 JSON 回應
- [ ] 能說出 `routes/web.php` 的作用
- [ ] 能說出 `php artisan serve` 做了什麼
