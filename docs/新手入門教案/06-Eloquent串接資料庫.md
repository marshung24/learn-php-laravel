# U06｜Eloquent 串接資料庫

> 能用 Eloquent 完成真實 CRUD，頁面與 API 顯示 DB 真實資料 ｜ 120 min ｜ 前置依賴：U02, U03, U05

---

## 為什麼先教這個？

U05 設計好了資料表，這堂課要把假資料換成真的——用 Eloquent ORM 串接 MySQL，讓 Controller 直接從 DB 讀寫資料。**完成後學員會第一次看到「頁面上的資料來自真實資料庫」的完整 CRUD 循環。**

此刻先不抽 Service 層，Controller 直接操作 Model——保持最短路徑讓 CRUD 跑起來。

---

## 對應檔案

| 檔案 | 角色 |
|------|------|
| `app/Models/Book.php` | Eloquent Model（對應 books 表） |
| `app/Http/Controllers/BookController.php` | MVC 端點（改用 Eloquent） |
| `app/Http/Controllers/Api/BookApiController.php` | API 端點（改用 Eloquent） |

---

## 核心觀念

### Active Record 模式

Eloquent 採用 Active Record 模式——一個 Model 物件對應一筆資料庫記錄。Model 本身負責資料的讀取與寫入。

```php
// 一個 Book 物件 = 一筆 books 表的記錄
$book = Book::find(1);
$book->title = '新書名';
$book->save();
```

### Model 定義

```php
// app/Models/Book.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    // 對應的資料表（預設為類名複數：books）
    protected $table = 'books';

    // 可批量賦值的欄位（Mass Assignment 防護）
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'stock',
        'publisher',
    ];

    // 隱藏欄位（toArray/toJson 時不輸出）
    protected $hidden = [];

    // 型別轉換
    protected $casts = [
        'stock' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
```

### `$fillable` 與 Mass Assignment

```php
// 允許批量賦值
protected $fillable = ['title', 'author', 'isbn', 'stock'];

// 這樣就能用 create() 或 update() 批量賦值
Book::create([
    'title' => 'Laravel 入門',
    'author' => '王大明',
    'isbn' => '978-1234567890',
    'stock' => 10,
]);
```

如果欄位不在 `$fillable` 中，會拋出 `MassAssignmentException`。

### 基本 CRUD 操作

**Create（新增）**

```php
// 方式一：create() 批量建立
$book = Book::create([
    'title' => 'Laravel 入門',
    'author' => '王大明',
    'isbn' => '978-1234567890',
    'stock' => 10,
]);

// 方式二：new + save()
$book = new Book();
$book->title = 'Laravel 入門';
$book->save();
```

**Read（查詢）**

```php
// 查詢全部
$books = Book::all();

// 依主鍵查詢
$book = Book::find(1);

// 依主鍵查詢，找不到拋 404
$book = Book::findOrFail(1);

// 條件查詢
$books = Book::where('author', '王大明')->get();

// 單筆查詢
$book = Book::where('isbn', '978-1234567890')->first();
```

**Update（更新）**

```php
// 方式一：找到後更新
$book = Book::find(1);
$book->title = '新書名';
$book->save();

// 方式二：update() 批量更新
$book = Book::find(1);
$book->update(['title' => '新書名']);

// 方式三：條件更新
Book::where('author', '王大明')->update(['stock' => 0]);
```

**Delete（刪除）**

```php
// 方式一：找到後刪除
$book = Book::find(1);
$book->delete();

// 方式二：依主鍵刪除
Book::destroy(1);

// 方式三：條件刪除
Book::where('stock', 0)->delete();
```

### Query Builder 進階查詢

```php
// 條件查詢
$books = Book::where('stock', '>', 0)
    ->where('author', 'like', '%王%')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

// 選擇欄位
$books = Book::select('id', 'title', 'author')->get();

// 聚合函數
$count = Book::count();
$avgStock = Book::avg('stock');
$maxStock = Book::max('stock');
```

---

## 動手做

### 必做

**1. 閱讀 Book Model**

閱讀 `app/Models/Book.php`，理解：
- `$fillable` 定義了哪些欄位
- 為什麼需要 Mass Assignment 防護

**2. 修改 BookController 使用 Eloquent**

將 U02 的假資料替換為 Eloquent 查詢：

```php
// app/Http/Controllers/BookController.php
use App\Models\Book;

public function index()
{
    $books = Book::all();
    return view('books.index', compact('books'));
}

public function show($id)
{
    $book = Book::findOrFail($id);
    return view('books.show', compact('book'));
}
```

更新 Blade 模板使用物件語法：

```blade
@foreach ($books as $book)
<tr>
    <td>{{ $book->title }}</td>
    <td>{{ $book->author }}</td>
    <td>{{ $book->stock }}</td>
</tr>
@endforeach
```

**3. 實作新增功能**

建立 create 表單頁面 `resources/views/books/create.blade.php`：

```blade
@extends('layouts.app')

@section('content')
<h1>新增書籍</h1>
<form action="{{ route('books.store') }}" method="POST">
    @csrf
    <div>
        <label>書名</label>
        <input type="text" name="title" required>
    </div>
    <div>
        <label>作者</label>
        <input type="text" name="author" required>
    </div>
    <div>
        <label>ISBN</label>
        <input type="text" name="isbn" required>
    </div>
    <div>
        <label>庫存</label>
        <input type="number" name="stock" value="0">
    </div>
    <button type="submit">新增</button>
</form>
@endsection
```

Controller 處理新增：

```php
public function create()
{
    return view('books.create');
}

public function store(Request $request)
{
    Book::create($request->only(['title', 'author', 'isbn', 'stock']));

    return redirect()->route('books.index')
        ->with('success', '書籍新增成功！');
}
```

**4. 修改 API Controller**

```php
// app/Http/Controllers/Api/BookApiController.php
use App\Models\Book;

public function index()
{
    return response()->json(Book::all());
}

public function show($id)
{
    $book = Book::find($id);

    if (!$book) {
        return response()->json(['message' => 'Book not found'], 404);
    }

    return response()->json($book);
}

public function store(Request $request)
{
    $book = Book::create($request->all());
    return response()->json($book, 201);
}

public function destroy($id)
{
    $book = Book::findOrFail($id);
    $book->delete();

    return response()->json(null, 204);
}
```

### 延伸挑戰

1. 新增 `scopeByAuthor` Query Scope：

```php
// Book.php
public function scopeByAuthor($query, $author)
{
    return $query->where('author', $author);
}

// 使用方式
$books = Book::byAuthor('王大明')->get();
```

2. 實作編輯與刪除功能

---

## 踩坑提示

| 現象 | 原因 | 解法 |
|------|------|------|
| `MassAssignmentException` | 欄位不在 `$fillable` 中 | 將欄位加入 Model 的 `$fillable` 陣列 |
| 查詢結果是空的 | 忘了執行 seeder | `php artisan db:seed` |
| Model 找不到 | namespace 錯誤 | 確認是 `App\Models\Book` |
| `$book->title` 報錯 | Blade 中把物件當陣列 | 物件用 `->` 取值，陣列用 `[]` |
| CSRF token 錯誤 | 表單忘了加 `@csrf` | 在 `<form>` 內加上 `@csrf` |

---

## 驗收標準（DoD）

- [ ] 能在瀏覽器看到來自 DB 的書籍清單（不再是假資料）
- [ ] 能透過表單新增一筆書籍記錄
- [ ] 能用 API 查詢真實 DB 資料
- [ ] 能說出 `$fillable` 的用途
- [ ] 能說出 `find()` 與 `findOrFail()` 的差異
