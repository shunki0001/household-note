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
        // ExpenseControllerから削除処理を実行
        // $response = $this->delete("/expenses/{$expense->id}");

        // TransactionControllerから削除処理を実行
        $response = $this->delete("/transactions/expense/{$expense->id}");

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

    // バリデーション違反は登録できない
    public function test_new_expense_cannot_register_with_invalid_data(): void
    {
        // ユーザー作成&ログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // カテゴリー作成
        $category = Category::factory()->create([
            'name' => '食費',
        ]);

        // 登録リスト
        $invaliDataSets = [
            [
                'data' => ['amount' => '', 'date' => '2025-11-09', 'title' => 'Test Title', 'category_id' => $category->id],
                'errors' => ['amount'], // amount必須チェック
                'case' => 'amount必須'
            ],
            [
                'data' => ['amount' => 100, 'date' => '', 'title' => 'Test Title', 'category_id' => $category->id],
                'errors' => ['date'], // date必須チェック
                'case' => 'date必須'
            ],
            [
                'data' => ['amount' => 100, 'date' => '2025-11-09', 'title' => '', 'category_id' => $category->id],
                'errors' => ['title'], // title必須チェック
                'case' => 'title必須'
            ],
            [
                'data' => ['amount' => 100, 'date' => '2025-11-09', 'title' => 'Test Title', 'category_id' => ''],
                'errors' => ['category_id'], // category_id必須チェック
                'case' => 'category_id必須'
            ],
            [
                'data' => ['amount' => -100, 'date' => '2025-11-09', 'title' => 'Test Title', 'category_id' => $category->id],
                'errors' => ['amount'], // amount不正値チェック
                'case' => 'amountにマイナスが入力'
            ],
            [
                'data' => ['amount' => 100.32, 'date' => '2025-11-09', 'title' => 'Test Title', 'category_id' => $category->id],
                'errors' => ['amount'], // amount不正値チェック
                'case' => 'amountに少数が入力'
            ],
        ];

        foreach ($invaliDataSets as $set) {
            $response = $this->post('/expenses', $set['data']);

            // バリデーションエラー時はリダイレクト
            $response->assertStatus(302);

            // 各フィールドに対してエラーメッセージが返されているか確認
            $response->assertSessionHasErrors($set['errors'], "失敗ケース: {$set['case']}");

            // データベースに登録されていないことを確認
            $this->assertDatabaseCount('expenses', 0);
        }

    }


    // 必須項目が空欄の場合にエラーとなることを確認するテスト
public function test_expense_cannot_be_stored_with_empty_fields()
{
    // ユーザー作成＆ログイン
    $user = User::factory()->create();
    $this->actingAs($user);

    // カテゴリ作成
    $category = Category::factory()->create([
        'name' => '食費',
    ]);

    // 必須項目を空欄でPOST
    $response = $this->post('/expenses', [
        'amount' => '',        // 空欄
        'date' => '',          // 空欄
        'title' => '',         // 空欄
        'category_id' => '',   // 空欄
    ]);

    // バリデーションエラー時はリダイレクト(302)
    $response->assertStatus(302);

    // 各フィールドに対してエラーメッセージが返されているか確認
    $response->assertSessionHasErrors(['amount', 'date', 'title', 'category_id']);

    // データベースに登録されていないことを確認
    $this->assertDatabaseCount('expenses', 0);
}

}
