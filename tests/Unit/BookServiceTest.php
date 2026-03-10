<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Services\BookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * BookService 單元測試
 *
 * 測試 Service 層的業務邏輯
 */
class BookServiceTest extends TestCase
{
    use RefreshDatabase;

    private BookService $bookService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookService = new BookService();
    }

    /**
     * 測試 getAll 回傳所有書籍
     */
    public function test_get_all_returns_all_books(): void
    {
        // Arrange
        Book::factory()->count(3)->create();

        // Act
        $result = $this->bookService->getAll();

        // Assert
        $this->assertCount(3, $result);
    }

    /**
     * 測試 find 找到書籍時回傳 Book
     */
    public function test_find_returns_book_when_exists(): void
    {
        // Arrange
        $book = Book::factory()->create(['title' => 'Test Book']);

        // Act
        $result = $this->bookService->find($book->id);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals('Test Book', $result->title);
    }

    /**
     * 測試 find 找不到時回傳 null
     */
    public function test_find_returns_null_when_not_exists(): void
    {
        // Act
        $result = $this->bookService->find(99999);

        // Assert
        $this->assertNull($result);
    }

    /**
     * 測試 findOrFail 找不到時拋出例外
     */
    public function test_find_or_fail_throws_exception_when_not_exists(): void
    {
        // Assert
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        // Act
        $this->bookService->findOrFail(99999);
    }

    /**
     * 測試 create 成功儲存到資料庫
     */
    public function test_create_stores_book_in_database(): void
    {
        // Arrange
        $data = [
            'title' => 'New Book',
            'author' => 'Author',
            'isbn' => '978-1234567890',
            'stock' => 10,
        ];

        // Act
        $book = $this->bookService->create($data);

        // Assert
        $this->assertDatabaseHas('books', ['title' => 'New Book']);
        $this->assertEquals('New Book', $book->title);
    }

    /**
     * 測試 update 成功更新資料庫
     */
    public function test_update_modifies_book_in_database(): void
    {
        // Arrange
        $book = Book::factory()->create(['title' => 'Old Title']);

        // Act
        $updated = $this->bookService->update($book, ['title' => 'New Title']);

        // Assert
        $this->assertEquals('New Title', $updated->title);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'title' => 'New Title']);
    }

    /**
     * 測試 delete 成功從資料庫刪除
     */
    public function test_delete_removes_book_from_database(): void
    {
        // Arrange
        $book = Book::factory()->create();

        // Act
        $this->bookService->delete($book);

        // Assert
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }
}
