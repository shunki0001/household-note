<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            '食費',
            '日用品費',
            '交通費',
            '住居費',
            '水道・光熱費',
            '通信費',
            '医療・保険',
            '娯楽・交通費',
            '教育費',
            'その他',
        ];

        foreach($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}
