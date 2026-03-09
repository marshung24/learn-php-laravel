<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * 資料庫種子入口
 *
 * 執行 php artisan db:seed 時會執行這個類別
 */
class DatabaseSeeder extends Seeder
{
    /**
     * 執行資料庫種子
     */
    public function run(): void
    {
        // 在這裡呼叫其他 Seeder
        // $this->call([
        //     BookSeeder::class,
        // ]);
    }
}
