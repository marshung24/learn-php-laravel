# U09｜Redis 快取整合

> 能用 redis-cli 驗證快取行為並說明 Cache-Aside 流程 ｜ 90 min ｜ 前置依賴：U07

---

## 為什麼先教這個？

資料庫查詢是 Web 應用最慢的環節。把「熱資料」放在 Redis 記憶體中，回應速度可以從毫秒降到微秒等級。這堂課在 U07 建立的 Service 層上加入快取邏輯。

---

## 對應檔案

| 檔案 | 角色 |
|------|------|
| `app/Services/BookService.php` | Cache-Aside 讀寫邏輯 |
| `config/cache.php` | 快取設定 |
| `config/database.php` | Redis 連線設定 |

---

## 核心觀念

### Cache-Aside 模式

**讀取流程**：
1. 先查快取
2. 快取有（hit）→ 直接回傳
3. 快取沒有（miss）→ 查 DB → 寫入快取 → 回傳

**寫入/刪除流程**：
1. 操作 DB
2. 清除相關快取（避免資料不一致）

```
┌────────────┐     hit     ┌────────────┐
│  Request   │ ──────────> │   Redis    │
└────────────┘             └────────────┘
      │                          │
      │ miss                     │
      ▼                          │
┌────────────┐                   │
│   MySQL    │ <─────────────────┘
└────────────┘      write back
```

### Laravel Cache Facade

```php
use Illuminate\Support\Facades\Cache;

// 取得快取
$value = Cache::get('key');
$value = Cache::get('key', 'default');

// 設定快取
Cache::put('key', $value, $seconds);
Cache::put('key', $value, now()->addMinutes(10));

// 永久快取
Cache::forever('key', $value);

// 刪除快取
Cache::forget('key');

// 清除所有快取
Cache::flush();
```

### `Cache::remember()` — 快取便捷方法

自動處理 hit/miss 邏輯：

```php
$book = Cache::remember("books:{$id}", 600, function () use ($id) {
    return Book::find($id);
});

// 等同於：
$book = Cache::get("books:{$id}");
if (!$book) {
    $book = Book::find($id);
    Cache::put("books:{$id}", $book, 600);
}
```

### `Cache::tags()` — 標籤群組管理

用標籤群組管理相關快取，方便批次清除：

```php
// 寫入時加標籤
Cache::tags(['books'])->put("books:{$id}", $book, 600);

// 清除標籤下所有快取
Cache::tags(['books'])->flush();
```

**注意**：`tags()` 只支援支援標籤的快取驅動（Redis、Memcached），不支援 file、database driver。

### 快取 Key 設計

```php
// 單筆資料
"books:{id}"           // books:1, books:2

// 列表
"books:all"
"books:page:{page}"    // books:page:1, books:page:2

// 條件查詢
"books:author:{name}"  // books:author:王大明
```

### TTL（Time To Live）設定

```php
// 秒數
Cache::put('key', $value, 600);  // 10 分鐘

// Carbon 物件
Cache::put('key', $value, now()->addMinutes(10));
Cache::put('key', $value, now()->addHours(1));
Cache::put('key', $value, now()->addDay());
```

---

## 動手做

### 必做

**1. 修改 BookService 加入快取**

```php
// app/Services/BookService.php
namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class BookService
{
    private const CACHE_TTL = 600; // 10 分鐘

    public function getAll(): Collection
    {
        return Cache::remember('books:all', self::CACHE_TTL, function () {
            return Book::all();
        });
    }

    public function find(int $id): ?Book
    {
        return Cache::remember("books:{$id}", self::CACHE_TTL, function () use ($id) {
            return Book::find($id);
        });
    }

    public function findOrFail(int $id): Book
    {
        return Cache::remember("books:{$id}", self::CACHE_TTL, function () use ($id) {
            return Book::findOrFail($id);
        });
    }

    public function create(array $data): Book
    {
        $book = Book::create($data);

        // 清除列表快取
        Cache::forget('books:all');

        return $book;
    }

    public function update(Book $book, array $data): Book
    {
        $book->update($data);

        // 清除相關快取
        Cache::forget("books:{$book->id}");
        Cache::forget('books:all');

        return $book->fresh();
    }

    public function delete(Book $book): bool
    {
        $id = $book->id;
        $result = $book->delete();

        // 清除相關快取
        Cache::forget("books:{$id}");
        Cache::forget('books:all');

        return $result;
    }
}
```

