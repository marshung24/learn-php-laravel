<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 書籍資料種子
 *
 * 建立初始測試資料供開發和展示使用
 */
class BookSeeder extends Seeder
{
    /**
     * 執行資料種子
     */
    public function run(): void
    {
        $books = [
            [
                'title' => 'Laravel 入門',
                'author' => '王大明',
                'isbn' => '978-1234567890',
                'stock' => 10,
                'publisher' => '技術出版社',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'PHP 實戰',
                'author' => '李小華',
                'isbn' => '978-0987654321',
                'stock' => 5,
                'publisher' => '程式書屋',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Redis 快取',
                'author' => '張三',
                'isbn' => '978-1122334455',
                'stock' => 0,
                'publisher' => '資料庫出版',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Docker 容器化',
                'author' => '陳四',
                'isbn' => '978-5566778899',
                'stock' => 15,
                'publisher' => '雲端技術',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'MySQL 資料庫',
                'author' => '林五',
                'isbn' => '978-9988776655',
                'stock' => 8,
                'publisher' => '資料庫出版',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('books')->insert($books);
    }
}
