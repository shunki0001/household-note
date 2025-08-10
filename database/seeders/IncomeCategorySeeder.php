<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\IncomeCategory;

class IncomeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $income_categories = [
            '給与',
            '賞与',
            '副業収入',
            'ポイント収入',
            '臨時収入',
            'その他',
        ];

        foreach ($income_categories as $income_category) {
            IncomeCategory::create(['name' => $income_category]);
        }
    }
}
