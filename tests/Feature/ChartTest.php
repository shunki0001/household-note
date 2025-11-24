<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use App\Models\Category;
use Tests\Traits\CreateUsers;

class ChartTest extends TestCase
{
    use RefreshDatabase;
    use CreateUsers;

    // 月別支出合計グラフテスト
    /**
     * ChartController@getMonthlyExpenseTotal の次のパターンの整合性を担保する
     * 1. データが全くない場合: APIの初期状態が正しく保証される
     * 2. データが1件だけ->対応月のみが反映: １ヶ月分の計算ロジックが正しい。ラベルの並び・配列の長さも保証
     * 3. 同じ月に複数データ->合計されるか: 集計ロジックの核の部分。月別集計処理の計算が正しいか
     * 4. 別ユーザーのデータが混じっても集計されない: マルチユーザーアプリにおいて整合性を保証
     *
     * テストするAPIは '/api/chat-data'
     */

    // ========================================================================================

    /**
     * 共通化: labelsの1~12月を定義
     */
    private array $labels =[
        "1月", "2月", "3月", "4月", "5月", "6月",
        "7月", "8月", "9月", "10月", "11月", "12月"
    ];

    /**
     * 共通化: API呼び出し + ステータス確認
     */
    private function getAndAsset(string $url)
    {
        $response = $this->get($url);
        $response->assertStatus(200);
        return $response;
    }

    /**
     * 共通化: カテゴリーの作成
     */
    private function createCategories()
    {
        $categories = [];

        foreach ($this->categoryNames as $i => $name) {
            $categories[$name] = Category::factory()->create([
                'name' => $name,
                'sort_order' => $i + 1,
            ]);
        }
        return $categories;
    }

    /**
     * 共通化: 期待値
     */
    public function assertMonthlyResponse($response, array $expected)
    {
        $response->assertJson([
            'labels' => $this->labels,
            'totals' => $expected
        ]);
    }

    /**
     * 共通化: 12ヶ月分の合計を作成
     * $value = ['10' => 1000]のように渡す
     */
    private function makeTotals(array $values = []): array
    {
        // １２ヶ月全て0で初期化
        $totals = array_fill(0, 12, 0);

        // 渡された月だけ値を上書き
        foreach($values as $month => $amount) {
            // $monthを1~12として扱う
            $totals[$month - 1] = $amount;
        }

        return $totals;
    }

    /**
     * 共通化:
     */
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

    /**
     * 共通化: 現在の年月を取得
     */
    private function nowDate()
    {
        return now()->format('Y-m-d');
    }

    // 1. データが全て0のパターンのテスト
    public function test_returns_zero_for_all_months_when_no_data(): void
    {
        // ユーザー作成 + ログイン
        $user = $this->createLoginUser();

        $response = $this->getAndAsset('/api/chart-data');

        // 期待するデータ
        $response->assertJson([
            'labels' => $this->labels,
            'totals' => $this->makeTotals(),
        ]);
    }

    // 2. データが1件だけのテスト
    public function test_single_expense_is_reflected_in_correct_month(): void
    {
        // ユーザー作成 + ログイン
        $user = $this->createLoginUser();

        // テストデータをDBに登録
        Expense::factory()->create(['amount' => 1000, 'date'=> '2025-10-12', 'user_id' => $user->id]);

        $response = $this->get('/api/chart-data');

        // 期待するデータ
        $response->assertJson([
            'labels' => $this->labels,
            'totals' => $this->makeTotals([
                10 => 1000, // 10月 1000円
            ]),
        ]);

    }

    // 3. 同じ月に複数データ登録されているかテスト
    public function test_monthly_total_is_calculated_correctly(): void
    {
        // ユーザー登録 + ログイン
        $user = $this->createLoginUser();

        // 同じ年月にデータを2つ登録
        Expense::factory()->create(['amount' => 1000, 'date' => '2025-11-14', 'user_id' => $user->id ]);
        Expense::factory()->create(['amount' => 2000, 'date' => '2025-11-13', 'user_id' => $user->id ]);

        $response = $this->get('/api/chart-data');

        // 期待するデータ
        $response->assertJson([
            'labels' => $this->labels,
            'totals' => $this->makeTotals([
                11 => 3000,
            ]),
        ]);
    }

