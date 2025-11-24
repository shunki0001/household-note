<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Tests\Traits\CreatesExpenses;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;
    use CreatesExpenses;

    // ユーザー作成 + ログイン + カテゴリー作成を共通化
    /**
     * 共通化
     *
     * 実装内容
     * - ユーザー作成 + ログイン
     * - カテゴリー「食費」作成
     *
     * @param $user
     * @param $category
     */
    private function create_and_login_user(): array
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::factory()->create([
            'name' => '食費',
        ]);

        return compact('user', 'category'); // ['user' => $user, 'category' => $category]
    }

    /**
     * テストデータ作成
     * data -> 支出データ
     * errors -> エラーメッセージ
     * case -> エラーのパターン
     * テストケースに応じて各データを上書きして利用する
     *
     * @param int $categoryId
     * @param array $overrides
     * @param string $errors
     * @param string $case
     */
    private function invalidData($categoryId, $overrides = [], $errors = [], $case = [])
    {
        return [
            'data' => array_merge([
                'amount' => 100,
                'date' => '2025-11-09',
                'title' => 'Test Title',
                'category_id' => $categoryId,
            ], $overrides),
            'errors' => $errors,
            'case' => $case,
        ];
    }

    /**
     * 支出追加テスト
     *
     * 手順
     * - ユーザー作成 + ログイン + カテゴリー作成
     * - 支出登録 + ステータスコード200チェック
     * - DBに保存されているか確認
     */
    public function test_expense_can_be_stored()
    {
        ['user' => $user, 'category' => $category] = $this->create_and_login_user();

        $response = $this->post('/expenses', [
            'amount' => 1000,
            'date' => '2025-09-13',
            'title' => 'ランチ',
            'category_id' => $category->id, // 作成したカテゴリーのIDを使用
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'date' => '2025-09-13',
            'title' => 'ランチ',
            'category_id' => $category->id,
            'amount' => 1000,
        ]);
    }

    /**
     * 支出編集テスト
     *
     * 手順
     * - ユーザ作成 + ログイン + カテゴリー作成
     * - 任意の支出データ登録
     * - 更新処理
     * - 更新データがDBに登録されているか確認
     */
    public function test_expense_can_be_updated(): void
    {

        ['user' => $user, 'category' => $category] = $this->create_and_login_user();

        $expense = $this->createExpense(1000, '2025-10-15', $user->id, $category->id, 'ランチ');

        $response = $this->put("expenses/{$expense->id}", [
            'amount' => 1500,
            'title' => 'ディナー',
            'date' => '2025-10-16',
            'category_id' => $category->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'amount' => 1500,
            'title' => 'ディナー',
            'date' => '2025-10-16',
            'category_id' => $category->id,
        ]);
    }

    /**
     * 支出削除テスト
     *
     * 手順
     * - ユーザー作成 + ログイン + カテゴリー作成
     * - 支出データ登録
     * - 削除処理を呼び出し
     * - DBにデータが削除されているか確認
     */
    public function test_expense_can_be_deleted(): void
    {
        ['user' => $user, 'category' => $category] = $this->create_and_login_user();

        $expense = $this->createExpense(1000, '2025-10-15', $user->id, $category->id, 'ランチ');

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

    /**
     * バリデーション違反は登録できない
     *
     * 手順
     * - バリデーション登録リストを設定
     * - 支出登録
     * - バリデーションエラー、リダイレクトされるかチェック
     * - エラーメッセージを返す
     * - DBに登録されていないことを確認
     */
    public function test_new_expense_cannot_register_with_invalid_data(): void
    {
        ['user' => $user, 'category' => $category] = $this->create_and_login_user();
        // 登録リスト
        $invalidDataSets = [
            $this->invalidData($category->id, ['amount' => ''], ['amount'], 'amount必須'),
            $this->invalidData($category->id, ['date' => ''], ['date'], 'date必須'),
            $this->invalidData($category->id, ['title' => ''], ['title'], 'title必須'),
            $this->invalidData($category->id, ['category_id' => ''], ['category_id'], 'category_id必須'),
            $this->invalidData($category->id, ['amount' => -100], ['amount'], 'amountマイナスのパターン'),
            $this->invalidData($category->id, ['amount' => 100.123], ['amount'], 'amount小数チェック'),
        ];

        foreach ($invalidDataSets as $set) {
            $response = $this->post('/expenses', $set['data']);

            $response->assertStatus(302);

            $response->assertSessionHasErrors($set['errors'], "失敗ケース: {$set['case']}");

            $this->assertDatabaseCount('expenses', 0);
        }

    }

    /**
     * 必須項目が空欄の場合にエラーとなることを確認
     *
     * 手順
     * - ユーザー作成 + ログイン + カテゴリー作成
     * - 支出項目を全て空白で登録
     * - エラー(302)が返されるか確認
     * - エラーメッセージが返されるか確認
     * - DBに登録されていないことを確認
     */
    public function test_expense_cannot_be_stored_with_empty_fields()
    {
        ['user' => $user, 'category' => $category] = $this->create_and_login_user();

        $response = $this->post('/expenses', [
            'amount' => '',
            'date' => '',
            'title' => '',
            'category_id' => '',
        ]);

        $response->assertStatus(302);

        $response->assertSessionHasErrors(['amount', 'date', 'title', 'category_id']);

        $this->assertDatabaseCount('expenses', 0);
    }


}
