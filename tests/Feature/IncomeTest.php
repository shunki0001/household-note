<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Income;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use App\Models\IncomeCategory;

class IncomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_store_income()
    {
        // ユーザーを作成して認証
        $user = User::factory()->create();
        $this->actingAs($user);

        // カテゴリーを作成
        $income_category = IncomeCategory::factory()->create([
            'name' => '給与',
        ]);

        // APIにPOSTリクエスト
        $response = $this->post('/incomes', [
            'amount' => '5000',
            'income_date' => '2025-09-20',
            'income_category_id' => $income_category->id,
        ]);

        // ステータスコード200かチェック
        $response->assertStatus(200);

        // データベースに保存されているか確認
        $this->assertDatabaseHas('incomes', [
            'user_id' => $user->id,
            'amount' => 5000,
            'income_date' => '2025-09-20',
            'income_category_id' => $income_category->id,
        ]);
    }

    public function test_income_database_connection()
    {
        dump(\DB::connection()->getDatabaseName()); // 接続DBを確認
        $this->assertEquals('testing', \DB::connection()->getDatabaseName());
    }
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
