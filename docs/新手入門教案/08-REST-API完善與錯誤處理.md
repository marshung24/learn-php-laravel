# U08｜REST API 完善與錯誤處理

> 能用 Form Request + Exception Handler 統一驗證與錯誤回應，正確使用 HTTP 狀態碼 ｜ 90 min ｜ 前置依賴：U07

---

## 為什麼先教這個？

U07 完成了分層，但 API 還缺少正確的 HTTP 狀態碼和統一的錯誤回應。使用者送了錯誤資料、查了不存在的 ID，應該要拿到清楚的錯誤訊息而非一堆 stack trace。這堂課把 API 做到「生產品質」。

---

## 對應檔案

| 檔案 | 角色 |
|------|------|
| `app/Http/Controllers/Api/BookApiController.php` | REST API 端點 |
| `app/Http/Requests/StoreBookRequest.php` | 新增驗證 |
| `app/Http/Requests/UpdateBookRequest.php` | 更新驗證 |
| `bootstrap/app.php` | Exception Handler 設定 |

---

## 核心觀念

### HTTP 狀態碼語意

| 狀態碼 | 意義 | 使用場景 |
|--------|------|----------|
| 200 OK | 成功 | GET、PUT 成功 |
| 201 Created | 已建立 | POST 新增成功 |
| 204 No Content | 無內容 | DELETE 成功 |
| 400 Bad Request | 請求錯誤 | 請求格式錯誤 |
| 404 Not Found | 找不到 | 資源不存在 |
| 422 Unprocessable Entity | 無法處理 | 驗證失敗 |
| 500 Internal Server Error | 伺服器錯誤 | 程式錯誤 |

### Form Request 驗證

把驗證邏輯獨立於 Controller：

```php
// app/Http/Requests/StoreBookRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 授權檢查，true 表示允許
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'author' => 'required|string|max:100',
            'isbn' => 'required|string|max:20|unique:books,isbn',
            'stock' => 'integer|min:0',
            'publisher' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '書名為必填',
            'author.required' => '作者為必填',
            'isbn.required' => 'ISBN 為必填',
            'isbn.unique' => 'ISBN 已存在',
        ];
    }
}
```

使用方式：

```php
public function store(StoreBookRequest $request)
{
    // 驗證通過才會進入這裡
    $book = $this->bookService->create($request->validated());
    return new BookResource($book);
}
```

### 常用驗證規則

| 規則 | 說明 |
|------|------|
| `required` | 必填 |
| `string` | 字串 |
| `integer` | 整數 |
| `max:100` | 最大長度/值 |
| `min:0` | 最小長度/值 |
| `email` | 電子郵件格式 |
| `unique:table,column` | 唯一值 |
| `exists:table,column` | 必須存在 |
| `nullable` | 允許 null |
| `sometimes` | 有提供時才驗證 |

### `$request->validated()` — 取得驗證通過的資料

```php
// 只取得通過驗證的欄位，過濾掉未定義的欄位
$data = $request->validated();

// 對比 $request->all() 會包含所有輸入
$allData = $request->all();
```

### 驗證失敗的回應

Form Request 驗證失敗時，Laravel 會自動：

- **API 請求**：回傳 422 JSON 錯誤
- **Web 請求**：redirect 回前頁並帶入錯誤訊息

```json
// 422 回應範例
{
    "message": "The title field is required.",
    "errors": {
        "title": ["The title field is required."],
        "isbn": ["The isbn has already been taken."]
    }
}
```

### 正確的 HTTP 狀態碼

```php
class BookApiController extends Controller
{
    public function index()
    {
        $books = $this->bookService->getAll();
        return BookResource::collection($books);
        // 預設 200 OK
    }

    public function store(StoreBookRequest $request)
    {
        $book = $this->bookService->create($request->validated());
        return (new BookResource($book))
            ->response()
            ->setStatusCode(201); // 201 Created
    }

    public function show($id)
    {
        $book = $this->bookService->find($id);

        if (!$book) {
            return response()->json([
                'message' => 'Book not found'
            ], 404); // 404 Not Found
        }

        return new BookResource($book);
    }

    public function destroy($id)
    {
        $book = $this->bookService->findOrFail($id);
        $this->bookService->delete($book);

        return response()->noContent(); // 204 No Content
    }
}
```

