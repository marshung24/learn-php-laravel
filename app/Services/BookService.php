<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * 書籍業務邏輯服務
 *
 * 實作 Cache-Aside 模式：
 * - 讀取：先查快取，miss 時查 DB 並寫入快取
 * - 寫入/刪除：操作 DB 後清除相關快取
 */
class BookService
{
    /**
     * 快取 TTL（秒）
     * 可透過 config('custom.cache_ttl') 設定
     */
    private const CACHE_TTL = 600;

    /**
     * 取得所有書籍（有快取）
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return Cache::remember('books:all', self::CACHE_TTL, function () {
            return Book::all();
        });
    }

    /**
     * 依 ID 查詢書籍（有快取）
     *
     * @param int $id
     * @return Book|null
     */
    public function find(int $id): ?Book
    {
        return Cache::remember("books:{$id}", self::CACHE_TTL, function () use ($id) {
            return Book::find($id);
        });
    }

    /**
     * 依 ID 查詢書籍，找不到拋 404（有快取）
     *
     * @param int $id
     * @return Book
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Book
    {
        return Cache::remember("books:{$id}", self::CACHE_TTL, function () use ($id) {
            return Book::findOrFail($id);
        });
    }

    /**
     * 依 ISBN 查詢書籍
     *
     * @param string $isbn
     * @return Book|null
     */
    public function findByIsbn(string $isbn): ?Book
    {
        return Book::where('isbn', $isbn)->first();
    }

    /**
     * 新增書籍
     *
     * 新增後清除列表快取
     *
     * @param array $data
     * @return Book
     */
    public function create(array $data): Book
    {
        $book = Book::create($data);

        // 清除列表快取
        $this->clearListCache();

        return $book;
    }

    /**
     * 更新書籍
     *
     * 更新後清除相關快取
     *
     * @param Book $book
     * @param array $data
     * @return Book
     */
    public function update(Book $book, array $data): Book
    {
        $book->update($data);

        // 清除相關快取
        $this->clearBookCache($book->id);
        $this->clearListCache();

        return $book->fresh();
    }

    /**
     * 刪除書籍
     *
     * 刪除後清除相關快取
     *
     * @param Book $book
     * @return bool
     */
    public function delete(Book $book): bool
    {
        $id = $book->id;
        $result = $book->delete();

        // 清除相關快取
        $this->clearBookCache($id);
        $this->clearListCache();

        return $result;
    }

    /**
     * 清除單筆書籍快取
     *
     * @param int $id
     * @return void
     */
    private function clearBookCache(int $id): void
    {
        Cache::forget("books:{$id}");
    }

    /**
     * 清除列表快取
     *
     * @return void
     */
    private function clearListCache(): void
    {
        Cache::forget('books:all');
    }
}
