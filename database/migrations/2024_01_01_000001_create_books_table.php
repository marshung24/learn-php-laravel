<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 建立書籍資料表
 *
 * 這是書籍管理系統的核心資料表
 * 時間戳命名確保 migration 按順序執行
 */
return new class extends Migration
{
    /**
     * 執行 migration（建立資料表）
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            // 主鍵，BIGINT UNSIGNED AUTO_INCREMENT
            $table->id();

            // 書名，VARCHAR(200) NOT NULL
            $table->string('title', 200);

            // 作者，VARCHAR(100) NOT NULL
            $table->string('author', 100);

            // ISBN，VARCHAR(20) UNIQUE
            // 支援 ISBN-10 和 ISBN-13 格式
            $table->string('isbn', 20)->unique();

            // 庫存數量，INT DEFAULT 0
            // 0 表示暫無庫存，不代表下架
            $table->integer('stock')->default(0);

            // 出版社，VARCHAR(100) NULLABLE
            $table->string('publisher', 100)->nullable();

            // 時間戳欄位
            // created_at: 建立時間
            // updated_at: 更新時間
            $table->timestamps();
        });
    }

    /**
     * 回滾 migration（刪除資料表）
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
