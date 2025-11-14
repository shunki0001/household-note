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

class AuthAccessTest extends TestCase
{
    use RefreshDatabase;

    // 未ログインユーザーはダッシュボードにアクセスできずログインんページにリダイレクトされる
    public function test_not_login_user_cannot_access_page(): void
    {
        $response = $this->get('/dashboard');
        // $response = $this->get('/list');

        $response->assertRedirect('/login'); // ログインページにリダイレクトされるか確認
    }

    // ログイン中にアクセス可能なページに全てアクセス
    public function test_login_user_can_access_all_pages(): void
    {
        $user = User::factory()->create();

        // ログイン中の状態を作る
        $this->actingAs($user);

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
    // 他ユーザーのデータにアクセスできない事をテスト ==

    // 共通化
    // ユーザーAとユーザーBを作成 + ユーザーAがログイン
    private function create_users(): array
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $this->actingAs($userA);

        return compact('userA', 'userB');
    }

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

    // 支出の削除時に他のユーザーが操作できないか
    public function test_other_user_cannot_delete_expense(): void
    {
        // ユーザーA,B作成 + ユーザーAログイン
        ['userA' => $userA, 'userB' => $userB] = $this->create_users();

        // ユーザーBが作成したデータ
        $expense = $this->create_other_users_record(Expense::class, [
            'user_id' => $userB->id,
            'amount' => 1000,
            'date' => '2025-11-14',
            'title' => 'Test Title',
        ]);

        // 削除処理 + アクセス拒否チェック
        $this->assert_forbidden_for_other_user(
            'delete',
            "/transactions/expense/{$expense->id}"
        );
    }

    // 収入の削除時に他のユーザーが操作できないか
    public function test_other_user_cannot_delete_income(): void
    {
        // ユーザーA,B作成 + ユーザーAログイン
        ['userA' => $userA, 'userB' => $userB] = $this->create_users();

        // ユーザーBが作成したデータ
        $income = $this->create_other_users_record(Income::class);

        // 削除処理 + アクセス拒否チェック
        $this->assert_forbidden_for_other_user(
            'delete',
            "/transactions/income/{$income->id}"
        );
    }

    // 支出の編集時に他のユーザーが操作できないか
    public function test_other_user_cannot_edit_expense(): void
    {
        // ユーザーA,B作成
        ['userA' => $userA, 'userB' => $userB] = $this->create_users();

        // カテゴリーデータ作成
        $category = Category::factory()->create([
            'name' => '食費'
        ]);

        // ユーザーBのデータ作成
        $expense = $this->create_other_users_record(Expense::class);

        // ユーザーAがユーザーBのデータを編集 -> アクセス拒否
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

    // 収入の編集時に他のユーザーが操作できないか
    public function test_other_user_cannot_edit_income(): void
    {
        // ユーザーA,B作成
        ['userA' => $userA, 'userB' => $userB] = $this->create_users();

        // カテゴリー作成
        $income_category = Category::factory()->create([
            'name' => '給与'
        ]);

        // ユーザーBのデータ作成=====
        $income = $this->create_other_users_record(Income::class);

        // ユーザーAがユーザーBのデータ編集 -> アクセス拒否  ==
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
