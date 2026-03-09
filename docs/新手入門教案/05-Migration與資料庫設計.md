# U05｜Migration 與資料庫設計

> 能撰寫 migration 自動建表與 seed data ｜ 90 min ｜ 前置依賴：U04

---

## 為什麼先教這個？

U02-U03 用的是假資料，接下來要換成真的。第一步是「設計資料庫結構」——用 Laravel Migration 管理 schema 版本，確保多人協作時每個人的 DB 長一樣。

---

## 對應檔案

| 檔案 | 角色 |
|------|------|
| `database/migrations/xxxx_create_books_table.php` | 建表 Migration |
| `database/seeders/BookSeeder.php` | 塞入範例資料 |
| `database/seeders/DatabaseSeeder.php` | Seeder 入口 |

---

## 核心觀念

### Migration 檔案命名

```
2024_01_15_100000_create_books_table.php
└─────────────────┬─────────────────────┘
     時間戳（確保執行順序）    描述
```

Migration 依照時間戳順序執行，確保表之間的依賴關係正確（如外鍵）。

### Migration 結構

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 執行 migration（建立/修改）
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();                              // BIGINT UNSIGNED AUTO_INCREMENT
            $table->string('title', 200);              // VARCHAR(200)
            $table->string('author', 100);             // VARCHAR(100)
            $table->string('isbn', 20)->unique();      // VARCHAR(20) UNIQUE
            $table->integer('stock')->default(0);      // INT DEFAULT 0
            $table->timestamps();                      // created_at, updated_at
        });
    }

    /**
     * 回滾 migration（還原）
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
```

### Schema Builder 常用方法

| 方法 | 說明 | MySQL 對應 |
|------|------|-----------|
| `$table->id()` | 主鍵 | `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY` |
| `$table->string('name', 100)` | 字串 | `VARCHAR(100)` |
| `$table->text('content')` | 長文字 | `TEXT` |
| `$table->integer('count')` | 整數 | `INT` |
| `$table->bigInteger('amount')` | 大整數 | `BIGINT` |
| `$table->decimal('price', 8, 2)` | 精確小數 | `DECIMAL(8,2)` |
| `$table->boolean('active')` | 布林 | `TINYINT(1)` |
| `$table->date('birth_date')` | 日期 | `DATE` |
| `$table->datetime('published_at')` | 日期時間 | `DATETIME` |
| `$table->timestamps()` | 時間戳 | `created_at`, `updated_at` |
| `$table->softDeletes()` | 軟刪除 | `deleted_at` |

### 欄位修飾符

```php
$table->string('email')->unique();          // 唯一索引
$table->string('name')->nullable();         // 允許 NULL
$table->integer('order')->default(0);       // 預設值
$table->string('note')->after('name');      // 指定位置
$table->string('old_col')->comment('說明'); // 欄位註解
```

### Migration 指令

```bash
# 執行所有未執行的 migration
php artisan migrate

# 回滾上一次 migration
php artisan migrate:rollback

# 回滾所有並重新執行
php artisan migrate:fresh

# 查看 migration 狀態
php artisan migrate:status

# 建立新 migration
php artisan make:migration create_books_table
php artisan make:migration add_publisher_to_books_table
```

### Seeder 塞入測試資料

```php
// database/seeders/BookSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        Book::create([
            'title' => 'Laravel 入門',
            'author' => '王大明',
            'isbn' => '978-1234567890',
            'stock' => 10,
        ]);

        Book::create([
            'title' => 'PHP 實戰',
            'author' => '李小華',
            'isbn' => '978-0987654321',
            'stock' => 5,
        ]);
    }
}

// database/seeders/DatabaseSeeder.php
public function run(): void
{
    $this->call([
        BookSeeder::class,
    ]);
}
```

### Seeder 指令

```bash
# 執行所有 seeder
php artisan db:seed

# 執行特定 seeder
php artisan db:seed --class=BookSeeder

# migrate 並 seed
php artisan migrate --seed
```

---

## 動手做

### 必做

**1. 閱讀現有 Migration**

閱讀 `database/migrations/xxxx_create_books_table.php`，說出每個欄位的設計理由：

- 為什麼用 `id()` 而非自定義主鍵？
- 為什麼 `isbn` 加 `unique()`？
- `timestamps()` 會建立哪些欄位？

**2. 執行 Migration**

```bash
# 確認 DB 連線正常
php artisan migrate:status

# 執行 migration
php artisan migrate
# 預期輸出：Running migrations... 完成訊息

# 確認表已建立
php artisan tinker
>>> Schema::hasTable('books')
=> true
```

**3. 新增 Migration 加欄位**

建立新 migration 為 books 表加 `publisher` 欄位：

```bash
php artisan make:migration add_publisher_to_books_table --table=books
```

編輯產生的檔案：

```php
public function up(): void
{
    Schema::table('books', function (Blueprint $table) {
        $table->string('publisher', 100)->nullable()->after('author');
    });
}

public function down(): void
{
    Schema::table('books', function (Blueprint $table) {
        $table->dropColumn('publisher');
    });
}
```

執行並驗證：

```bash
php artisan migrate
php artisan migrate:status
```

**4. 執行 Seeder**

```bash
php artisan db:seed

# 驗證資料
php artisan tinker
>>> App\Models\Book::count()
=> 2
```

### 延伸挑戰

1. 思考題：如果 migration 部署後發現 `publisher` 欄位長度不夠（需要 200），該怎麼修正？

提示：建立新的 migration 修改欄位：

```php
public function up(): void
{
    Schema::table('books', function (Blueprint $table) {
        $table->string('publisher', 200)->nullable()->change();
    });
}
```

注意：需要安裝 `doctrine/dbal` 套件才能修改欄位：

```bash
composer require doctrine/dbal
```

---

## 踩坑提示

| 現象 | 原因 | 解法 |
|------|------|------|
| Migration 執行順序錯誤 | 時間戳不對 | 確認檔名的時間戳正確；或刪除後重新建立 |
| 外鍵約束失敗 | 參照的表尚未建立 | 確保被參照的表先建立（migration 時間戳較早） |
| `down()` 執行失敗 | `down()` 方法沒寫或寫錯 | 確保 `down()` 能正確還原 `up()` 的變更 |
| Seeder 執行失敗 | 資料已存在（違反唯一約束） | 先清空表或使用 `firstOrCreate()` |
| 表不存在 | 忘了執行 migrate | `php artisan migrate` |

---

## 驗收標準（DoD）

- [ ] 能撰寫一支新的 migration 並成功執行
- [ ] 能用 `php artisan migrate:status` 確認 migration 狀態
- [ ] 能用 `php artisan migrate:rollback` 回滾 migration
- [ ] 能執行 seeder 塞入測試資料
- [ ] 能說出 `up()` 與 `down()` 的作用
