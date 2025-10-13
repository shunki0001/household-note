<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use PHPUnit\Framework\Attrubutes\Test;

class AuthAccessTest extends TestCase
{
    use RefreshDatabase;

    // 未ログインユーザーはダッシュボードにアクセスできずログインんページにリダイレクトされる
    public function test_not_login_user_cannot_access_page(): void
    {
        $response = $this->get('/dashboard');
        // $response = $this->get('/list');

        $response->assertRedirect('/login'); // ログインページにリダイレクトされるか確認
    }

    // ログインユーザーはダッシュボードにアクセスできる
    // public function test_login_user_can_access_page(): void
    // {
        // $user = User::factory()->create();

        // $response = $this->actingAs($user)->get('/dashboard');

        // $response->assertStatus(200); // 正常にアクセスできるか確認
        // // $response->assertSee('<div id="app"'); // ページ内に文字があるかなども確認可能
    // }

    // ログイン中にアクセス可能なページに全てアクセス
    public function test_login_user_can_access_all_pages(): void
    {
        $user = User::factory()->create();

        // ログイン中の状態を作る
        $this->actingAs($user);

        // ログイン中にアクセス可能なルート一覧
        $pages = [
            '/dashboard',
            '/list',
            '/graph/monthly',
            '/graph/category',
            '/profile',
        ];

        foreach ($pages as $page) {
            $response = $this->get($page);
            $response->assertStatus(200, "{$page} にアクセスできませんでした");
        }
    }
}
