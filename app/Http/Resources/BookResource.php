<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 書籍 API Resource
 *
 * 把 Book Model 轉換為 JSON 回應格式
 * 控制輸出的欄位與格式，分離輸出邏輯
 *
 * 優點：
 * 1. 統一輸出格式
 * 2. 隱藏內部欄位
 * 3. 格式轉換（snake_case → camelCase）
 * 4. 計算欄位（如 inStock）
 */
class BookResource extends JsonResource
{
    /**
     * 將資源轉換為陣列
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'stock' => $this->stock,
            'inStock' => $this->stock > 0,
            'publisher' => $this->publisher,
            'createdAt' => $this->created_at?->toIso8601String(),
            'updatedAt' => $this->updated_at?->toIso8601String(),
        ];
    }
}
