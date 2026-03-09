<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * 書籍管理 REST API Controller（回傳 JSON）
 *
 * 使用假資料示範 RESTful API 的基本用法
 */
class BookApiController extends Controller
{
    /**
     * 假資料：書籍清單
     */
    private array $books = [
        ['id' => 1, 'title' => 'Laravel 入門', 'author' => '王大明', 'isbn' => '978-1234567890', 'stock' => 5],
        ['id' => 2, 'title' => 'PHP 實戰', 'author' => '李小華', 'isbn' => '978-0987654321', 'stock' => 0],
        ['id' => 3, 'title' => 'Redis 快取', 'author' => '張三', 'isbn' => '978-1122334455', 'stock' => 10],
    ];

    /**
     * 取得所有書籍
     *
     * GET /api/books
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json($this->books);
    }

    /**
     * 取得單一書籍
     *
     * GET /api/books/{id}
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $book = collect($this->books)->firstWhere('id', $id);

        if (!$book) {
            return response()->json([
                'message' => 'Book not found',
            ], 404);
        }

        return response()->json($book);
    }

    /**
     * 新增書籍（假實作）
     *
     * POST /api/books
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // U06 會改用 Eloquent 真實儲存
        // 目前先用假實作，展示 JSON 回應

        $book = array_merge(
            ['id' => 4],
            $request->only(['title', 'author', 'isbn', 'stock'])
        );

        return response()->json($book, 201);
    }

    /**
     * 更新書籍（假實作）
     *
     * PUT /api/books/{id}
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $book = collect($this->books)->firstWhere('id', $id);

        if (!$book) {
            return response()->json([
                'message' => 'Book not found',
            ], 404);
        }

        // U06 會改用 Eloquent 真實更新
        $book = array_merge($book, $request->only(['title', 'author', 'isbn', 'stock']));

        return response()->json($book);
    }

    /**
     * 刪除書籍（假實作）
     *
     * DELETE /api/books/{id}
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $book = collect($this->books)->firstWhere('id', $id);

        if (!$book) {
            return response()->json([
                'message' => 'Book not found',
            ], 404);
        }

        // U06 會改用 Eloquent 真實刪除
        return response()->json(null, 204);
    }
}
