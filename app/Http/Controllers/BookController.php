<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Services\BookService;

/**
 * 書籍管理 MVC Controller（回傳 HTML 頁面）
 *
 * 使用 Form Request 驗證、Service 處理業務邏輯
 */
class BookController extends Controller
{
    /**
     * 建構子 - 依賴注入 BookService
     */
    public function __construct(
        private BookService $bookService
    ) {}

    /**
     * 書籍清單頁面
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $books = $this->bookService->getAll();

        return view('books.index', compact('books'));
    }

    /**
     * 書籍詳情頁面
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show(int $id)
    {
        $book = $this->bookService->findOrFail($id);

        return view('books.show', compact('book'));
    }

    /**
     * 新增書籍表單頁面
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('books.create');
    }

    /**
     * 儲存新書籍
     *
     * PRG 模式：POST 後 redirect，避免使用者按 F5 重複提交
     *
     * @param StoreBookRequest $request 驗證通過才會進入
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreBookRequest $request)
    {
        $this->bookService->create($request->validated());

        return redirect()
            ->route('books.index')
            ->with('success', '書籍新增成功！');
    }

    /**
     * 編輯書籍表單頁面
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        $book = $this->bookService->findOrFail($id);

        return view('books.edit', compact('book'));
    }

    /**
     * 更新書籍
     *
     * @param UpdateBookRequest $request 驗證通過才會進入
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateBookRequest $request, int $id)
    {
        $book = $this->bookService->findOrFail($id);
        $this->bookService->update($book, $request->validated());

        return redirect()
            ->route('books.show', $id)
            ->with('success', '書籍更新成功！');
    }

    /**
     * 刪除書籍
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        $book = $this->bookService->findOrFail($id);
        $this->bookService->delete($book);

        return redirect()
            ->route('books.index')
            ->with('success', '書籍刪除成功！');
    }
}
