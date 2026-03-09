<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Services\BookService;

/**
 * 書籍管理 REST API Controller（回傳 JSON）
 *
 * 使用 Form Request 驗證、Service 處理業務邏輯、Resource 處理輸出格式
 * 正確使用 HTTP 狀態碼
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
     * 回傳 200 OK
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
     * 回傳 200 OK 或 404 Not Found
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
     * 回傳 201 Created 或 422 Unprocessable Entity
     *
     * @param StoreBookRequest $request 驗證通過才會進入
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreBookRequest $request)
    {
        $book = $this->bookService->create($request->validated());

        return (new BookResource($book))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * 更新書籍
     *
     * PUT /api/books/{id}
     * 回傳 200 OK、404 Not Found 或 422 Unprocessable Entity
     *
     * @param UpdateBookRequest $request 驗證通過才會進入
     * @param int $id
     * @return BookResource|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateBookRequest $request, int $id)
    {
        $book = $this->bookService->find($id);

        if (!$book) {
            return response()->json([
                'message' => 'Book not found',
            ], 404);
        }

        $book = $this->bookService->update($book, $request->validated());

        return new BookResource($book);
    }

    /**
     * 刪除書籍
     *
     * DELETE /api/books/{id}
     * 回傳 204 No Content 或 404 Not Found
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $book = $this->bookService->find($id);

        if (!$book) {
            return response()->json([
                'message' => 'Book not found',
            ], 404);
        }

        $this->bookService->delete($book);

        return response()->noContent();
    }
}