### Exception Handler 統一錯誤處理

在 `bootstrap/app.php` 設定全域錯誤處理：

```php
->withExceptions(function (Exceptions $exceptions) {
    // 自訂 ModelNotFoundException 處理
    $exceptions->render(function (ModelNotFoundException $e, Request $request) {
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Resource not found',
            ], 404);
        }
    });

    // 自訂業務例外
    $exceptions->render(function (DuplicateIsbnException $e, Request $request) {
        if ($request->wantsJson()) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 409);
        }
    });
})
```

---

## 動手做

### 必做

**1. 建立 Form Request**

```bash
php artisan make:request StoreBookRequest
php artisan make:request UpdateBookRequest
```

編輯 `StoreBookRequest`：

```php
public function rules(): array
{
    return [
        'title' => 'required|string|max:200',
        'author' => 'required|string|max:100',
        'isbn' => 'required|string|max:20|unique:books,isbn',
        'stock' => 'integer|min:0',
        'publisher' => 'nullable|string|max:100',
    ];
}
```

編輯 `UpdateBookRequest`（更新時 isbn 排除自己）：

```php
public function rules(): array
{
    return [
        'title' => 'sometimes|string|max:200',
        'author' => 'sometimes|string|max:100',
        'isbn' => 'sometimes|string|max:20|unique:books,isbn,' . $this->route('id'),
        'stock' => 'integer|min:0',
        'publisher' => 'nullable|string|max:100',
    ];
}
```

**2. 修改 API Controller 使用 Form Request**

```php
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;

public function store(StoreBookRequest $request)
{
    $book = $this->bookService->create($request->validated());

    return (new BookResource($book))
        ->response()
        ->setStatusCode(201);
}

public function update(UpdateBookRequest $request, $id)
{
    $book = $this->bookService->findOrFail($id);
    $book = $this->bookService->update($book, $request->validated());

    return new BookResource($book);
}
```

**3. 測試驗證錯誤**

故意送缺欄位的 JSON：

```bash
curl -X POST http://localhost:8000/api/books \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{}'

# 預期：422 回應
# {
#     "message": "The title field is required.",
#     "errors": {
#         "title": ["The title field is required."],
#         ...
#     }
# }
```

**4. 測試 404 錯誤**

```bash
curl http://localhost:8000/api/books/99999 \
  -H "Accept: application/json"

# 預期：404 回應
# {"message": "Book not found"}
```

### 延伸挑戰

1. 自訂業務例外 `DuplicateIsbnException`：

```php
// app/Exceptions/DuplicateIsbnException.php
namespace App\Exceptions;

use Exception;

class DuplicateIsbnException extends Exception
{
    public function __construct(string $isbn)
    {
        parent::__construct("ISBN {$isbn} 已存在");
    }
}
```

在 Service 中使用：

```php
public function create(array $data): Book
{
    if (Book::where('isbn', $data['isbn'])->exists()) {
        throw new DuplicateIsbnException($data['isbn']);
    }

    return Book::create($data);
}
```

在 `bootstrap/app.php` 註冊處理：

```php
$exceptions->render(function (DuplicateIsbnException $e, Request $request) {
    if ($request->wantsJson()) {
        return response()->json([
            'message' => $e->getMessage(),
        ], 409);
    }
});
```

---

## 踩坑提示

| 現象 | 原因 | 解法 |
|------|------|------|
| Form Request 驗證沒生效 | Controller 方法參數沒有 type hint | 確認參數類型是 `StoreBookRequest` |
| 驗證錯誤回傳 HTML 而非 JSON | 請求沒有 `Accept: application/json` header | 確認請求 header 正確；或檢查 `wantsJson()` |
| `authorize()` 回傳 false 導致 403 | 授權檢查失敗 | 確認 `authorize()` 回傳 `true` |
| 更新時 unique 檢查失敗 | 沒有排除自己 | `unique:books,isbn,' . $this->route('id')` |

---

## 驗收標準（DoD）

- [ ] 能用 Swagger UI 完成完整 CRUD 操作
- [ ] POST 成功回傳 201 Created
- [ ] DELETE 成功回傳 204 No Content
- [ ] 驗證失敗回傳 422 與統一的錯誤格式
- [ ] 查詢不存在的 ID 回傳 404
- [ ] 能說出 Form Request 的優點
