<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 新增書籍驗證請求
 *
 * 把驗證邏輯從 Controller 獨立出來
 * 驗證失敗時：
 * - API 請求：自動回傳 422 JSON 錯誤
 * - Web 請求：redirect 回前頁並帶入錯誤訊息
 */
class StoreBookRequest extends FormRequest
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
        return [
            'title' => 'required|string|max:200',
            'author' => 'required|string|max:100',
            'isbn' => 'required|string|max:20|unique:books,isbn',
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
            'title.required' => '書名為必填',
            'title.max' => '書名不可超過 200 字',
            'author.required' => '作者為必填',
            'author.max' => '作者不可超過 100 字',
            'isbn.required' => 'ISBN 為必填',
            'isbn.unique' => 'ISBN 已存在',
            'isbn.max' => 'ISBN 不可超過 20 字',
            'stock.integer' => '庫存必須是整數',
            'stock.min' => '庫存不可為負數',
            'publisher.max' => '出版社不可超過 100 字',
        ];
    }
}
