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

    // ユーザー作成 + ログイン + カテゴリー作成を共通化
    private function create_and_login_user(): array
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::factory()->create([
            'name' => '食費',
        ]);

        return compact('user', 'category'); // ['user' => $user, 'category' => $category]
    }

     // テストデータを関数化
    // defaultの値を上書きして使う
    private function invalidData($categoryId, $overides = [], $errors = [], $case = [])
    {
        // カテゴリーが消えないように定義しておく
        /* $category = Category::factory()->create(); */

        return [
            'data' => array_merge([
                'amount' => 100,
                'date' => '2025-11-09',
                'title' => 'Test Title',
                'category_id' => $categoryId,
            ], $overides),
            'errors' => $errors,
            'case' => $case,
        ];
    }


    // 追加テスト
    public function test_expense_can_be_stored()
    {
        // ユーザー作成 + カテゴリー作成
        ['user' => $user, 'category' => $category] = $this->create_and_login_user();

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

        ['user' => $user, 'category' => $category] = $this->create_and_login_user();

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
        ['user' => $user, 'category' => $category] = $this->create_and_login_user();
        // 既存データを作成
        $expense = Expense::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 1000,
            'title' => 'ランチ',
            'date' => '2025-10-15',
        ]);


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
        ['user' => $user, 'category' => $category] = $this->create_and_login_user();
        // 登録リスト
        $invaliDataSets = [
            $this->invalidData($category->id, ['amount' => ''], ['amount'], 'amount必須'),
            $this->invalidData($category->id, ['date' => ''], ['date'], 'date必須'),
            $this->invalidData($category->id, ['title' => ''], ['title'], 'title必須'),
            $this->invalidData($category->id, ['category_id' => ''], ['category_id'], 'category_id必須'),
            $this->invalidData($category->id, ['amount' => -100], ['amount'], 'amoountマイナスのパターン'),
            $this->invalidData($category->id, ['amount' => 100.123], ['amount'], 'amount小数チェック'),
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

        ['user' => $user, 'category' => $category] = $this->create_and_login_user();

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
