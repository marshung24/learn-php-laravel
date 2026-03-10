<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Book Factory
 *
 * 用於測試時快速建立 Book 假資料
 *
 * 使用方式：
 * - Book::factory()->create()                    建立單筆
 * - Book::factory()->count(3)->create()          建立多筆
 * - Book::factory()->create(['title' => '...'])  自訂屬性
 * - Book::factory()->outOfStock()->create()      使用自訂狀態
 */
class BookFactory extends Factory
{
    /**
     * 定義模型預設狀態
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'author' => fake()->name(),
            'isbn' => fake()->unique()->isbn13(),
            'stock' => fake()->numberBetween(0, 100),
            'publisher' => fake()->company(),
        ];
    }

    /**
     * 自訂狀態：缺貨
     *
     * @return self
     */
    public function outOfStock(): self
    {
        return $this->state([
            'stock' => 0,
        ]);
    }

    /**
     * 自訂狀態：有庫存
     *
     * @return self
     */
    public function inStock(): self
    {
        return $this->state([
            'stock' => fake()->numberBetween(1, 100),
        ]);
    }
}
