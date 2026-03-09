# U03｜API 回傳假資料

> 能用 Controller 回傳固定 JSON 並在 Swagger 測試 ｜ 60 min ｜ 前置依賴：U01

---

## 為什麼先教這個？

U02 做了給人看的頁面，這堂課做給機器用的 API。同樣用假資料，讓學員先理解「Controller 怎麼回傳 JSON」。有了 API，就能用 Swagger UI 互動式測試，也為後面串接真實 DB 打下基礎。

---

## 對應檔案

| 檔案 | 角色 |
|------|------|
| `app/Http/Controllers/Api/BookApiController.php` | REST API 端點（回傳 JSON） |
| `routes/api.php` | API 路由定義 |

---

## 核心觀念

### `return response()->json()` — 回傳 JSON

```php
// app/Http/Controllers/Api/BookApiController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class BookApiController extends Controller
{
    public function index()
    {
        $books = [
            ['id' => 1, 'title' => 'Laravel 入門', 'author' => '王大明'],
            ['id' => 2, 'title' => 'PHP 實戰', 'author' => '李小華'],
            ['id' => 3, 'title' => 'Redis 快取', 'author' => '張三'],
        ];

        return response()->json($books);
    }
}
```

方法回傳值會自動序列化為 JSON，與 U02 的 `view()` 不同。

### `routes/api.php` — API 路由

```php
// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookApiController;

Route::get('/books', [BookApiController::class, 'index']);
Route::get('/books/{id}', [BookApiController::class, 'show']);
Route::post('/books', [BookApiController::class, 'store']);
Route::put('/books/{id}', [BookApiController::class, 'update']);
Route::delete('/books/{id}', [BookApiController::class, 'destroy']);
```

定義在 `api.php` 的路由會自動加上 `/api` 前綴，所以 `GET /books` 實際路徑是 `GET /api/books`。

### RESTful 動詞對應

| HTTP Method | 用途 | 範例路徑 |
|-------------|------|----------|
| GET | 查詢 | `/api/books`、`/api/books/1` |
| POST | 新增 | `/api/books` |
| PUT/PATCH | 更新 | `/api/books/1` |
| DELETE | 刪除 | `/api/books/1` |

### Swagger UI

安裝 L5-Swagger 套件後，可在 `/api/documentation` 訪問互動式 API 文件：

```bash
composer require darkaonline/l5-swagger
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
php artisan l5-swagger:generate
```

### MVC 頁面 vs REST API

| 面向 | MVC 頁面 | REST API |
|------|---------|----------|
| 回傳格式 | HTML | JSON |
| 消費者 | 瀏覽器/人類 | 程式/機器 |
| Controller 類型 | 一般 Controller | API Controller |
| 路由檔案 | `routes/web.php` | `routes/api.php` |

---

## 動手做

### 必做

**1. 建立 API Controller**

建立 `app/Http/Controllers/Api/BookApiController.php`：

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookApiController extends Controller
{
    private array $books = [
        ['id' => 1, 'title' => 'Laravel 入門', 'author' => '王大明', 'stock' => 5],
        ['id' => 2, 'title' => 'PHP 實戰', 'author' => '李小華', 'stock' => 0],
        ['id' => 3, 'title' => 'Redis 快取', 'author' => '張三', 'stock' => 10],
    ];

    public function index()
    {
        return response()->json($this->books);
    }

    public function show($id)
    {
        $book = collect($this->books)->firstWhere('id', (int) $id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        return response()->json($book);
    }
}
```

**2. 定義 API 路由**

在 `routes/api.php` 加入：

```php
use App\Http\Controllers\Api\BookApiController;

Route::get('/books', [BookApiController::class, 'index']);
Route::get('/books/{id}', [BookApiController::class, 'show']);
```

**3. 測試 API**

```bash
curl http://localhost:8000/api/books
# 預期：JSON 陣列，3 本書

curl http://localhost:8000/api/books/1
# 預期：單筆書籍 JSON
```

**4. 使用 Swagger UI 測試**

訪問 `http://localhost:8000/api/documentation`，在 UI 中測試剛才建立的 API。

### 延伸挑戰

1. 加入 `POST /api/books` 端點：

```php
public function store(Request $request)
{
    // 印出收到的資料（還沒有 DB，先確認能收到請求）
    dd($request->all());
}
```

在 `routes/api.php` 加入：

```php
Route::post('/books', [BookApiController::class, 'store']);
```

用 curl 測試：

```bash
curl -X POST http://localhost:8000/api/books \
  -H "Content-Type: application/json" \
  -d '{"title": "新書", "author": "新作者"}'
```

---

## 踩坑提示

| 現象 | 原因 | 解法 |
|------|------|------|
| API 回 404 | 路由定義在 `web.php` 而非 `api.php` | API 路由應放在 `routes/api.php` |
| Swagger UI 看不到新加的 API | 沒有加上 OpenAPI 註解或沒重新產生文件 | 加上 `@OA\Get` 等註解並執行 `php artisan l5-swagger:generate` |
| 回傳的 JSON 格式不對 | 使用了 `return $books` 而非 `response()->json()` | Laravel 會自動轉換陣列，但明確使用 `response()->json()` 更清楚 |
| POST 請求收不到資料 | 沒有 `Content-Type: application/json` header | 確認請求 header 正確 |
| CSRF token 錯誤 | POST 請求走了 web middleware | 確認路由在 `api.php` 中，API 路由預設不檢查 CSRF |

---

## 驗收標準（DoD）

- [ ] 能用 `curl http://localhost:8000/api/books` 看到 JSON 回應
- [ ] 能用 `curl http://localhost:8000/api/books/1` 看到單筆書籍
- [ ] 能用 Swagger UI 測試 API
- [ ] 能說出 MVC 頁面（`view()`）與 REST API（`response()->json()`）的差異
