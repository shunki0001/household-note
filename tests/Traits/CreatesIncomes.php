<?php

namespace Tests\Traits;

use App\Models\Income;

trait CreatesIncomes
{
    /**
     * 収入登録用のテストデータを作成
     *
     * @param int $amount: 金額
     * @param string $date: 日付
     * @param int $userId: ユーザーID
     * @return Income
     */
    private function createUserWithIncomes(int $amount, string $date, int $userId): Income
    {
        return Income::factory()->create([
            'amount' => $amount,
            'income_date' => $date,
            'user_id' => $userId
        ]);
    }

    /**
     * 収入登録用のテストデータ作成(拡張版)
     *
     * 基本設定
     * @param int $user_id: ユーザーID
     * @param int $amount: 金額
     * @param string $income_date: 日付
     * @return Income
     *
     * 追加項目($overrides)
     * @param int $income_category_id: カテゴリーID
     */
    protected function createIncome(int $amount, string $income_date, int $userId, ?int $income_category_id = null): Income
    {
        $data = [
            'amount' => $amount,
            'income_date' => $income_date,
            'user_id' => $userId,
        ];

        // income_category_idが指定された時だけ追加する
        if (!is_null($income_category_id)) {
            $data['income_category_id'] = $income_category_id;
        }

        return Income::factory()->create($data);
    }
}
