<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class GuestAccessTest extends TestCase
{
    use RefreshDatabase;

    // 未ログインユーザーがアクセスできない「auth必須ページ」を全てテスト
    public function test_not_login_user_cannot_access_protected_routes(): void
    {
        // authミドルウェアが設定されているルートだけ取得
        $protectedRoutes = collect(Route::getRoutes())
            ->filter(fn($route) => in_array('auth', $route->middleware()))
            ->filter(fn($route) => in_array('GET', $route->methods)) // GETのみ
            ->pluck('uri')
            ->unique()
            ->values();

        foreach ($protectedRoutes as $uri) {
            $response = $this->get($uri);
            $response->assertRedirect('/login');
        }
    }

    // authミドルウェアが設定されていないルーろの中に
    // 「本来はログイン必須ページにすべきページ」が混じっていないか検出

    public function test_routes_without_auth_middleware_are_intended():void
    {
        // authミドルウェアがついていないルート
        $publicRoutes = collect(Route::getRoutes())
            ->reject(fn($route) => in_array('auth', $route->middleware()))
            ->filter(fn($route) => in_array('GET', $route->methods))
            ->pluck('uri')
            ->values();

        // 許可された公開ルート(例:ログイン画面, 登録画面など)
        $allowedPublicRoutes = [
            '/',
            'login',
            'register',
            'password/reset',
            'api/*', 'sanctum/csrf-cookie',
            'up',
            'forgot-password',
            'reset-password/*',
            'storage/*',
        ];

        $unauthorizedRoutes = [];

        foreach ($publicRoutes as $uri) {
            $isAllowed = collect($allowedPublicRoutes)->contains(function ($allowed) use ($uri) {
                return Str::is($allowed, $uri);
            });

            if (! $isAllowed) {
                $this->fail("⚠️ ルート「{$uri}」にauthミドルウェアが設定されていません。間違い無いですか？");
            }
        }

        // 不正ルートがあればテスト失敗
        $this->assertEmpty(
            $unauthorizedRoutes,
            '⚠️ authミドルウェアは設定されていない不正ルート:' . implode(', ', $unauthorizedRoutes)
        );
    }
}
