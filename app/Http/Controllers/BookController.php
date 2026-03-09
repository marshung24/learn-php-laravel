<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * 書籍管理 MVC Controller（回傳 HTML 頁面）
 *
 * 使用假資料示範 Blade 模板的基本用法
 */
class BookController extends Controller
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
     * 書籍清單頁面
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $books = collect($this->books);

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
        $book = collect($this->books)->firstWhere('id', $id);

        if (!$book) {
            abort(404, '找不到書籍');
        }

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
     * 儲存新書籍（目前為假實作）
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // U06 會改用 Eloquent 真實儲存
        // 目前先用假實作，展示 PRG 模式

        return redirect()
            ->route('books.index')
            ->with('success', '書籍新增成功！（假資料）');
    }
}
