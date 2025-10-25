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

    // 1~12月毎に分けたカテゴリー別合計金額
    public function getCategoryTotals(Request $request) {
        $now = Carbon::now();
        $month = (int) $request->query('month', now()->month); // 月指定、デフォルトは今月
        $year = (int) $request->query('year', now()->year); // 年指定、デフォルトは今年
        $userId = Auth::id(); // ログインユーザーID

        // カテゴリー名一覧
        $categories = Category::select('name', 'icon_path', 'sort_order')
            ->withSum(['expenses' => function($query) use ($month, $year) {
                $query->whereMonth('date', $month)
                        ->whereYear('date', $year);
            }], 'amount')
            ->orderBy('sort_order', 'asc')
            ->get();

        $labels = $categories->pluck('name');
        $icons = $categories->pluck('icon_path')->map(fn($path) => asset($path));
        $datasets = [
            [
                'label' => '支出合計',
                'data' => $categories->pluck('expenses_sum_amount'),
            ]
            ];

        // 指定月のカテゴリー別合計取得
        $expenses = Expense::selectRaw('category_id, SUM(amount) as total')
            ->where('user_id', $userId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->groupBy('category_id')
            ->get();

        // 各カテゴリーの金額を配列か(存在しないカテゴリーは0)
        $totals = $categories->map(function($name, $categoryId) use ($expenses) {
            return (int) ($expenses->firstWhere('category_id', $categoryId)?->total ?? 0);
        })->values();

        return response()->json([
            'labels' => $labels,
            'icons' => $icons,
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

    // 支出合計グラフ
    public function getIncomeMonthlyTotals(Request $request)
    {
        $year = $request->query('year', Carbon::now()->year);
        $userId = Auth::id();

        $month = [];
        for ($i = 1; $i <= 12; $i++) {
            $month[] = $i . '月';
        }

        $incomes = DB::table('incomes')
            ->selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->where('user_id', $userId)
            ->whereYear('income_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $data = array_fill(1, 12, 0);

        foreach ($incomes as $income) {
            $data[$income->month] = (int) $income->total;
        }

        return response()->json([
            'labels' => $month,
            'totals' => array_values($data),
        ]);
    }

}
