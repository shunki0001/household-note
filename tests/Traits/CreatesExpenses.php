<?php

namespace Tests\Traits;

use App\Models\Expense;

trait CreatesExpenses
{
    /**
    * 支出のテストデータ作成
    *
    * @param int $user: ユーザー
    * @param int $amount: 金額
    * @param date $date: 日付(Y-m-d) default: today
    * @param int category_id: カテゴリーID default: null
    * @param string title: タイトル default: null
    */
    protected function createExpense(int $amount, string $date, int $userId, ?int $category_id = null, ?string $title = null): Expense
    {
        $data = [
            'amount' => $amount,
            'date' =>  $date,
            'user_id' => $userId,
        ];

        // category_idが指定された時だけ追加する
        if (!is_null($category_id)) {
            $data['category_id'] = $category_id;
        }

        // titleが指定された時だけ追加する
        if (!is_null($title)) {
            $data['title'] = $title;
        }

        return Expense::factory()->create($data);
    }
}
