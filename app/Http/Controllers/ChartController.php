<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Expense;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

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
        $data = [];
        $months = [];

        for ($month = 1; $month <= 12; $month++) {
            $total = DB::table('expenses')
                ->whereYear('date', 2025)
                ->whereMonth('date', $month)
                ->sum('amount');

                $data[] = (int) $total;
                $months[] = $month. '月';
        }

        return response()->json([
            'labels' => $months,
            'totals' => $data,
        ]);
    }
    public function getCategoryTotals() {
        $data = [];
        $labels = [];

        // x軸の月ラベル
        for ($month = 1; $month <= 12; $month++) {
            $labels[] = $month. '月';
        }

        // カテゴリー一覧をDBから取得
        $categories = DB::table('categories')->pluck('name', 'id');

        foreach($categories as $categoryId => $categoryName) {
            $monthlyTotals = [];

            for ($month = 1; $month <= 12; $month++) {
                $sum = DB::table('expenses')
                    ->where('category_id', $categoryId)
                    ->whereYear('date', 2025)
                    ->whereMonth('date', $month)
                    ->sum('amount');

                $monthlyTotals[] = (int) $sum;
            }

            $datasets[] = [
                'label' => $categoryName,
                'data' => $monthlyTotals,
            ];
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => $datasets,
        ]);
    }

}
