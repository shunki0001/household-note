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
            ['name' => '食費', 'icon_path' => 'images/icons/FoodExpenses32px.svg', 'sort_order' => 1],
            ['name' => '日用品費', 'icon_path' => 'images/icons/DailyGoods32px.svg', 'sort_order' => 2],
            ['name' => '交通費', 'icon_path' => 'images/icons/TransportationExpenses32px.svg', 'sort_order' => 3],
            ['name' => '住居費', 'icon_path' => 'images/icons/HousingExpenses32px.svg', 'sort_order' => 4],
            ['name' => '水道・光熱費', 'icon_path' => 'images/icons/UtilitiesExpenses32px.svg', 'sort_order' => 5],
            ['name' => '通信費', 'icon_path' => 'images/icons/CommunicationExpenses32px.svg', 'sort_order' => 6],
            ['name' => '医療・保険', 'icon_path' => 'images/icons/MedicalInsuranceExpenses32px.svg', 'sort_order' => 7],
            ['name' => '娯楽・交際費', 'icon_path' => 'images/icons/EntertainmentExpenses32px.svg', 'sort_order' => 8],
            ['name' => '教育費', 'icon_path' => 'images/icons/EducationExpenses32px.svg', 'sort_order' => 9],
            ['name' => 'その他', 'icon_path' => 'images/icons/OtherExpenses32px.svg', 'sort_order' => 10],
        ];

        foreach($categories as $category) {
            // 誤字や古い名前を削除(該当がある場合のみ)
            // Category::where('name', '娯楽・交通費')->delete();

            // 正しい名前とアイコンパスで登録または更新
            Category::updateOrCreate([
                'name' => $category['name']
            ], [
                'icon_path' => $category['icon_path'],
                'sort_order' => $category['sort_order'],
            ]);
        }
    }
}
