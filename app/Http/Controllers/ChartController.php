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
    // public function getMonthlyExpenses()
    // {
    //     $data = Expense::selectRaw('MONTH(date) as month, SUM(amount as total)')
    //         ->groupBy('month')
    //         ->orderBy('month')
    //         ->get();

    //         return response()->json($data);
    // }

    public function getMonthlyTotals(Request $request)
    {
        $year = $request->query('year', Carbon::now()->year);
        $userId = Auth::id();

        $month = [];
        for ($i = 1; $i <= 12; $i++) {
            $month[] = $i . '月';
        }

        $expenses = DB::table('expenses')
            ->selectRaw('MONTH(date) as month, SUM(amount) as total ')
            ->where('user_id', $userId)
            ->whereYear('date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $data = array_fill(1, 12, 0);

        foreach ($expenses as $expense) {
            $data[$expense->month] = (int) $expense->total;
        }

        return response()->json([
            'labels' => $month,
            'totals' => array_values($data),
        ]);
    }

    // public function getMonthlyTotals()
    // {
    //     // 月ラベル作成
    //     $month = [];
    //     for ($month = 1; $month <=12; $month++) {
    //         $months[] = $month . '月';
    //     }

    //     // SQL 1回で月毎の合計を取得
    //     $expenses = DB::table('expenses')
    //         ->selectRaw('MONTH(date) as month, SUM(amount) as total')
    //         ->whereYear('date', 2025)
    //         ->groupBy('month')
    //         ->orderBy('month')
    //         ->get();

    //     // 月別合計を0で初期化
    //     $data = array_fill(1, 12, 0);

    //     // 集計結果を反映
    //     foreach ($expenses as $expense) {
    //         $data[$expense->month] = (int) $expense->total;
    //     }

    //     return response()->json([
    //         'labels' => $months,
    //         'totals' => array_values($data), // ０始まりにする
    //     ]);
    // }

    // 1~12月毎に分けたカテゴリー別合計金額
    public function getCategoryTotals(Request $request) {
        $now = Carbon::now();
        $month = (int) $request->query('month', now()->month); // 月指定、デフォルトは今月
        $userId = Auth::id(); // ログインユーザーID

        $labels = [ "{$month}月" ];

        $categories = Category::pluck('name', 'id');

        $expenses = Expense::selectRaw('category_id, SUM(amount) as total')
            ->where('user_id', $userId)
            ->whereYear('date', 2025)
            ->whereMonth('date', $month)
            ->groupBy('category_id')
            ->get();

        $datasets = [];

        foreach ($categories as $categoryId => $categoryName) {
            $total = $expenses->firstWhere('category_id', $categoryId)?->total ?? 0;

            $datasets[] = [
                'label' => $categoryName,
                'data' => [ (int) $total ],
            ];
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => $datasets,
        ]);
    }

    // ドーナツグラフ用のグラフデータ取得
    public function doughnutGetCategoryTotals() {
        $now = Carbon::now(); // 現在日時
        $userId = Auth::id(); // ログインユーザーID

        $data = Expense::select('category_id', DB::raw('SUM(amount) as total'))
            ->where('user_id', $userId)
            ->whereYear('date', $now->year) // 今年
            ->whereMonth('date', $now->month) // 今月
            ->groupBy('category_id')
            ->with('category')
            ->get();

        // カテゴリー名の配列を生成
        $labels = $data->map(function($item) {
            return optional($item->category)->name ?? '未分類';
        });

        $response = [
            'labels' => $data->pluck('category.name'), // カテゴリー名
            'totals' => $data->pluck('total') // 合計金額
        ];
        return response()->json($response);
    }

}
