<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 書籍 Model，對應資料庫 books 資料表
 *
 * 使用 Active Record 模式 - 一個 Model 物件對應一筆資料庫記錄
 *
 * @property int $id 主鍵
 * @property string $title 書名
 * @property string $author 作者
 * @property string $isbn ISBN（全系統唯一）
 * @property int $stock 可借閱庫存數量
 * @property string|null $publisher 出版社
 * @property \Carbon\Carbon $created_at 建立時間
 * @property \Carbon\Carbon $updated_at 更新時間
 */
class Book extends Model
{
    use HasFactory;

    /**
     * 對應的資料表名稱
     *
     * @var string
     */
    protected $table = 'books';

    /**
     * 可批量賦值的欄位（Mass Assignment 防護）
     *
     * 只有列在這裡的欄位才能用 create() 或 update() 批量賦值
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'stock',
        'publisher',
    ];

    /**
     * 隱藏欄位（toArray/toJson 時不輸出）
     *
     * @var list<string>
     */
    protected $hidden = [];

    /**
     * 型別轉換
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * 查詢作用域：依作者篩選
     *
     * 使用方式：Book::byAuthor('王大明')->get()
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $author
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByAuthor($query, string $author)
    {
        return $query->where('author', $author);
    }

    /**
     * 查詢作用域：有庫存
     *
     * 使用方式：Book::inStock()->get()
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}
