<?php

namespace Tests\Traits;

use App\Models\Category;

trait CreatesCategories
{
    // 支出カテゴリー一覧
    protected array $categoryNames =[
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

    // カテゴリーの作成
    protected function createCategories()
    {
        $categories = [];

        foreach ($this->categoryNames as $i => $name) {
            $category = Category::factory()->create([
                'name' => $name,
                'sort_order' => $i + 1,
            ]);

            $categories[$name] = $category;
        }
        return $categories;
    }
}