    // 4. 別ユーザーのデータが混じっても集計されないかテスト
    public function test_other_users_expenses_are_not_included(): void
    {
        // ユーザーA,Bを作成 + ユーザーAログイン
        ['userA' => $userA, 'userB' => $userB] = $this->createLoginUsers();

        // 各ユーザーが支出を1つ登録
        Expense::factory()->create(['amount' => 1000, 'date' => '2025-11-14', 'user_id' => $userA->id ]);
        Expense::factory()->create(['amount' => 2000, 'date' => '2025-10-13', 'user_id' => $userB->id ]);

        $response = $this->get('/api/chart-data');

        // ユーザーAが登録したデータが反映されているか
        $response->assertJson([
            'labels' => $this->labels,
            'totals' => $this->makeTotals([
                11 => 1000,
            ]),
        ]);
    }


    // ========================================================================================

    // ChartController@getMonthlyIncomeTotals
    // フロントでは未実装
    // ======================================

    /**
     * ChartController@getCategoryExpenseTotals の次のパターンの整合性を担保する
     * 1. データが全くない場合: APIの初期状態が正しく保証される
     * 2. データが1件だけ->対応カテゴリーのみが反映: １カテゴリーの計算ロジックが正しい。ラベルの並び・配列の長さも保証
     * 3. 同じカテゴリーに複数データ->合計されるか: 集計ロジックの核の部分。カテゴリー別集計処理の計算が正しいか
     * 4. 別ユーザーのデータが混じっても集計されない: マルチユーザーアプリにおいて整合性を保証
     *
     * テストするAPIは '/api/chat-data/category-monthly-single'
     */

    // 1. データが全て0
    public function test_returns_zero_for_all_categories_when_no_data()
    {
        // ユーザー作成 + ログイン
        $user = $this->createLoginUser();

        // // 1カテゴリーずつ作成して $categories に保存
        $categories = $this->createCategories();

        // API呼び出し
        $response = $this->get('/api/chart-data/category-monthly-single');

        // ラベルが正しいか
        $response->assertJson([
            'labels' => $this->categoryNames,
        ]);

        // データが全て0か確認
        $response->assertJsonPath('datasets.0.label', '支出合計');
        for ($i = 1; $i < 10; $i++) {
            $response->assertJsonPath("datasets.0.data.$i", 0);
        }
    }

    // 2. データが1件だけ
    public function test_single_expense_is_reflected_in_correct_category(): void
    {
        // 既存のカテゴリをすべて削除（テストデータの分離のため）
        Category::query()->delete();

        // ユーザー作成 + ログイン
        $user = $this->createLoginUser();

        // // 1カテゴリずつ作成して $categories に保存
        $categories = $this->createCategories();

       // 支出登録（食費のみ）
        Expense::factory()->create([
            'amount' => 1000,
            'date' => '2025-11-14',
            'category_id' => $categories['食費']->id,
            'user_id' => $user->id,
        ]);

        // API 呼び出し
        $response = $this->get('/api/chart-data/category-monthly-single');

        // ラベル(カテゴリ名)が正しいか
        $response->assertJson([
            'labels' => $this->categoryNames,
        ]);

        // 食費 = 6000、それ以外は 0 を確認
        $response->assertJsonPath('datasets.0.label', '支出合計');
        $response->assertJsonPath('datasets.0.data.0', 1000); // 食費

        for ($i = 1; $i < 10; $i++) {
            $response->assertJsonPath("datasets.0.data.$i", 0);
        }
    }

    // 3. 同じカテゴリーに複数データが合計されるか
    public function test_category_monthly_single(): void
    {
        // 既存のカテゴリをすべて削除（テストデータの分離のため）
        Category::query()->delete();

        // ユーザー作成 + ログイン
        $user = $this->createLoginUser();

        // 1カテゴリずつ作成して $categories に保存
        $categories = $this->createCategories();

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

        $response = $this->get('/api/chart-data/category-monthly-single');

        // ラベル(カテゴリ名)が正しいか
        $response->assertJson([
            'labels' => $this->categoryNames,
        ]);

        // 食費 = 6000、それ以外は 0 を確認
        $response->assertJsonPath('datasets.0.label', '支出合計');
        $response->assertJsonPath('datasets.0.data.0', 6000); // 食費

        for ($i = 1; $i < 10; $i++) {
            $response->assertJsonPath("datasets.0.data.$i", 0);
        }
    }

