<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Expense;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ChartController extends Controller
{
    // 1~12月の月別支出合計データ
    public function getMonthlyExpenses()
    {
        $data = Expense::selectRaw('MONTH(date) as month, SUM(amount as total)')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

            return response()->json($data);
    }

    public function getMonthlyTotals()
    {
        // $data = [];
        // $months = [];

        // for ($month = 1; $month <= 12; $month++) {
        //     $total = DB::table('expenses')
        //         ->whereYear('date', 2025)
        //         ->whereMonth('date', $month)
        //         ->sum('amount');

        //         $data[] = (int) $total;
        //         $months[] = $month. '月';
        // }

        // return response()->json([
        //     'labels' => $months,
        //     'totals' => $data,
        // ]);

        // 月ラベル作成
        $month = [];
        for ($month = 1; $month <=12; $month++) {
            $months[] = $month . '月';
        }

        // SQL 1回で月毎の合計を取得
        $expenses = DB::table('expenses')
            ->selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->whereYear('date', 2025)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 月別合計を0で初期化
        $data = array_fill(1, 12, 0);

        // 集計結果を反映
        foreach ($expenses as $expense) {
            $data[$expense->month] = (int) $expense->total;
        }

        return response()->json([
            'labels' => $months,
            'totals' => array_values($data), // ０始まりにする
        ]);
    }
    public function getCategoryTotals() {
        // $data = [];
        // $labels = [];

        // // x軸の月ラベル
        // for ($month = 1; $month <= 12; $month++) {
        //     $labels[] = $month. '月';
        // }

        // // 全カテゴリを取得
        // $categories = Category::all();

        // foreach($categories as $category) {
        //     $monthlyTotals = [];

        //     for ($month = 1; $month <= 12; $month++) {
        //         $sum = DB::table('expenses')
        //             ->where('category_id', $category->id)
        //             ->whereYear('date', 2025)
        //             ->whereMonth('date', $month)
        //             ->sum('amount');

        //         $monthlyTotals[] = (int) $sum;
        //     }

        //     $datasets[] = [
        //         'label' => $category->name,
        //         'data' => $monthlyTotals,
        //     ];
        // }

        // return response()->json([
        //     'labels' => $labels,
        //     'datasets' => $datasets,
        // ]);

        // パフォーマンス最適化バージョン

        // 月ラベル作成
        $labels = [];
        for ($month = 1; $month <= 12; $month++) {
            $labels[] = $month . '月';
        }

        // カテゴリ情報（id => name のマップ）
        $categories = Category::pluck('name', 'id'); // 例: [1 => '食費', 2 => '交通費', ...]

        // 一括集計（SQL発行はこの1回のみ）
        $expenses = Expense::selectRaw('category_id, MONTH(date) as month, SUM(amount) as total')
            ->whereYear('date', 2025)
            ->groupBy('category_id', 'month')
            ->get();

        // カテゴリごとにデータを初期化
        $datasets = [];

        foreach ($categories as $categoryId => $categoryName) {
            // 初期化：各月の金額を0で埋める
            $monthlyTotals = array_fill(1, 12, 0);

            // 現在のカテゴリに該当するデータだけ抽出
            $categoryExpenses = $expenses->where('category_id', $categoryId);

            foreach ($categoryExpenses as $expense) {
                $monthlyTotals[$expense->month] = (int) $expense->total;
            }

            // 1月始まりの配列にする
            $datasets[] = [
                'label' => $categoryName,
                'data' => array_values($monthlyTotals),
            ];
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => $datasets,
        ]);
    }

    // ドーナツグラフ用のグラフデータ取得
    public function doughnutGetCategoryTotals() {
        // テストデータ
        // $labels = ["食費", "交通費", "趣味", "光熱費"];
        // $datasets = [1000, 3000, 50000, 6000];

        // return response()->json([
        //     'labels' => $labels,
        //     'datasets' => $datasets,
        // ]);
        $now = Carbon::now(); // 現在日時

        // $data = Expense::select('category_id', DB::raw('SUM(amount) as total'))
        //     ->whereYear('date', $now->year) // 今年
        //     ->whereMonth('date', $now->month) // 今月
        //     ->groupBy('category_id')
        //     ->with('category')
        //     ->get();

        // // カテゴリー名の配列を生成
        // $labels = $data->map(function($item) {
        //     return optional($item->category)->name ?? '未分類';
        // });

        // $response = [
        //     'labels' => $data->pluck('category.name'), // カテゴリー名
        //     'totals' => $data->pluck('total') // 合計金額
        // ];

        $categories = Category::withSum(['expenses' => function($query) use ($now) {
            $query->whereYear('date', $now->year)
                  ->whereMonth('date', $now->month);
        }], 'amount')->get();

        $response = [
            'labels' => $categories->pluck('name'),
            'totals' => $categories->pluck('expenses_sum_amount')
        ];

        // return response()->json($response);


        return response()->json($response);
    }

}
