<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Services\BookService;
use Illuminate\Http\Request;

/**
 * 書籍管理 REST API Controller（回傳 JSON）
 *
 * 使用 Service 層處理業務邏輯，Resource 處理輸出格式
 */
class BookApiController extends Controller
{
    /**
     * 建構子 - 依賴注入 BookService
     */
    public function __construct(
        private BookService $bookService
    ) {}

    /**
     * 取得所有書籍
     *
     * GET /api/books
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $books = $this->bookService->getAll();

        return BookResource::collection($books);
    }

    /**
     * 取得單一書籍
     *
     * GET /api/books/{id}
     *
     * @param int $id
     * @return BookResource|\Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $book = $this->bookService->find($id);

        if (!$book) {
            return response()->json([
                'message' => 'Book not found',
            ], 404);
        }

        return new BookResource($book);
    }

    /**
     * 新增書籍
     *
     * POST /api/books
     *
     * @param Request $request
     * @return BookResource
     */
    public function store(Request $request)
    {
        $book = $this->bookService->create($request->all());

        return (new BookResource($book))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * 更新書籍
     *
     * PUT /api/books/{id}
     *
     * @param Request $request
     * @param int $id
     * @return BookResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $book = $this->bookService->find($id);

        if (!$book) {
            return response()->json([
                'message' => 'Book not found',
            ], 404);
        }

        $book = $this->bookService->update($book, $request->all());

        return new BookResource($book);
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
        $book = $this->bookService->findOrFail($id);
        $this->bookService->delete($book);

        return response()->noContent();
    }
}