    // 4. 別ユーザーが混じっても集計されない
    public function test_other_users_categories_are_not_included(): void
    {
        Category::query()->delete();

        ['userA' => $userA, 'userB' => $userB] = $this->createLoginUsers();

        $categories = $this->createCategories();

        // 支出登録(ユーザーA,B 1件ずつ)
        Expense::factory()->create([
            'amount' => 1500,
            'date' => '2025-11-16',
            'category_id' => $categories['食費']->id,
            'user_id' => $userA->id,
        ]);
        Expense::factory()->create([
            'amount' => 2000,
            'date' => '2025-11-15',
            'category_id' => $categories['日用品費']->id,
            'user_id' => $userB->id,
        ]);

        // API呼び出し
        $response = $this->get('/api/chart-data/category-monthly-single');

        // ラベル(カテゴリー名)が正しいか
        $response->assertJson([
            'labels' => $this->categoryNames,
        ]);

        // ユーザーAのデータ確認
        $response->assertJsonPath('datasets.0.label', '支出合計');
        $response->assertJsonPath('datasets.0.data.0', 1500);

        for ($i = 1; $i < 10; $i++) {
            $response->assertJsonPath("datasets.0.data.$i", 0);
        }
    }
    // ========================================

    /**
     * ChartController@doughnutGetCategoryExpenseTotals の次のパターンの整合性を担保する
     * 1. データが0件: APIの初期状態が正しく保証される
     * 2. データが1件だけ->対応カテゴリーのみが反映: １ヶ月分のロジックが正しい
     * 3. 同じ月に複数データ->合計されるか: 集計ロジックの核の部分。月別集計処理の計算が正しいか
     * 4. 別ユーザーのデータが混じっても集計されない: マルチユーザーにおいて整合性を保証
     *
     * API: '/api/chart-data/doughnut'
     */

    // 1. データが0件
    public function test_returns_zero_for_all_doughnut_categories_when_no_data(): void
    {
        // 既存のカテゴリーを全て削除
        Category::query()->delete();

        // ユーザー作成 + ログイン
        $user = $this->createLoginUser();

        // 現在の年月を取得
        // ['now' => $now, 'currentYear' => $currentYear, 'currentMonth' => $currentMonth, 'currentDate' => $currentDate] = $this->getMonthYear();
        $this->nowDate();

        $categories = $this->createCategories();

        // API呼び出し
        $response = $this->get('/api/chart-data/doughnut');

        // レスポンス形式の確認
        $response->assertJsonStructure([
            'labels',
            'totals',
            'colors',
        ]);

        // ラベル(カテゴリー名)が正しいか
        $response->assertJson([
            'labels' => $this->categoryNames,
        ]);

        // データが0を確認
        for ($i = 0; $i < 10; $i++) {
            $response->assertJsonPath("totals.$i", 0);
        }

        // 色の配列が正しく返されているか確認
        $response->assertJsonCount(10, 'colors');


    }

    // 2. データが1件
    public function test_single_expense_is_reflected_in_correct_category_doughnut(): void
    {
        Category::query()->delete();

        // ユーザー作成 + ログイン
        $user = $this->createLoginUser();

        // 現在の年月を取得（doughnutGetCategoryExpenseTotalsは現在の年月を使用）
        // ['now' => $now, 'currentYear' => $currentYear, 'currentMonth' => $currentMonth, 'currentDate' => $currentDate] = $this->getMonthYear();
        $this->nowDate();

        $categories = $this->createCategories();

        // 支出登録（食費: 1000円）
        Expense::factory()->create([
            'amount' => 1000,
            'date' => $this->nowDate(),
            'category_id' => $categories['食費']->id,
            'user_id' => $user->id,
        ]);

        // API呼び出し
        $response = $this->get('/api/chart-data/doughnut');

        // レスポンス形式の確認
        $response->assertJsonStructure([
            'labels',
            'totals',
            'colors',
        ]);

        // ラベル(カテゴリ名)が正しいか
        $response->assertJson([
            'labels' => $this->categoryNames,
        ]);

        // 食費 = 1000
        $response->assertJsonPath('totals.0', 1000); // 食費

        for ($i = 1; $i < 10; $i++) {
            $response->assertJsonPath("totals.$i", 0);
        }

        // 色の配列が正しく返されているか確認
        $response->assertJsonCount(10, 'colors');
    }

