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
    private const MONTHS_IN_YEAR = 12;

    // 月別支出合計を取得
    public function getMonthlyTotals(Request $request)
    {
        $year = $request->query('year', Carbon::now()->year);
        $userId = Auth::id();

        $monthLabels = $this->generateMonthLabels();
        $monthlyTotals = $this->fetchMonthlyTotals($userId, $year);

        return response()->json([
            'labels' => $monthLabels,
            'totals' => array_values($monthlyTotals),
        ]);
    }

    // 月ラベル(1~12月)を生成
    private function generateMonthLabels(): array
    {
        return array_map(fn($i) => "{$i}月", range(1, self::MONTHS_IN_YEAR));
    }

    // DBから指定ユーザー・年の月別支出合計を取得
    private function fetchMonthlyTotals(int $userId, int $year): array
    {
        $expenses = DB::table('expenses')
            ->selectRaw('MONTH(date) as month, SUM(amount) as total ')
            ->where('user_id', $userId)
            ->whereYear('date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 初期値0の配列を生成
        $monthlyTotals = array_fill(1, self::MONTHS_IN_YEAR, 0);

        foreach ($expenses as $expense) {
            $monthlyTotals[$expense->month] = (int) $expense->total;
        }

        return $monthlyTotals;
    }


    private const DEFAULT_COLOR = '#000000';
    private const DATASET_LABEL = '支出合計';

    // カテゴリー別の月次合計金額を取得
    public function getCategoryTotals(Request $request) {

        [$year, $month, $userId] = $this->getQueryParams($request);

        $categories = $this->fetchCategoriesWithTotals($year, $month);
        $categoryData = $this->formatCategoryData($categories);

        $datasets = $this->createDataset($categories);

        return response()->json([
            'labels' => $categoryData['labels'],
            'icons' => $categoryData['icons'],
            'colors' => $categoryData['colors'],
            'datasets' => $datasets,
        ]);

    }

    // クエリパラメータを取得し整形
    private function getQueryParams(Request $request): array
    {
        $now = Carbon::now();

        $year = (int) $request->query('year', $now->year);
        $month = (int) $request->query('month', $now->month);
        $userId = Auth::id();

        return [$year, $month, $userId];

    }

    // カテゴリーごとの支出合計を取得
    private function fetchCategoriesWithTotals(int $year, int $month)
    {
        return Category::select('name', 'icon_path', 'sort_order', 'color')
            ->withSum(['expenses' => function ($query) use ($month, $year) {
                $query->whereMonth('date', $month)
                    ->whereYear('date', $year);
            }], 'amount')
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    // カテゴリー情報をラベル・アイコン・色に整形
    private function formatCategoryData($categories): array
    {
        return [
            'labels' => $categories->pluck('name'),
            'icons' => $categories->pluck('icon_path')->map(fn($path) => asset($path)),
            'colors' => $categories->pluck('color')->map(fn($color) => $color ?? self::DEFAULT_COLOR),
        ];
    }

    // グラフ用データセットを作成
    private function createDataset($categories): array
    {
        return [[
            'label' => self::DATASET_LABEL,
            'data' => $categories->pluck('expenses_sum_amount')->map(fn($value) => (int)$value),
        ]];
    }

    // ドーナツグラフ用のカテゴリー別支出データ取得
    public function doughnutGetCategoryTotals() {
        $now = Carbon::now(); // 現在日時
        $userId = Auth::id(); // ログインユーザーID

        $categories = $this->getCategoryTotalsForMonth($userId, $now->year, $now->month);

        $chartData = $this->formatDoughnutChartData($categories);

        return response()->json($chartData);
    }

    // 指定年月のカテゴリー別支出合計を取得
    private function getCategoryTotalsForMonth(int $userId, int $year, int $month)
    {
        return Category::select('name', 'color')
            ->withSum(['expenses' => function($query) use ($userId, $year, $month) {
                $query->where('user_id', $userId)
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month);
            }], 'amount')
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    // Chart.jsのドーナツ用データに整形
    private function formatDoughnutChartData($categories): array
    {
        return [
            'labels' => $categories->pluck('name'),
            'totals' => $categories->pluck('expenses_sum_amount')->map(fn($value) => (int) $value),
            'colors' => $categories->pluck('color')->map(fn($color) => $color ?? self::DEFAULT_COLOR),
        ];
    }


    // 支出合計グラフ
    public function getIncomeMonthlyTotals(Request $request)
    {
        $year = (int) $request->query('year', Carbon::now()->year);
        $userId = Auth::id();

        $labels = $this->generateMonthLabels();
        $totals = $this->getMonthlyTotalsByTables('incomes', 'income_date', $userId, $year);

        return response()->json([
            'labels' => $labels,
            'totals' => $totals,
        ]);
    }

    // 指定テーブルからユーザー・年別の月合計を取得
    private function getMonthlyTotalsByTables(string $table, string $dateColumn, int $userId, int $year): array
    {
        $records = DB::table($table)
            ->selectRaw("MONTH({$dateColumn}) as month, SUM(amount) as total")
            ->where('user_id', $userId)
            ->whereYear($dateColumn, $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyData = array_fill(1, self::MONTHS_IN_YEAR, 0);

        foreach($records as $record) {
            $monthlyData[$record->month] = (int) $record->total;
        }

        return array_values($monthlyData);
    }

}
