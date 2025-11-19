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

class DashboardTest extends TestCase
{
    use RefreshDatabase;
    /**
     * DashboardControllerのロジックのテスト
     * @index: 初回表示
     * @getTotalExpense: 今月の合計支出を取得するAPI
     * @getTotalMonthlyIncome: 今月の合計収入を取得するAPI
     */

    private function createUserAndLogin()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    private function create_and_login_users(): array
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $this->actingAs($userA);

        return compact('userA', 'userB');
    }

    private function response_and_assert_json(string $url, $status = 200, string $total ,int $totalAmount)
    {
        $response = $this->get($url);
        $response->assertStatus($status);

        $response->assertJson([
            $total => $totalAmount
        ]);
    }

    // Dashboardの初回表示が正常に行われるか
    public function test_index()
    {
        // テストユーザー作成
        $user = User::factory()->create();

        // ダッシュボードページにアクセス
        $response = $this->actingAs($user)->get('/dashboard');

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

    // TODO categoriesの内容が正しいか
    public function test_dashboard_categories_content_is_correct()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // --- テストデータ ---
        $cat1 = Category::factory()->create([
            'name' => '食費',
            'sort_order' => 1,
        ]);
        $cat2 = Category::factory()->create([
            'name' => '日用品費',
            'sort_order' => 2,
        ]);
        $cat3 = Category::factory()->create([
            'name' => '交通費',
            'sort_order' => 3,
        ]);
        $cat4 = Category::factory()->create([
            'name' => '住居費',
            'sort_order' => 4,
        ]);
        $cat5 = Category::factory()->create([
            'name' => '水道・光熱費',
            'sort_order' => 5,
        ]);
        $cat6 = Category::factory()->create([
            'name' => '通信費',
            'sort_order' => 6,
        ]);
        $cat7 = Category::factory()->create([
            'name' => '医療・保険',
            'sort_order' => 7,
        ]);
        $cat8 = Category::factory()->create([
            'name' => '娯楽・交通費',
            'sort_order' => 8,
        ]);
        $cat9 = Category::factory()->create([
            'name' => '教育費',
            'sort_order' => 9,
        ]);
        $cat10 = Category::factory()->create([
            'name' => 'その他',
            'sort_order' => 10,
        ]);
        // --- 画面アクセス ---
        $response = $this->get('/dashboard');

        // --- Inertiaのpropsの中身検証 ---
        $response->assertInertia(fn ($page) =>
            $page->component('Dashboard')
                ->has('categories', 10) // 件数チェック
                ->where('categories.0.id', $cat1->id)
                ->where('categories.0.name', '食費')
                ->where('categories.0.sort_order', 1)
                ->where('categories.1.id', $cat2->id)
                ->where('categories.1.name', '日用品費')
                ->where('categories.1.sort_order', 2)
                ->where('categories.2.id', $cat3->id)
                ->where('categories.2.name', '交通費')
                ->where('categories.2.sort_order', 3)
                ->where('categories.3.id', $cat4->id)
                ->where('categories.3.name', '住居費')
                ->where('categories.3.sort_order', 4)
                ->where('categories.4.id', $cat5->id)
                ->where('categories.4.name', '水道・光熱費')
                ->where('categories.4.sort_order', 5)
                ->where('categories.5.id', $cat6->id)
                ->where('categories.5.name', '通信費')
                ->where('categories.5.sort_order', 6)
                ->where('categories.6.id', $cat7->id)
                ->where('categories.6.name', '医療・保険')
                ->where('categories.6.sort_order', 7)
                ->where('categories.7.id', $cat8->id)
                ->where('categories.7.name', '娯楽・交通費')
                ->where('categories.7.sort_order', 8)
                ->where('categories.8.id', $cat9->id)
                ->where('categories.8.name', '教育費')
                ->where('categories.8.sort_order', 9)
                ->where('categories.9.id', $cat10->id)
                ->where('categories.9.name', 'その他')
                ->where('categories.9.sort_order', 10)
        );
    }

    // TODO income_categoriesの内容が正しいか
    public function test_dashboard_income_categories_content_is_correct()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // --- テストデータ ---
        $in_cat1 = IncomeCategory::factory()->create([
            'name' => '給与'
        ]);
        $in_cat2 = IncomeCategory::factory()->create([
            'name' => '副収入'
        ]);

        // --- 画面アクセス ---
        $response = $this->get('/dashboard');

        // --- Inertia props の中身検証
        $response->assertInertia(fn ($page) =>
            $page->component('Dashboard')
                ->has('income_categories', 2)
                ->where('income_categories.0.id', $in_cat1->id)
                ->where('income_categories.0.name', '給与')
                ->where('income_categories.1.id', $in_cat2->id)
                ->where('income_categories.1.name', '副収入')
        );
    }

    // 今月の支出合計を取得するテスト
    public function test_calculate_total_expense()
    {
        // ユーザー作成
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $this->actingAs($userA);

        // 現在の年月を取得
        $now = now();
        $currentYear = $now->year;
        $currentMonth = $now->month;
        $currentDate = $now->format('Y-m-d');

        // 今月のデータ
        Expense::factory()->create([
            'amount' => 1000,
            'date' => $currentDate,
            'user_id' =>$userA->id
        ]);

        Expense::factory()->create([
            'amount' => 2000,
            'date' => $currentDate,
            'user_id' => $userA->id
        ]);

        // 別ユーザー(含まれない)
        Expense::factory()->create([
            'amount' => 99999,
            'date' => $currentDate,
            'user_id' => $userB->id
        ]);

        // 別月(含まれない)
        Expense::factory()->create([
            'amount' => 55555,
            'date' => '2025-10-01',
            'user_id' => $userA->id
        ]);

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
        // ユーザー作成
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $this->actingAs($userA);

        //現在の年月を取得
        $now = now();
        $currentYear = $now->year;
        $currentMonth = $now->month;
        $currentDate = $now->format('Y-m-d');

        // 今月のデータ
        Income::factory()->create([
            'amount' => 3000,
            'income_date' => $currentDate,
            'user_id' => $userA->id
        ]);
        Income::factory()->create([
            'amount' => 6000,
            'income_date' => $currentDate,
            'user_id' => $userA->id
        ]);

        // 別ユーザー(含まれない)
        Income::factory()->create([
            'amount' => 2000,
            'income_date' => $currentDate,
            'user_id' => $userB->id
        ]);

        /// 別月(含まれない)
        Income::factory()->create([
            'amount' => 3000,
            'income_date' => '2025-09-01',
            'user_id' => $userA->id
        ]);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) =>
            $page->component('Dashboard')
                ->where('totalIncome', 9000)
        );
    }

    // 今月の合計支出のAPI
    public function test_api_total_expense_returns_correct_value()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $this->actingAs($userA);

        Expense::factory()->create([
            'amount' => 1000,
            'date' => '2025-11-01',
            'user_id' => $userA->id
        ]);
        Expense::factory()->create([
            'amount' => 6000,
            'date' => '2025-11-04',
            'user_id' => $userA->id
        ]);

        // 別ユーザー(含まれない)
        Expense::factory()->create([
            'amount' => 2222,
            'date' => '2025-11-01',
            'user_id' => $userB->id
        ]);

        // 別月(含まれない)
        Expense::factory()->create([
            'amount' => 9999,
            'date' => '2025-12-01',
            'user_id' => $userA->id
        ]);

        $response = $this->get('/api/dashboard/total-expense');
        $response->assertStatus(200);

        // 期待するデータ
        $response->assertJson([
            'totalExpense' => 7000
        ]);

    }

    // 今月の合計収入のAPI
    public function test_api_total_income_returns_correct_value()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $this->actingAs($userA);

        Income::factory()->create([
            'amount' => 1000,
            'income_date' => '2025-11-01',
            'user_id' => $userA->id
        ]);
        Income::factory()->create([
            'amount' => 6000,
            'income_date' => '2025-11-04',
            'user_id' => $userA->id
        ]);

        // 別ユーザー(含まれない)
        Income::factory()->create([
            'amount' => 2222,
            'income_date' => '2025-11-01',
            'user_id' => $userB->id
        ]);

        // 別月(含まれない)
        Income::factory()->create([
            'amount' => 9999,
            'income_date' => '2025-12-01',
            'user_id' => $userA->id
        ]);

        $response = $this->get('/api/incomes/total-monthly-incomes');
        $response->assertStatus(200);

        // 期待するデータ
        $response->assertJson([
            'totalIncome' => 7000
        ]);
    }
}
