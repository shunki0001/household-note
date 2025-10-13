<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class ApiAccessTest extends TestCase
{
    use RefreshDatabase;

    // APIエンドポイント一覧
    /** @var array */
    private array $apiEndpoints = [
        'api/expenses/latest-json',
        'api/dashboard/total-expense',
        'api/chart-data',
        'api/chart-data/category-monthly-single',
        'api/chart-data/doughnut',
        'api/chart-data/monthly-expenses',
        'api/incomes/total-monthly-incomes',
        'api/report-data/latest-json',
        'api/report-data/monthly-transactions',
        // 'api/xxxx', // エラーハンドリング用
    ];

    // ログイン状態でAPIアクセス可能か
    public function test_login_user_can_access_api_endpoints(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $failedEndpoints = [];

        foreach ($this->apiEndpoints as $endpoint) {
            $response = $this->getJson($endpoint);

            if($response->status() !== 200) {
                $failedEndpoints[] = "{$endpoint} (HTTP: {$response->status()})";
            }
            // $response->assertStatus(200);
        }

        if (!empty($failedEndpoints)) {
            $this->fail("以下のAPIにアクセスできませんでした:\n" . implode("\n", $failedEndpoints));
        }

        $this->assertTrue(true); // 成功時はOKとして扱う
    }

    // 未ログイン状態はAPIアクセスは不可
    public function test_guest_user_cannot_access_api_endpoints(): void
    {

        $failedEndpoints = [];

        foreach ($this->apiEndpoints as $endpoint) {
            $response = $this->getJson($endpoint);

            if($response->status() !== 401) {
                $failedEndpoints[] = "{$endpoint} (HTTP: {$response->status()})";
            }
            // $response->assertStatus(401); // 未認証の場合
        }

        if (!empty($failedEndpoints)) {
            $this->fail("以下のAPIに不正アクセスできました:\n" . implode("\n", $failedEndpoints));
        }

        $this->assertTrue(true);
    }
}