    // 3. 同じ月に複数データ
    public function test_doughnut_category_expense_totals(): void
    {
        // 既存のカテゴリをすべて削除（テストデータの分離のため）
        Category::query()->delete();

        // ユーザー作成 + ログイン
        $user = $this->createLoginUser();

        // 現在の年月を取得（doughnutGetCategoryExpenseTotalsは現在の年月を使用）
        // ['now' => $now, 'currentYear' => $currentYear, 'currentMonth' => $currentMonth, 'currentDate' => $currentDate] = $this->getMonthYear();
        $this->nowDate();

        $categories = $this->createCategories();

        // 支出登録（食費: 3000円、日用品費: 2000円、それ以外は0円）
        Expense::factory()->create([
            'amount' => 1000,
            'date' => $this->nowDate(),
            'category_id' => $categories['食費']->id,
            'user_id' => $user->id,
        ]);
        Expense::factory()->create([
            'amount' => 2000,
            'date' => $this->nowDate(),
            'category_id' => $categories['食費']->id,
            'user_id' => $user->id,
        ]);
        Expense::factory()->create([
            'amount' => 2000,
            'date' => $this->nowDate(),
            'category_id' => $categories['日用品費']->id,
            'user_id' => $user->id,
        ]);

        $response = $this->get('/api/chart-data/doughnut');

        // レスポンス形式の確認
        $response->assertJsonStructure([
            'labels',
            'totals',
            'colors',
        ]);

        // ラベル(カテゴリ名)が正しいか
        $response->assertJson([
            'labels' => $this->categoryNames,
        ]);

        // 食費 = 3000、日用品費 = 2000、それ以外は 0 を確認
        $response->assertJsonPath('totals.0', 3000); // 食費
        $response->assertJsonPath('totals.1', 2000); // 日用品費

        for ($i = 2; $i < 10; $i++) {
            $response->assertJsonPath("totals.$i", 0);
        }

        // 色の配列が正しく返されているか確認
        $response->assertJsonCount(10, 'colors');
    }

    // 4. 別ユーザーが混じっても集計されない
    public function test_other_users_categories_doughnut_are_not_included(): void
    {
        // 既存のカテゴリをすべて削除（テストデータの分離のため）
        Category::query()->delete();

        // ユーザー作成A,B + ログインA
        ['userA' => $userA, 'userB' => $userB] = $this->createLoginUsers();

        // 現在の年月を取得（doughnutGetCategoryExpenseTotalsは現在の年月を使用）
        // ['now' => $now, 'currentYear' => $currentYear, 'currentMonth' => $currentMonth, 'currentDate' => $currentDate] = $this->getMonthYear();
        $this->nowDate();

        $categories = $this->createCategories();

        // 支出登録 食費 A:1000円, B:2000円
        Expense::factory()->create([
            'amount' => 1000,
            'date' => $this->nowDate(),
            'category_id' => $categories['食費']->id,
            'user_id' => $userA->id,
        ]);
        Expense::factory()->create([
            'amount' => 2000,
            'date' => $this->nowDate(),
            'category_id' => $categories['食費']->id,
            'user_id' => $userB->id,
        ]);

        $response = $this->get('/api/chart-data/doughnut');

        // レスポンス形式の確認
        $response->assertJsonStructure([
            'labels',
            'totals',
            'colors',
        ]);

        // ラベル(カテゴリ名)が正しいか
        $response->assertJson([
            'labels' => $this->categoryNames,
        ]);

        // 食費 = 1000 それ以外は 0 を確認
        $response->assertJsonPath('totals.0', 1000); // 食費

        for ($i = 1; $i < 10; $i++) {
            $response->assertJsonPath("totals.$i", 0);
        }

        // 色の配列が正しく返されているか確認
        $response->assertJsonCount(10, 'colors');
    }

    // ========================================

}
