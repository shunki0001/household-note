<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use PHPUnit\Framework\Attrubutes\Test;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Category;
use Tests\Traits\CreatesExpenses;
use Tests\Traits\CreateUsers;

class AuthAccessTest extends TestCase
{
    use RefreshDatabase;
    use CreateUsers;
    use CreatesExpenses;

    // 共通化
    // モデル作成 + HTTPメソッド共通化
    private function assert_forbidden_for_other_user($method, $url, $data = [])
    {
        $response = $this->{$method}($url, $data);
        $response->assertStatus(403);
    }

    // Expense/Incomeの作成
    private function create_other_users_record($model, $attributes = [])
    {
        $userB = User::factory()->create();
        return $model::factory()->create(array_merge(['user_id' => $userB->id], $attributes));
    }

    /**
     * ログイン中にアクセス可能なページに全てアクセス
     *
     * 手順
     * - ユーザーA作成 + ログイン
     * - ログイン中にアクセス可能なルート一覧を定義
     * - 定義したルート一覧に全てアクセス -> アクセス可(200)
     */
    public function test_login_user_can_access_all_pages(): void
    {
        // ログイン中の状態を作る
        $this->createLoginUser();

        // ログイン中にアクセス可能なルート一覧
        $pages = [
            '/dashboard',
            '/list',
            '/graph/monthly',
            '/graph/category',
            '/profile',
        ];

        foreach ($pages as $page) {
            $response = $this->get($page);
            $response->assertStatus(200, "{$page} にアクセスできませんでした");
        }
    }

    /**
     * 支出の削除時に他のユーザーが操作できないか
     *
     * 手順
     * - ユーザーA,B作成 + ユーザーAログイン
     * - ユーザーBで任意の支出データを登録
     */
    public function test_other_user_cannot_delete_expense(): void
    {
        ['userA' => $userA, 'userB' => $userB] = $this->createLoginUsers();

        $expense = $this->createExpense(1000, '2025-11-14', $userB->id);

        $this->assert_forbidden_for_other_user(
            'delete',
            "/transactions/expense/{$expense->id}"
        );
    }

    /**
     * 収入の削除時に他のユーザーが操作できないか
     *
     * 手順
     * - ユーザーA,B作成 + ユーザーAログイン
     * - ユーザーBで任意の収入データを登録
     * - AがBのデータを削除処理 -> アクセス拒否チェック
     */
    public function test_other_user_cannot_delete_income(): void
    {
        ['userA' => $userA, 'userB' => $userB] = $this->createLoginUsers();

        $income = $this->create_other_users_record(Income::class);

        $this->assert_forbidden_for_other_user(
            'delete',
            "/transactions/income/{$income->id}"
        );
    }

    /**
     * 支出の編集時に他のユーザーが操作できないか
     *
     * 手順
     * - ユーザーA,B作成 + ユーザーAログイン
     * - カテゴリー作成
     * - ユーザーBの任意の支出データ作成
     * - ユーザーAがBのデータを編集 ->アクセス拒否
     */
    public function test_other_user_cannot_edit_expense(): void
    {
        ['userA' => $userA, 'userB' => $userB] = $this->createLoginUsers();

        $category = Category::factory()->create([
            'name' => '食費'
        ]);

        $expense = $this->create_other_users_record(Expense::class);

        $this->assert_forbidden_for_other_user(
            'put',
            "expenses/{$expense->id}",
            [
                'amount' => 99999,
                'title' => 'test',
                'date' =>  '2025-11-14',
                'category_id' => $category->id,
            ]
            );
    }

    /**
     * 収入の編集時に他のユーザーが操作できないか
     *
     * 手順
     * - ユーザーA,B作成 + ユーザーAログイン
     * - カテゴリー作成
     * - ユーザーBの任意の収入データ作成
     * - ユーザーAがBのデータを編集 ->アクセス拒否
     */
    public function test_other_user_cannot_edit_income(): void
    {
        ['userA' => $userA, 'userB' => $userB] = $this->createLoginUsers();

        $income_category = Category::factory()->create([
            'name' => '給与'
        ]);

        $income = $this->create_other_users_record(Income::class);

        $this-> assert_forbidden_for_other_user(
            'put',
            "incomes/{$income->id}",
            [
                'amount' => 1000,
                'income_date' => '2025-11-13',
                'income_category_id' => $income_category->id,
            ]
            );
    }
}
