<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use App\Models\Category;

class ChartTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // 月別支出合計グラフテスト
    // ChartController@getMonthlyExpenseTotal の動作テスト
    public function test_monthly_total_is_calculated_correctly(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Expense::factory()->create(['amount' => 1000, 'date' => '2025-11-14', 'user_id' => $user->id ]);
        Expense::factory()->create(['amount' => 2000, 'date' => '2025-11-13', 'user_id' => $user->id ]);

        $response = $this->get('/api/chart-data');
        $response->assertJson([
            'labels' => [
                "1月", "2月", "3月", "4月", "5月", "6月",
                "7月","8月","9月","10月","11月","12月",
            ],
            'totals' => [
                0, // 1月
                0, // 2月
                0, // 3月
                0, // 4月
                0, // 5月
                0, // 6月
                0, // 7月
                0, // 8月
                0, // 9月
                0, // 10月
                3000, // 11月
                0, // 12月
            ]
        ]);
    }

    // ChartController@getMonthlyIncomeTotals
    // フロントでは未実装
    // ======================================

    // ChartController@getCategoryExpenseTotals
    // API: /api/chart-data/category-monthly-single
    // カテゴリー別合計の集計ロジックのテスト
    public function test_category_monthly_single(): void
    {
        // 既存のカテゴリをすべて削除（テストデータの分離のため）
        Category::query()->delete();

        // ユーザー作成 + ログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // 期待されるカテゴリ名リスト（labels と一致する必要がある）
        $categoryNames = [
            '食費',
            '日用品費',
            '交通費',
            '住居費',
            '水道・光熱費',
            '通信費',
            '医療・保険',
            '娯楽・交際費',
            '教育費',
            'その他',
        ];

        // カテゴリモデルを格納する配列（呼び出し用）
        $categories = [];

        // 1カテゴリずつ作成して $categories に保存
        foreach ($categoryNames as $i => $name) {
            $categories[$name] = Category::factory()->create([
                'name' => $name,
                'sort_order' => $i + 1,
            ]);
        }

        // 支出登録（食費のみ）
        Expense::factory()->create([
            'amount' => 1000,
            'date' => '2025-11-14',
            'category_id' => $categories['食費']->id,
            'user_id' => $user->id,
        ]);
        Expense::factory()->create([
            'amount' => 5000,
            'date' => '2025-11-13',
            'category_id' => $categories['食費']->id,
            'user_id' => $user->id,
        ]);

        // API 呼び出し
        $response = $this->get('/api/chart-data/category-monthly-single');

        // ステータス確認
        $response->assertStatus(200);

        // ラベル(カテゴリ名)が正しいか
        $response->assertJson([
            'labels' => $categoryNames,
        ]);

        // 食費 = 6000、それ以外は 0 を確認
        $response->assertJsonPath('datasets.0.label', '支出合計');
        $response->assertJsonPath('datasets.0.data.0', 6000); // 食費

        for ($i = 1; $i < 10; $i++) {
            $response->assertJsonPath("datasets.0.data.$i", 0);
        }
    }
    // ========================================

    //ChartController@doughnutGetCategoryExpenseTotals
    // API: /api/chart-data/doughnut
    // ========================================


}
