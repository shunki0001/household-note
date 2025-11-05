<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class PasswordRequest extends FormRequest
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
            // パスワードルールを設定
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                // 8文字かつ英数字混在
                Password::min(8)
                    ->letters()  // 英字を含む
                    ->numbers(), // 数字を含む
            ],
        ];
    }
}
