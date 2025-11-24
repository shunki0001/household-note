<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use App\Models\Income;
use App\Models\IncomeCategory;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Traits\CreatesExpenses;
use Tests\Traits\CreatesIncomes;
use Tests\Traits\CreateUsers;

class DashboardTest extends TestCase
{
    use RefreshDatabase;
    use CreateUsers;
    use CreatesExpenses;
    use CreatesIncomes;
/**
 * DashboardController の機能全体を検証するテスト
 *
 * 以下の動作が仕様通りであることを確認する:
 *
 * - ダッシュボード初回表示が正常に行われること
 * - 支出カテゴリー (categories) が props に正しく渡されること
 * - 収入カテゴリー (income_categories) が props に正しく渡されること
 * - 今月の合計支出の計算ロジックが正しく動作すること
 * - 今月の合計収入の計算ロジックが正しく動作すること
 * - 今月の合計支出 API が正しい値を返すこと
 * - 今月の合計収入 API が正しい値を返すこと
 */


    /**
     * API + ステータス + 合計金額の確認
     *
     * @param string $url: ルート
     * @param int $status: ステータスコード
     * @param string $type: totalExpense or totalIncome
     * @param int $totalAmount: 合計金額
     */
    private function response_and_assert_json(string $url, int $status = 200, string $type ,int $totalAmount)
    {
        $response = $this->get($url);
        $response->assertStatus($status);

        $response->assertJson([
            $type => $totalAmount
        ]);
    }

