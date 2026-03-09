<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 更新書籍驗證請求
 *
 * 更新時 ISBN unique 檢查需排除自己
 */
class UpdateBookRequest extends FormRequest
{
    /**
     * 授權檢查
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 驗證規則
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // 取得路由參數中的 id
        $id = $this->route('id');

        return [
            'title' => 'sometimes|string|max:200',
            'author' => 'sometimes|string|max:100',
            // unique 檢查排除自己
            'isbn' => 'sometimes|string|max:20|unique:books,isbn,' . $id,
            'stock' => 'integer|min:0',
            'publisher' => 'nullable|string|max:100',
        ];
    }

    /**
     * 自訂錯誤訊息
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.max' => '書名不可超過 200 字',
            'author.max' => '作者不可超過 100 字',
            'isbn.unique' => 'ISBN 已存在',
            'isbn.max' => 'ISBN 不可超過 20 字',
            'stock.integer' => '庫存必須是整數',
            'stock.min' => '庫存不可為負數',
            'publisher.max' => '出版社不可超過 100 字',
        ];
    }
}
