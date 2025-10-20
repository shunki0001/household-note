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

    // 追加テスト
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

    // 編集テスト
    public function test_expense_can_be_updated(): void
    {
        // ユーザー作成
        $user = User::factory()->create();
        $this->actingAs($user);

        // カテゴリー作成
        $category = Category::factory()->create([
            'name' => '食費',
        ]);

        // 既存データを作成
        $expense = Expense::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 1000,
            'title' => 'ランチ',
            'date' => '2025-10-15',
        ]);

        // PUTリクエストで更新
        $response = $this->put("expenses/{$expense->id}", [
            'amount' => 1500,
            'title' => 'ディナー',
            'date' => '2025-10-16',
            'category_id' => $category->id,
        ]);

        $response->assertStatus(200);

        // データベースに更新後のデータがあるか確認
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'amount' => 1500,
            'title' => 'ディナー',
            'date' => '2025-10-16',
            'category_id' => $category->id,
        ]);
    }

    // 削除テスト
    public function test_expense_can_be_deleted(): void
    {
        // ユーザー作成
        $user = User::factory()->create();
        $this->actingAs($user);

        // カテゴリー作成
        $category = Category::factory()->create([
            'name' => '食費',
        ]);

        // 既存データを作成
        $expense = Expense::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 1000,
            'title' => 'ランチ',
            'date' => '2025-10-15',
        ]);

        // 既存データを削除
        $response = $this->delete("/expenses/{$expense->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('expenses', [
            'id' => $expense->id,
        ]);
    }


    // データベース接続テスト
    public function test_database_connection()
    {
        dump(\DB::connection()->getDatabaseName()); // 接続DBを確認
        $this->assertEquals('testing', \DB::connection()->getDatabaseName());
    }
}
