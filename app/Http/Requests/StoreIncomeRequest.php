<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncomeRequest extends FormRequest
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
            'income_date' => 'required|date',
            'income_category_id' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            // 'amount.required' => '金額は必須です',
            // 'amount.numeric' => '金額は数値で入力してください',
            // 'amount.min' => '金額は0以上の値を入力してください',
            // 'income_date.required' => '日付は必須です',
            // 'income_date.date' => '日付はYYYY-MM-DDの形式で入力してください',
            // 'income_category_id.required' => 'カテゴリーは必須です',
            // 'income_category_id.exists' => 'カテゴリーが存在しません',
        ];
    }
}
