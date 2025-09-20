<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabaseState;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_expense_can_be_stored()
    {
        // ユーザーを作成して認証
        $user = User::factory()->create();
        $this->actingAs($user);

        // カテゴリーを作成
        $category = Category::factory()->create([
            'name' => '食費',
        ]);

        // APIにPOSTリスエスト
        $response = $this->post('/expenses', [
            'amount' => 1000,
            'date' => '2025-09-13',
            'title' => 'ランチ',
            'category_id' => $category->id, // 作成したカテゴリーのIDを使用
        ]);

        // ステータスコード200かチェック
        $response->assertStatus(200);

        // データベースに保存されているか確認
        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'date' => '2025-09-13',
            'title' => 'ランチ',
            'category_id' => $category->id,
            'amount' => 1000,
        ]);
    }
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_database_connection()
    {
        dump(\DB::connection()->getDatabaseName()); // 接続DBを確認
        $this->assertEquals('testing', \DB::connection()->getDatabaseName());
    }
}
