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

    // ユーザー作成 + ログイン + カテゴリー作成を共通化
    private function create_and_login_user(): array
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $income_category = IncomeCategory::factory()->create([
            'name' => '給与',
        ]);

        return compact('user', 'income_category');

    }

    // ========テストデータを関数化 ============
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


    public function test_user_can_store_income()
    {
        ['user' => $user, 'income_category' => $income_category] = $this->create_and_login_user();

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
        ['user' => $user, 'income_category' => $income_category] = $this->create_and_login_user();
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
        ['user' => $user, 'income_category' => $income_category] = $this->create_and_login_user();

        // 既存データを作成
        $income = Income::factory()->create([
            'user_id' => $user->id,
            'income_category_id' => $income_category->id,
            'amount' => 1000,
            'income_date' => '2025-10-18',
        ]);

        // 既存データを削除
        $response = $this->delete("/transactions/income/{$income->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('incomes', [
            'id' => $income->id,
        ]);
    }

    // ======== バリデーション違反は登録できない ========
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

            // バリデーションエラー時はリダイレクト
            $response->assertStatus(302);

            // 各フィールドに対してエラーメッセージが返されているか確認
            $response->assertSessionHasErrors($set['errors'], "失敗ケース: {$set['case']}");

            // データベースに登録されていないことを確認
            $this->assertDatabaseCount('incomes', 0);

        }
    }

}