**2. 使用 redis-cli 觀察快取**

```bash
# 進入 redis 容器
docker compose exec redis redis-cli

# 查看所有 key
127.0.0.1:6379> KEYS *

# 查看 books 相關的 key
127.0.0.1:6379> KEYS *books*

# 查看特定 key 的值
127.0.0.1:6379> GET laravel_cache:books:1

# 查看 key 的 TTL
127.0.0.1:6379> TTL laravel_cache:books:1

# 刪除特定 key
127.0.0.1:6379> DEL laravel_cache:books:1

# 刪除所有 key
127.0.0.1:6379> FLUSHALL
```

**3. 觀察 Cache Hit/Miss**

加入 log 觀察快取行為：

```php
public function find(int $id): ?Book
{
    $cacheKey = "books:{$id}";

    if (Cache::has($cacheKey)) {
        \Log::info("Cache HIT: {$cacheKey}");
    } else {
        \Log::info("Cache MISS: {$cacheKey}");
    }

    return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($id) {
        return Book::find($id);
    });
}
```

測試：

```bash
# 第一次呼叫（MISS）
curl http://localhost:8000/api/books/1

# 第二次呼叫（HIT）
curl http://localhost:8000/api/books/1

# 查看 log
tail -f storage/logs/laravel.log
```

**4. 手動清除快取測試回源**

```bash
# 在 redis-cli 中刪除快取
127.0.0.1:6379> DEL laravel_cache:books:1

# 再次呼叫 API，觀察 MISS
curl http://localhost:8000/api/books/1
```

### 延伸挑戰

1. 呼叫 `GET /api/books/1` 兩次，比較回應時間差異：

```bash
time curl http://localhost:8000/api/books/1  # 第一次（MISS）
time curl http://localhost:8000/api/books/1  # 第二次（HIT）
```

2. 將 TTL 從 600 秒改為 60 秒，觀察快取失效行為

3. 使用 `Cache::tags()` 重構快取管理：

```php
public function find(int $id): ?Book
{
    return Cache::tags(['books'])->remember("books:{$id}", self::CACHE_TTL, function () use ($id) {
        return Book::find($id);
    });
}

public function create(array $data): Book
{
    $book = Book::create($data);

    // 清除所有 books 標籤的快取
    Cache::tags(['books'])->flush();

    return $book;
}
```

---

## 踩坑提示

| 現象 | 原因 | 解法 |
|------|------|------|
| `KEYS` 指令在正式環境不能用 | `KEYS` 會阻塞 Redis（全表掃描） | 教學環境直觀好用；正式環境改用 `SCAN` |
| 快取沒更新 | 忘了在 update/delete 時清快取 | 確保 `Cache::forget()` 有被呼叫 |
| Redis 連不上 | 設定錯誤或服務未啟動 | 檢查 `.env` 的 `REDIS_HOST` 與 Docker 容器狀態 |
| `tags()` 不起作用 | 使用不支援 tags 的快取驅動 | 確認 `CACHE_DRIVER=redis` |
| Key 前綴不同 | Laravel 預設加上 `laravel_cache:` 前綴 | 在 redis-cli 中搜尋 `KEYS laravel_cache:*` |
| 快取資料是舊的 | 沒有正確清除快取 | 檢查所有修改資料的地方是否都有清快取 |

---

## 驗收標準（DoD）

- [ ] 能用 `redis-cli` 驗證快取行為（確認 key 存在、TTL、手動刪除）
- [ ] 能觀察到 Cache HIT 與 MISS 的行為差異
- [ ] 能說明 Cache-Aside 的讀取流程
- [ ] 能說明為什麼 update/delete 後需要清除快取
- [ ] 能解釋 TTL 設太短與太長的取捨
