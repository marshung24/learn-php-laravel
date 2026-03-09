<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

/**
 * 書籍管理 MVC Controller（回傳 HTML 頁面）
 *
 * 使用 Eloquent ORM 串接資料庫
 */
class BookController extends Controller
{
    /**
     * 書籍清單頁面
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $books = Book::all();

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
        $book = Book::findOrFail($id);

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
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        Book::create($request->only(['title', 'author', 'isbn', 'stock', 'publisher']));

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
        $book = Book::findOrFail($id);

        return view('books.edit', compact('book'));
    }

    /**
     * 更新書籍
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $book = Book::findOrFail($id);
        $book->update($request->only(['title', 'author', 'isbn', 'stock', 'publisher']));

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
        $book = Book::findOrFail($id);
        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', '書籍刪除成功！');
    }
}
