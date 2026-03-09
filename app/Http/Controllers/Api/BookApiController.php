<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

/**
 * 書籍管理 REST API Controller（回傳 JSON）
 *
 * 使用 Eloquent ORM 串接資料庫
 */
class BookApiController extends Controller
{
    /**
     * 取得所有書籍
     *
     * GET /api/books
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(Book::all());
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
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'message' => 'Book not found',
            ], 404);
        }

        return response()->json($book);
    }

    /**
     * 新增書籍
     *
     * POST /api/books
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $book = Book::create($request->all());

        return response()->json($book, 201);
    }

    /**
     * 更新書籍
     *
     * PUT /api/books/{id}
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'message' => 'Book not found',
            ], 404);
        }

        $book->update($request->all());

        return response()->json($book);
    }

    /**
     * 刪除書籍
     *
     * DELETE /api/books/{id}
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json(null, 204);
    }
}
