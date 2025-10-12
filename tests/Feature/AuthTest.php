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
    /* @test */
    public function test_registered_user_can_login(): void
    {
        // 1. テスト用のユーザーを作成
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password123'), // パスワードを指定
        ]);

        // 2. ログインページにアクセスして、ログインフォームを送信
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        // 3. ログイン後のリダイレクトを確認
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user); // 認証されていることを確認
    }

    // ログイン失敗のテスト
    public function test_user_cannot_login_with_invalid_password(): void
    {
        // 1. テスト用のユーザーを作成
        $user = User::factory()->create([
            'password' => bcrypt('correct_password'), // 正しいパスワードを指定
        ]);

        // 2. ログインページにアクセスして、無効なパスワードでログインフォームを送信
        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrong_password', // 間違ったパスワード
        ]);

        // 3. ログイン失敗後のリダイレクトとエラーメッセージを確認
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email'); // エラーメッセージがセッションにあることを確認
        $this->assertGuest(); // 認証されていないことを確認
    }

    // ログアウトのテスト
    public function test_authenticated_user_can_logout(): void
    {
        // 1. テスト用のユーザーを作成してログイン
        $user = User::factory()->create();
        $this->be($user); // ユーザーを認証状態にする

        // 2. ログアウトリクエストを送信
        $response = $this->post('/logout');

        // 3. ログアウト後のリダイレクトと認証状態を確認
        $response->assertRedirect('/'); // ログアウト後にトップページにリダイレクトされることを確認
        $this->assertGuest(); // 認証されていないことを確認
    }

}
