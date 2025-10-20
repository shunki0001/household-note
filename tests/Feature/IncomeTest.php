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

    // 編集テスト
    public function test_income_can_be_updated(): void
    {
        // ユーザー作成
        $user = User::factory()->create();
        $this->actingAs($user);

        // カテゴリー作成
        $income_category = IncomeCategory::factory()->create([
            'name' => '給与',
        ]);

        // 既存データを作成
        $income = Income::factory()->create([
            'user_id' => $user->id,
            'income_category_id' => $income_category->id,
            'amount' => 1000,
            'income_date' => '2025-10-18',
        ]);

        // PUTリクエストで更新
        $response = $this->put("incomes/{$income->id}", [
            'amount' => 1500,
            'income_date' => '2025-10-19',
            'income_category_id' => $income_category->id,
        ]);

        $response->assertStatus(200);

        // データベースに更新後のデータがあるか確認
        $this->assertDatabaseHas('incomes', [
            'id' => $income->id,
            'amount' => 1500,
            'income_date' => '2025-10-19',
            'income_category_id' => $income_category->id,
        ]);
    }

    // 削除テスト
    public function test_income_can_be_deleted(): void
    {
        // ユーザー作成
        $user = User::factory()->create();
        $this->actingAs($user);

        // カテゴリー作成
        $income_category = IncomeCategory::factory()->create([
            'name' => '給与',
        ]);

        // 既存データを作成
        $income = Income::factory()->create([
            'user_id' => $user->id,
            'income_category_id' => $income_category->id,
            'amount' => 1000,
            'income_date' => '2025-10-18',
        ]);

        // 既存データを削除
        $response = $this->delete("/incomes/{$income->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('incomes', [
            'id' => $income->id,
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