    // 支出カテゴリー一覧
    private array $categoryNames =[
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

    // 収入カテゴリー一覧
    private array $incomeCategoryNames = [
        '給与',
        '賞与',
        '副業収入',
        'ポイント収入',
        '臨時収入',
        'その他',
    ];

    // カテゴリー登録(支出)
    private function create_expense_category()
    {
        $categories = [];

        foreach ($this->categoryNames as $i => $name) {
            $categories[] = Category::factory()->create([
                'name' => $name,
                'sort_order' => $i + 1,
            ]);
        }
        return $categories;
    }

    // カテゴリー登録(収入)
    private function create_income_category()
    {
        $income_categories = [];

        foreach($this->incomeCategoryNames as $i => $name) {
            $income_categories[] = IncomeCategory::factory()->create([
                'name' => $name,
            ]);
        }
        return $income_categories;
    }

    // 現在の日付取得
    private function nowDate(): string
    {
        return now()->format('Y-m-d');
    }

    // Dashboardの初回表示が正常に行われるか
    public function test_index()
    {
        // テストユーザー作成
        $user = $this->createLoginUser();
        // // ダッシュボードページにアクセス
        $response = $this->actingAs($user)->get('/dashboard');
        // $response = $this->get('/dashboard');

        // ステータスコード200を確認
        $response->assertStatus(200);

        // Inertiaレスポンスの確認
        $response->assertInertia(fn ($page) =>
            $page->component('Dashboard')
                ->has('categories')
                ->has('income_categories')
                ->has('totalExpense')
                ->has('totalIncome')
        );
    }

    // categoriesの内容が正しいか
    public function test_dashboard_categories_content_is_correct()
    {
        $this->createLoginUser();

        // カテゴリー10件作成
        $categories = $this->create_expense_category();

        // --- 画面アクセス ---
        $response = $this->get('/dashboard');

        // Inertiaの検証
        $response->assertInertia(fn ($page) =>
            $page->component('Dashboard')
                ->has('categories', count($this->categoryNames))
                ->where('categories', function ($cats) use ($categories) {

                    foreach ($cats as $i => $cat) {
                        $this->assertEquals($categories[$i]->id, $cat['id']);
                        $this->assertEquals($categories[$i]->name, $cat['name']);
                        $this->assertEquals($categories[$i]->sort_order, $cat['sort_order']);
                    }

                    return true;
                })
        );
    }

    // income_categoriesの内容が正しいか
    public function test_dashboard_income_categories_content_is_correct()
    {
        $this->createLoginUser();

        // カテゴリー作成
        $income_categories = $this->create_income_category();

        // --- 画面アクセス ---
        $response = $this->get('/dashboard');

        // --- Inertia props の中身検証
        $response->assertInertia(fn ($page) =>
            $page->component('Dashboard')
                ->has('income_categories', count($this->incomeCategoryNames))
                ->where('categories', function ($cats) use ($income_categories) {

                    foreach($cats as $i => $cat) {
                        $this->assertEquals($income_categories[$i]->id, $cat['id']);
                        $this->assertEquals($income_categories[$i]->id, $cat['name']);
                    }

                    return true;
                })
        );
    }

    // 今月の支出合計を取得するテスト
    public function test_calculate_total_expense()
    {
        // ユーザー A / B を作成し、Aでログイン
        ['userA' => $userA, 'userB' => $userB] = $this->createLoginUsers();

        /**
         * テストデータ作成
         *
         * - 今月の支出(ユーザーA): 1000 + 2000 = 3000 (期待値)
         * - 今月の支出(ユーザーB): 99999 (Aの値に影響なし)
         * - 先月の支出(ユーザーA): 55555 (今月分に含まれない)
         */
        $this->createExpense(1000, $this->nowDate(), $userA->id);
        $this->createExpense(2000, $this->nowDate(), $userA->id);
        $this->createExpense(99999, $this->nowDate(), $userB->id);
        $this->createExpense(55555, '2025-10-01', $userA->id);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) =>
            $page->component('Dashboard')
                ->where('totalExpense', 3000)
        );

    }

    // 今月の収入を取得するテスト
    public function test_calculate_total_income()
    {
        // ユーザー A / B を作成し、Aでログイン
        ['userA' => $userA, 'userB' => $userB] = $this->createLoginUsers();

        /**
         * テストデータ作成
         *
         * - 今月の収入(ユーザーA): 3000 + 6000 = 9000 (期待値)
         * - 今月の収入(ユーザーB): 2000 (Aの値に影響なし)
         * - 先月の収入(ユーザーA): 3000 (今月分に含まれない)
         */
        $this->createIncome(3000, $this->nowDate(), $userA->id);
        $this->createIncome(6000, $this->nowDate(), $userA->id);
        $this->createIncome(2000, $this->nowDate(), $userB->id);
        $this->createIncome(3000, '2025-09-01', $userA->id);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) =>
            $page->component('Dashboard')
                ->where('totalIncome', 9000)
        );
    }

    // 今月の合計支出を返すAPIのテスト
    public function test_api_total_expense_returns_correct_value()
    {
        // ユーザー A / B を作成し、Aでログイン
        ['userA' => $userA, 'userB' => $userB] = $this->createLoginUsers();

        // テストデータ
        /**
         * テストデータ作成
         *
         * - 今月の収入(ユーザーA): 1000 + 6000 = 7000 (期待値)
         * - 今月の収入(ユーザーB): 2222 (Aの値に影響なし)
         * - 先月の収入(ユーザーA): 4000 (今月分に含まれない)
         */
        $this->createExpense(1000, $this->nowDate(), $userA->id);
        $this->createExpense(6000, $this->nowDate(), $userA->id);
        $this->createExpense(2222, $this->nowDate(), $userB->id);
        $this->createExpense(4000, '2025-10-11', $userA->id);

        // APIレスポンスとJSONの値(totalExpense=7000)を検証
        $this->response_and_assert_json('/api/dashboard/total-expense', 200, 'totalExpense', 7000);

    }

    // 今月の合計収入を返すAPIのテスト
    public function test_api_total_income_returns_correct_value()
    {
        // ユーザー A / B を作成し、Aでログイン
        ['userA' => $userA, 'userB' => $userB] = $this->createLoginUsers();

        // テストデータ
        /**
         * テストデータ作成
         *
         * - 今月の収入(ユーザーA): 1000 + 6000 = 7000 (期待値)
         * - 今月の収入(ユーザーB): 1000 (Aの値に影響なし)
         * - 先月の収入(ユーザーA): 9999 (今月分に含まれない)
         */
        $this->createIncome(1000, $this->nowDate(), $userA->id);
        $this->createIncome(6000, $this->nowDate(), $userA->id);
        $this->createIncome(1000, $this->nowDate(), $userB->id);
        $this->createIncome(9999, '2025-10-01', $userA->id);

        // APIレスポンスとJSONの値(totalIncome=7000)を検証
        $this->response_and_assert_json('api/incomes/total-monthly-incomes', 200, 'totalIncome', 7000);
    }
}
