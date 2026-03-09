<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Collection;

/**
 * 書籍業務邏輯服務
 *
 * 將業務邏輯從 Controller 抽離，讓 Controller 只負責 HTTP 協議
 * Service 層負責：驗證規則、流程控制、業務邏輯
 */
class BookService
{
    /**
     * 取得所有書籍
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return Book::all();
    }

    /**
     * 依 ID 查詢書籍
     *
     * @param int $id
     * @return Book|null
     */
    public function find(int $id): ?Book
    {
        return Book::find($id);
    }

    /**
     * 依 ID 查詢書籍，找不到拋 404
     *
     * @param int $id
     * @return Book
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Book
    {
        return Book::findOrFail($id);
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
     * @param array $data
     * @return Book
     */
    public function create(array $data): Book
    {
        return Book::create($data);
    }

    /**
     * 更新書籍
     *
     * @param Book $book
     * @param array $data
     * @return Book
     */
    public function update(Book $book, array $data): Book
    {
        $book->update($data);

        return $book->fresh();
    }

    /**
     * 刪除書籍
     *
     * @param Book $book
     * @return bool
     */
    public function delete(Book $book): bool
    {
        return $book->delete();
    }
}
