# U02｜假畫面 — Blade 初體驗

> 能用 Blade 做出書籍清單頁面（假資料） ｜ 60 min ｜ 前置依賴：U01

---

## 為什麼先教這個？

U01 讓程式跑起來了，但只有文字回應。這堂課讓學員第一次「看到畫面」——用 Blade 做出一個有書籍清單的 HTML 頁面。資料先用 Controller 中的假資料（hardcoded），不需要 DB，**重點是讓學員體驗「改了模板，刷新就看到結果」的即時回饋感**。

---

## 對應檔案

| 檔案 | 角色 |
|------|------|
| `app/Http/Controllers/BookController.php` | MVC 端點（回傳 HTML 頁面） |
| `resources/views/books/index.blade.php` | 書籍清單頁 |
| `resources/views/books/show.blade.php` | 書籍詳情頁 |
| `resources/views/layouts/app.blade.php` | 共用 Layout |

---

## 核心觀念

### 伺服器端渲染（SSR）

Blade 在 server 端組好完整 HTML 再送出，不需要前端框架。瀏覽器收到的是完整的 HTML 頁面。

### `return view()` — 回傳視圖

```php
// BookController.php
public function index()
{
    $books = collect([
        ['id' => 1, 'title' => 'Laravel 入門', 'author' => '王大明', 'stock' => 5],
        ['id' => 2, 'title' => 'PHP 實戰', 'author' => '李小華', 'stock' => 0],
        ['id' => 3, 'title' => 'Redis 快取', 'author' => '張三', 'stock' => 10],
    ]);

    return view('books.index', compact('books'));
}
```

`view('books.index')` 代表渲染 `resources/views/books/index.blade.php`。

### 傳遞資料給視圖

```php
// 方式一：compact()
return view('books.index', compact('books'));

// 方式二：with()
return view('books.index')->with('books', $books);

// 方式三：陣列
return view('books.index', ['books' => $books]);
```

### Blade 關鍵語法

**迴圈渲染 `@foreach`**

```blade
@foreach ($books as $book)
    <tr>
        <td>{{ $book['title'] }}</td>
        <td>{{ $book['author'] }}</td>
    </tr>
@endforeach
```

**輸出文字 `{{ }}`**

```blade
{{ $book['title'] }}  <!-- 自動 XSS 防護 -->
{!! $html !!}         <!-- 不轉義（慎用） -->
```

**條件判斷 `@if`**

```blade
@if ($book['stock'] > 0)
    <span class="text-green-500">有庫存</span>
@else
    <span class="text-red-500">缺貨</span>
@endif
```

**Layout 繼承**

```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', '書籍管理系統')</title>
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>

{{-- resources/views/books/index.blade.php --}}
@extends('layouts.app')

@section('title', '書籍清單')

@section('content')
    <h1>書籍清單</h1>
    {{-- 內容 --}}
@endsection
```

---

## 動手做

### 必做

**1. 觀察 Controller 如何傳遞資料**

閱讀 `BookController@index`，理解 `compact()` 如何把變數傳給視圖。

**2. 建立書籍清單頁面**

在 `BookController` 中建立假資料：

```php
public function index()
{
    $books = collect([
        ['id' => 1, 'title' => 'Laravel 入門', 'author' => '王大明', 'stock' => 5],
        ['id' => 2, 'title' => 'PHP 實戰', 'author' => '李小華', 'stock' => 0],
        ['id' => 3, 'title' => 'Redis 快取', 'author' => '張三', 'stock' => 10],
    ]);

    return view('books.index', compact('books'));
}
```

建立 `resources/views/books/index.blade.php`：

```blade
@extends('layouts.app')

@section('content')
<h1>書籍清單</h1>
<table>
    <thead>
        <tr>
            <th>書名</th>
            <th>作者</th>
            <th>庫存</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($books as $book)
        <tr>
            <td>
                <a href="{{ route('books.show', $book['id']) }}">
                    {{ $book['title'] }}
                </a>
            </td>
            <td>{{ $book['author'] }}</td>
            <td>{{ $book['stock'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
```

瀏覽 `http://localhost:8000/books` 看到表格。

**3. 建立詳情頁**

在 `BookController` 新增：

```php
public function show($id)
{
    $book = [
        'id' => $id,
        'title' => 'Laravel 入門',
        'author' => '王大明',
        'stock' => 5,
        'isbn' => '978-1234567890',
    ];

    return view('books.show', compact('book'));
}
```

建立 `resources/views/books/show.blade.php`：

```blade
@extends('layouts.app')

@section('content')
<h1>{{ $book['title'] }}</h1>
<p>作者：{{ $book['author'] }}</p>
<p>ISBN：{{ $book['isbn'] }}</p>
<p>庫存：{{ $book['stock'] }}</p>
<a href="{{ route('books.index') }}">返回清單</a>
@endsection
```

### 延伸挑戰

1. 修改 `index.blade.php`，在表格加一欄「狀態」，當庫存 = 0 時顯示紅字「缺貨」

```blade
<td>
    @if ($book['stock'] > 0)
        <span style="color: green;">有庫存</span>
    @else
        <span style="color: red;">缺貨</span>
    @endif
</td>
```

---

## 踩坑提示

| 現象 | 原因 | 解法 |
|------|------|------|
| View not found | Controller 回傳的 view 名稱錯誤（如 `book.list` 而非 `books.index`） | 確認 `views/` 下的資料夾名稱與 view 名稱完全一致 |
| 頁面顯示 `{{ $books }}` 原始文字 | 忘了用 `.blade.php` 副檔名 | Blade 檔案必須是 `.blade.php` 結尾 |
| Layout 沒套用 | 忘了 `@extends('layouts.app')` | 在 Blade 檔案開頭加上 extends |
| `$book['title']` 報錯 | 陣列取值語法錯誤 | 確認變數是陣列還是物件；陣列用 `[]`，物件用 `->` |
| 路由不存在 | 忘了在 `routes/web.php` 定義路由 | 加上 `Route::get('/books', [BookController::class, 'index'])->name('books.index')` |

---

## 驗收標準（DoD）

- [ ] 能在瀏覽器看到一個有書籍清單的 HTML 頁面（即使資料是假的）
- [ ] 能說出 `@foreach` 的用途
- [ ] 能說出 `{{ }}` 的用途與 XSS 防護原理
- [ ] 能說出 `@extends` 與 `@section` 的關係
