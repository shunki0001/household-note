<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password1234',
            'password_confirmation' => 'password1234',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    // バリデーションルールに違反していたら新規登録できない
    public function test_new_users_cannot_register_with_invalid_passwords(): void
    {
        $invalidPasswords = [
            '12345678' => '数字のみ',
            'password' => '英字のみ',
            'pass12' => '8文字未満',
        ];

        foreach ($invalidPasswords as $invalidPassword => $case) {
            $response = $this->from('/register')->post('/register', [
                'name' => 'Test User',
                'email' => "test_{$case}@example.com", // case名でメールを変えて重複回避
                'password' => $invalidPassword,
                'password_confirmation' => $invalidPassword,
            ]);

            // passwordフィールドでバリデーションエラーが出る
            $response
                ->assertSessionHasErrors('password' , "失敗ケース: {$case}")
                ->assertRedirect('/register');

            // ユーザーは認証されていない(登録失敗)
            $this->assertGuest(null, "失敗ケース: {$case}");
        }
    }

    // 入力誤りがあれば新規登録できない
    public function test_new_users_cannot_register_with_invalid_data(): void
    {
        $invaliDataSets = [
            [
                'data' => ['name' => '', 'email' => 'test@example.com', 'password' => 'password1234', 'password_confirmation' => 'password1234'],
                'errors' => ['name'], // name必須チェック
                'case' => 'name必須'
            ],
            [
                'data' => ['name' => 'Test User', 'email' => '', 'password' => 'password1234', 'password_confirmation' => 'password1234'],
                'errors' => ['email'], // email必須チェック
                'case' => 'name必須'
            ],
            [
                'data' => ['name' => 'Test User', 'email' => 'invalid-email', 'password' => 'password1234', 'password_confirmation' => 'password1234'],
                'errors' => ['email'], // メール形式チェック
                'case' => 'メール形式不正'
            ],
            [
                'data' => ['name' => 'Test User', 'email' => 'test_null@example.com', 'password' => '', 'password_confirmation' => ''],
                'errors' => ['password'], // password必須チェック
                'case' => 'パスワード数字のみ'
            ],
            [
                'data' => ['name' => 'Test User', 'email' => 'test_numonly@example.com', 'password' => '12345678', 'password_confirmation' => '12345678'],
                'errors' => ['password'], // パスワード数字のみ
                'case' => 'パスワード数字のみ'
            ],
            [
                'data' => ['name' => 'Test User', 'email' => 'test_letters@example.com', 'password' => 'password', 'password_confirmation' => 'password'],
                'errors' => ['password'], // パスワード英字のみ
                'case' => 'パスワード英字のみ'
            ],
            [
                'data' => ['name' => 'Test User', 'email' => 'test_short@example.com', 'password' => 'pass12', 'password_confirmation' => 'pass12'],
                'errors' => ['password'], // パスワード8文字未満
                'case' => 'パスワード短すぎ'
            ],
        ];

        foreach ($invaliDataSets as $set) {
            $response = $this->from('/register')->post('/register', $set['data']);

            $response
                ->assertSessionHasErrors($set['errors'], "失敗ケース: {$set['case']}")
                ->assertRedirect('/register');

            // 登録失敗のためのユーザーが作られていないことを確認
            $this->assertGuest(null, "失敗ケース: {$set['case']}");
        }
    }

    // 登録済みのメールアドレスは登録できない
    public function test_cannot_register_with_existing_email(): void
    {
        // 既存ユーザー作成
        $existingUser = \App\Models\User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        // 同じメールアドレスで新規登録を試みる
        $response = $this->from('/register')->post('/register', [
            'name' => 'Another User',
            'email' => 'existing@example.com',
            'password' => 'password1234',
            'password_confirmation' => 'password1234',
        ]);

        $response
            ->assertSessionHasErrors('email')
            ->assertRedirect('/register');

        // 登録失敗のため認証されていないことを確認
        $this->assertGuest();
    }
}
