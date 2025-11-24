<?php

namespace Tests\Traits;

use App\Models\IncomeCategory;
use App\Models\Income;

trait CreatesIncomeCategories
{
    // 収入カテゴリー一覧
    private array $incomeCategoryNames = [
        '給与',
        '賞与',
        '副業収入',
        'ポイント収入',
        '臨時収入',
        'その他',
    ];

    // カテゴリー登録(収入)
    private function createIncomeCategory()
    {
        $income_categories = [];

        foreach($this->incomeCategoryNames as $i => $name) {
            $income_categories[] = IncomeCategory::factory()->create([
                'name' => $name,
            ]);
        }
        return $income_categories;
    }
}
