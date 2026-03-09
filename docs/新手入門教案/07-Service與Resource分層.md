# U07｜Service 與 Resource 分層

> 能從 Controller 抽出 Service 層，引入 Resource 做輸出轉換 ｜ 90 min ｜ 前置依賴：U06

---

## 為什麼先教這個？

U06 完成了 CRUD，但 Controller 直接操作 Model——所有邏輯擠在一起，改一個地方可能影響全部。這堂課把程式碼「整理乾淨」：抽出 Service 層集中業務邏輯，引入 API Resource 分離輸出格式。**重構前後功能完全一樣，但程式碼更好維護、更好測試。**

---

## 對應檔案

| 檔案 | 角色 |
|------|------|
| `app/Services/BookService.php` | Service 層（業務邏輯） |
| `app/Http/Resources/BookResource.php` | API Resource（輸出格式轉換） |
| `app/Http/Resources/BookCollection.php` | 集合 Resource（選用） |

---

## 核心觀念

### 為什麼需要 Service 層？

**重構前**（Controller 直接操作 Model）：

```php
class BookController extends Controller
{
    public function store(Request $request)
    {
        // 驗證邏輯
        $validated = $request->validate([...]);

        // 業務邏輯（檢查 ISBN 是否重複）
        if (Book::where('isbn', $validated['isbn'])->exists()) {
            return back()->withErrors(['isbn' => 'ISBN 已存在']);
        }

        // 資料存取
        $book = Book::create($validated);

        // 其他業務邏輯（發送通知等）
        // ...

        return redirect()->route('books.index');
    }
}
```

問題：Controller 做太多事，難以測試、難以重用。

**重構後**（分離 Service 層）：

```php
// Controller 只負責 HTTP 協議
class BookController extends Controller
{
    public function __construct(
        private BookService $bookService
    ) {}

    public function store(BookRequest $request)
    {
        $this->bookService->create($request->validated());
        return redirect()->route('books.index');
    }
}

// Service 負責業務邏輯
class BookService
{
    public function create(array $data): Book
    {
        // 業務邏輯集中在這裡
        return Book::create($data);
    }
}
```

### 單一職責原則

| 層級 | 職責 |
|------|------|
| Controller | HTTP 協議（接收請求、回傳回應） |
| Service | 業務邏輯（驗證規則、流程控制） |
| Model | 資料存取（CRUD、關聯） |
| Resource | 輸出轉換（格式化回應） |

### 依賴注入

Laravel 自動解析建構子的依賴：

```php
class BookController extends Controller
{
    public function __construct(
        private BookService $bookService
    ) {}

    // $bookService 自動注入，不需手動 new
}
```

### API Resource

把 Model 轉換為 JSON 回應格式，控制輸出的欄位與格式：

```php
// app/Http/Resources/BookResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'stock' => $this->stock,
            'inStock' => $this->stock > 0,
            'createdAt' => $this->created_at->toIso8601String(),
        ];
    }
}
```

使用方式：

```php
// 單筆
return new BookResource($book);

// 多筆
return BookResource::collection($books);
```

### Resource 的優點

1. **統一輸出格式**：所有 API 回傳一致的 JSON 結構
2. **隱藏內部欄位**：不暴露 `updated_at`、敏感欄位等
3. **格式轉換**：snake_case → camelCase、日期格式化
4. **計算欄位**：如 `inStock` 由 `stock > 0` 計算得出

### PRG 模式（Post-Redirect-Get）

MVC 表單送出後用 redirect 避免使用者按 F5 重複提交：

```php
public function store(BookRequest $request)
{
    $this->bookService->create($request->validated());

    // PRG：POST 後 redirect，避免重複提交
    return redirect()
        ->route('books.index')
        ->with('success', '書籍新增成功！');
}
```

---

## 動手做

### 必做

**1. 建立 BookService**

建立 `app/Services/BookService.php`：

```php
<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Collection;

class BookService
{
    public function getAll(): Collection
    {
        return Book::all();
    }

    public function find(int $id): ?Book
    {
        return Book::find($id);
    }

    public function findOrFail(int $id): Book
    {
        return Book::findOrFail($id);
    }

    public function create(array $data): Book
    {
        return Book::create($data);
    }

    public function update(Book $book, array $data): Book
    {
        $book->update($data);
        return $book->fresh();
    }

    public function delete(Book $book): bool
    {
        return $book->delete();
    }
}
```

**2. 修改 Controller 使用 Service**

```php
// app/Http/Controllers/BookController.php
namespace App\Http\Controllers;

use App\Services\BookService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct(
        private BookService $bookService
    ) {}

    public function index()
    {
        $books = $this->bookService->getAll();
        return view('books.index', compact('books'));
    }

    public function show($id)
    {
        $book = $this->bookService->findOrFail($id);
        return view('books.show', compact('book'));
    }

    public function store(Request $request)
    {
        $this->bookService->create($request->all());
        return redirect()->route('books.index')
            ->with('success', '書籍新增成功！');
    }
}
```

**3. 建立 BookResource**

```bash
php artisan make:resource BookResource
```

編輯 `app/Http/Resources/BookResource.php`：

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'stock' => $this->stock,
            'inStock' => $this->stock > 0,
            'publisher' => $this->publisher,
            'createdAt' => $this->created_at?->toIso8601String(),
            'updatedAt' => $this->updated_at?->toIso8601String(),
        ];
    }
}
```

**4. 修改 API Controller 使用 Service 與 Resource**

```php
// app/Http/Controllers/Api/BookApiController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Services\BookService;
use Illuminate\Http\Request;

class BookApiController extends Controller
{
    public function __construct(
        private BookService $bookService
    ) {}

    public function index()
    {
        $books = $this->bookService->getAll();
        return BookResource::collection($books);
    }

    public function show($id)
    {
        $book = $this->bookService->find($id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        return new BookResource($book);
    }

    public function store(Request $request)
    {
        $book = $this->bookService->create($request->all());
        return new BookResource($book);
    }

    public function destroy($id)
    {
        $book = $this->bookService->findOrFail($id);
        $this->bookService->delete($book);

        return response()->json(null, 204);
    }
}
```

### 延伸挑戰

1. 畫出 Controller → Service → Model 的呼叫流程圖
2. 在 Service 加入 `findByIsbn($isbn)` 方法
3. 建立 `BookCollection` Resource 自訂集合格式

---

## 踩坑提示

| 現象 | 原因 | 解法 |
|------|------|------|
| Service 無法注入 | 沒有 type hint 或 namespace 錯誤 | 確認建構子參數有正確的 type hint：`private BookService $bookService` |
| Resource 輸出欄位名稱不對 | `toArray()` 方法的 key 名稱錯誤 | 檢查 Resource 的 `toArray()` 方法 |
| `$this->title` 報錯 | Resource 中忘了繼承 JsonResource | 確認 `extends JsonResource` |
| 資料沒有 wrap | Laravel 預設會用 `data` 包裝 | 可在 `AppServiceProvider` 設定 `JsonResource::withoutWrapping()` |

---

## 驗收標準（DoD）

- [ ] 能說明 Service 層存在的理由
- [ ] 能解釋 Model 與 Resource 的職責差異
- [ ] 頁面和 API 行為與重構前完全一致
- [ ] 能說出依賴注入的優點
