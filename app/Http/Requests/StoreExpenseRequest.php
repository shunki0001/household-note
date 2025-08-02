<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => '金額は必須です',
            'amount.numeric' => '金額は数値で入力してください',
            'amount.min' => '金額は0以上の値を入力してください',
            'date.required' => '日付は必須です',
            'date.date' => '日付はYYYY-MM-DDの形式で入力してください',
            'title.required' => '費用名は必須です',
            'title.max' => '費用名は255文字以内で入力してください',
            'category_id.required' => 'カテゴリーは必須です',
            'category_id.exists' => 'カテゴリーが存在しません',
        ];
    }
}
