<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // 正しいユーザー情報でログインできること
    /**
     * 正しいユーザー情報でログインできること
     * 1. テスト用のユーザーを作成
     * 2. ログインページにアクセスして、ログインフォームを送信
     * 3. ログイン後のリダイレクトを確認
     */
    public function test_registered_user_can_login(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * ログイン失敗のテスト
     *
     * 手順
     * 1. テスト用のユーザーを作成
     * 2. ログインページにアクセスして無効なパスワードでログイン
     * 3. 想定動作: /loginにリダイレクト、エラーメッセージ表示、認証されていないことを確認
     */
    public function test_user_cannot_login_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('correct_password'), // 正しいパスワードを指定
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrong_password', // 間違ったパスワード
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // ログアウトのテスト
    /**
     * ログアウトのテスト
     *
     * 手順
     * 1. ユーザー作成 + ログイン
     * 2. ログアウトリクエストを送信
     * 3. トップページにリダイレクト + 未ログイン状態か確認
     */
    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();
        $this->be($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

}
