<?php

namespace App\Http\Requests\Traits;

use Illuminate\Validation\Rule;

trait UserValidationRules
{
    protected function nameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    protected function emailRules($userId = null): array
    {
        $rules = ['required', 'string', 'email', 'max:255'];

        if ($userId) {
            // 更新時(既存ユーザー)->自分のメールは除外してuniqueチェック
            $rules[] = Rule::unique('users', 'email')->ignore($userId);
        } else {
            // 新規登録時
            $rules[] = 'unique:users,email';
        }

        return $rules;
    }

    protected function passwordRules(bool $isUpdate = false): array
    {
        if ($isUpdate) {
            // 更新時：空ならスキップ
            return [
                'nullable',
                'string',
                'min:8',
                'regex:/^(?=.*[a-zA-Z])(?=.*[0-9]).+$/',
                'confirmed',
            ];
        }

        // 新規登録時
        return [
            'required',
            'string',
            'min:8',
            'regex:/^(?=.*[a-zA-Z])(?=.*[0-9]).+$/',
            'confirmed',
        ];
    }
}
