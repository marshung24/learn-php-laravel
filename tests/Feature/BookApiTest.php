<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Book API Feature Test
 *
 * 測試 API 端點的完整流程（HTTP 請求 → 回應）
 */
class BookApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 測試 GET /api/books 回傳所有書籍
     */
    public function test_index_returns_all_books(): void
    {
        // Arrange
        Book::factory()->count(3)->create();

        // Act
        $response = $this->getJson('/api/books');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /**
     * 測試 GET /api/books/{id} 回傳單一書籍
     */
    public function test_show_returns_single_book(): void
    {
        // Arrange
        $book = Book::factory()->create(['title' => 'Test Book']);

        // Act
        $response = $this->getJson("/api/books/{$book->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Test Book');
    }

    /**
     * 測試 GET /api/books/{id} 找不到時回傳 404
     */
    public function test_show_returns_404_when_not_found(): void
    {
        // Act
        $response = $this->getJson('/api/books/99999');

        // Assert
        $response->assertStatus(404);
    }

    /**
     * 測試 POST /api/books 成功新增書籍
     */
    public function test_store_creates_book(): void
    {
        // Arrange
        $data = [
            'title' => 'New Book',
            'author' => 'Author',
            'isbn' => '978-1234567890',
            'stock' => 10,
        ];

        // Act
        $response = $this->postJson('/api/books', $data);

        // Assert
        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'New Book');

        $this->assertDatabaseHas('books', ['title' => 'New Book']);
    }

    /**
     * 測試 POST /api/books 驗證失敗回傳 422
     */
    public function test_store_returns_422_when_validation_fails(): void
    {
        // Act
        $response = $this->postJson('/api/books', []);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'author', 'isbn']);
    }

    /**
     * 測試 POST /api/books ISBN 重複回傳 422
     */
    public function test_store_returns_422_when_isbn_duplicate(): void
    {
        // Arrange
        Book::factory()->create(['isbn' => '978-1234567890']);

        $data = [
            'title' => 'New Book',
            'author' => 'Author',
            'isbn' => '978-1234567890',
            'stock' => 10,
        ];

        // Act
        $response = $this->postJson('/api/books', $data);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['isbn']);
    }

    /**
     * 測試 PUT /api/books/{id} 成功更新書籍
     */
    public function test_update_modifies_book(): void
    {
        // Arrange
        $book = Book::factory()->create(['title' => 'Old Title']);

        // Act
        $response = $this->putJson("/api/books/{$book->id}", [
            'title' => 'New Title',
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'New Title');

        $this->assertDatabaseHas('books', ['id' => $book->id, 'title' => 'New Title']);
    }

    /**
     * 測試 PUT /api/books/{id} 找不到時回傳 404
     */
    public function test_update_returns_404_when_not_found(): void
    {
        // Act
        $response = $this->putJson('/api/books/99999', ['title' => 'New Title']);

        // Assert
        $response->assertStatus(404);
    }

    /**
     * 測試 DELETE /api/books/{id} 成功刪除書籍
     */
    public function test_destroy_removes_book(): void
    {
        // Arrange
        $book = Book::factory()->create();

        // Act
        $response = $this->deleteJson("/api/books/{$book->id}");

        // Assert
        $response->assertStatus(204);
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    /**
     * 測試 DELETE /api/books/{id} 找不到時回傳 404
     */
    public function test_destroy_returns_404_when_not_found(): void
    {
        // Act
        $response = $this->deleteJson('/api/books/99999');

        // Assert
        $response->assertStatus(404);
    }
}
