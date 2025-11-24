<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Income;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use App\Models\IncomeCategory;
use Tests\Traits\CreatesIncomes;

class IncomeTest extends TestCase
{
    use RefreshDatabase;
    use CreatesIncomes;

    // ユーザー作成 + ログイン + カテゴリー作成を共通化
     // ユーザー作成 + ログイン + カテゴリー作成を共通化
    /**
     * 共通化
     *
     * 実装内容
     * - ユーザー作成 + ログイン
     * - カテゴリー「給与」作成
     *
     * @param $user
     * @param $income_category
     */
    private function create_and_login_user(): array
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $income_category = IncomeCategory::factory()->create([
            'name' => '給与',
        ]);

        return compact('user', 'income_category');

    }

    /**
     * テストデータ作成
     * data -> 支出データ
     * errors -> エラーメッセージ
     * case -> エラーのパターン
     * テストケースに応じて各データを上書きして利用する
     *
     * @param int $income_categoryId
     * @param array $overrides
     * @param string $errors
     * @param string $case
     */
    private function invalidData($income_category_id, $overrides = [], $errors = [], $case = [])
    {
        return [
            'data' => array_merge([
                'amount' => 100,
                'income_date' => '2025-11-11',
                'income_category_id' => $income_category_id,
            ], $overrides),
            'errors' => $errors,
            'case' => $case,
        ];
    }

    /**
     * 収入追加テスト
     *
     * 手順
     * - ユーザー作成 + ログイン + カテゴリー作成
     * - 収入登録 + ステータスコード200チェック
     * - DBに保存されているか確認
     */
    public function test_user_can_store_income()
    {
        ['user' => $user, 'income_category' => $income_category] = $this->create_and_login_user();

        $response = $this->post('/incomes', [
            'amount' => '5000',
            'income_date' => '2025-09-20',
            'income_category_id' => $income_category->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('incomes', [
            'user_id' => $user->id,
            'amount' => 5000,
            'income_date' => '2025-09-20',
            'income_category_id' => $income_category->id,
        ]);
    }

    /**
     * 収入編集テスト
     *
     * 手順
     * - ユーザ作成 + ログイン + カテゴリー作成
     * - 任意の収入データ登録
     * - 更新処理
     * - 更新データがDBに登録されているか確認
     */
    public function test_income_can_be_updated(): void
    {
        ['user' => $user, 'income_category' => $income_category] = $this->create_and_login_user();

        // $income = Income::factory()->create([
        //     'user_id' => $user->id,
        //     'income_category_id' => $income_category->id,
        //     'amount' => 1000,
        //     'income_date' => '2025-10-18',
        // ]);
        $income = $this->createIncome(1000, '2025-10-18', $user->id, $income_category->id);

        $response = $this->put("incomes/{$income->id}", [
            'amount' => 1500,
            'income_date' => '2025-10-19',
            'income_category_id' => $income_category->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('incomes', [
            'id' => $income->id,
            'amount' => 1500,
            'income_date' => '2025-10-19',
            'income_category_id' => $income_category->id,
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
    public function test_income_can_be_deleted(): void
    {
        ['user' => $user, 'income_category' => $income_category] = $this->create_and_login_user();

        $income = $this->createIncome(1000, '2025-10-18', $user->id, $income_category->id);

        $response = $this->delete("/transactions/income/{$income->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('incomes', [
            'id' => $income->id,
        ]);
    }

    /**
     * バリデーション違反は登録できない
     *
     * 手順
     * - バリデーション登録リストを設定
     * - 収入登録
     * - バリデーションエラー、リダイレクトされるかチェック
     * - エラーメッセージを返す
     * - DBに登録されていないことを確認
     */
    public function test_new_income_cannot_register_with_invalid_data(): void
    {
        ['user' => $user, 'income_category' => $income_category] = $this->create_and_login_user();
        // 登録リスト
        $invalidDataSets = [
            $this->invalidData($income_category->id, ['amount' => ''], ['amount'], 'amount必須'),
            $this->invalidData($income_category->id, ['income_date' => ''], ['income_date'], 'income_date必須'),
            $this->invalidData($income_category->id, ['income_category_id' => ''], ['income_category_id'], 'income_category_idt必須'),
            $this->invalidData($income_category->id, ['amount' => -100], ['amount'], 'amountマイナスのパターン'),
            $this->invalidData($income_category->id, ['amount' => 100.23], ['amount'], 'amount小数'),
        ];

        foreach ($invalidDataSets as $set) {
            $response = $this->post('/incomes', $set['data']);

            $response->assertStatus(302);

            $response->assertSessionHasErrors($set['errors'], "失敗ケース: {$set['case']}");

            $this->assertDatabaseCount('incomes', 0);

        }
    }

}
