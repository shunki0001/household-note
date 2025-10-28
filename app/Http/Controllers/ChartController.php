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
    /**
     * 定数群
     */
    private const MONTHS_IN_YEAR = 12;
    private const DEFAULT_COLOR = '#000000';
    private const DATASET_LABEL = '支出合計';

    private const TABLE_EXPENSES = 'expenses';
    private const TABLE_INCOMES = 'incomes';
    private const COLUMN_DATE = 'date';
    private const COLUMN_INCOME_DATE = 'income_date';
    private const COLUMN_AMOUNT = 'amount';

    /**
     * 共通: JSONレスポンス生成
     */
    private function jsonResponse(array $data)
    {
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 共通: 月ラベル(1~12月)生成
     */
    private function generateMonthLabels(): array
    {
        return array_map(fn($i) => "{$i}月", range(1, self::MONTHS_IN_YEAR));
    }

    /**
     * 共通: 指定テーブルからユーザー・年別の月合計を取得
     */
    // (テーブル名, 日付カラム名, ユーザーID, 年度)
    private function fetchMonthlyData(string $table, string $dateColumn, int $userId, int $year): array
    {
        \Log::info('fetchMonthlyData called', ['table' => $table, 'dateColumn' => $dateColumn, 'userId' => $userId, 'year' => $year]);

        try {
            // データベースドライバーを取得
            $driverName = DB::connection()->getDriverName();

            // PostgreSQLとMySQL/SQLiteで異なるSQLを使用
            if ($driverName === 'pgsql') {
                // PostgreSQL用
                $monthColumn = "EXTRACT(MONTH FROM {$dateColumn})";
            } else {
                // MySQL, SQLite用
                $monthColumn = "MONTH({$dateColumn})";
            }

            $records = DB::table($table)
                ->selectRaw("{$monthColumn} as month, SUM(amount) as total")
                ->where('user_id', $userId)
                ->whereYear($dateColumn, $year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            \Log::info('fetchMonthlyData records', ['count' => $records->count()]);

            $monthlyData = array_fill(1, self::MONTHS_IN_YEAR, 0);

            foreach($records as $record) {
                $monthlyData[$record->month] = (int) $record->total;
            }

            \Log::info('fetchMonthlyData completed successfully');
            return array_values($monthlyData);
        } catch (\Exception $e) {
            // Render用にstdoutに出力
            error_log('fetchMonthlyData error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());

            \Log::error('fetchMonthlyData error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * 共通: 指定年月のカテゴリー別支出合計を取得
     */
    private function fetchCategoryTotals(int $userId, int $year, int $month)
    {
        return Category::select('name', 'icon_path', 'sort_order', 'color')
            ->withSum(['expenses' => function ($query) use ($userId, $month, $year) {
                $query->where('user_id', $userId)
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month);
            }], 'amount')
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    /**
     * 共通: カテゴリー情報をラベル・アイコン・色に整形
     */
    private function formatCategoryData($categories): array
    {
        return [
            'labels' => $categories->pluck('name'),
            'icons' => $categories->pluck('icon_path')->map(fn($path) => asset($path)),
            'colors' => $categories->pluck('color')->map(fn($color) => $color ?? self::DEFAULT_COLOR),
        ];
    }

    /**
     * 共通: Chart.jsデータセット生成
     */
    private function createDataset($categories): array
    {
        return [[
            'label' => self::DATASET_LABEL,
            'data' => $categories->pluck('expenses_sum_amount')->map(fn($value) => (int)$value),
        ]];
    }

    /**
     * 共通: クエリパラメータ取得
     */
    private function getQueryParams(Request $request): array
    {
        $now = Carbon::now();

        $year = (int) $request->query('year', $now->year);
        $month = (int) $request->query('month', $now->month);
        $userId = Auth::id();

        return [$year, $month, $userId];

    }

    /* ======================================================
     * 各種グラフ用API
     * ====================================================== */

    /**
     * 月別支出合計グラフ
     */
    public function getMonthlyExpenseTotals(Request $request)
    {
        try {
            $year = (int) $request->query('year', Carbon::now()->year);
            $userId = Auth::id();

            \Log::info('getMonthlyExpenseTotals called', ['year' => $year, 'userId' => $userId]);

            if (!$userId) {
                \Log::warning('getMonthlyExpenseTotals: No user ID');
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $totals = $this->fetchMonthlyData(self::TABLE_EXPENSES, self::COLUMN_DATE, $userId, $year);

            return $this->jsonResponse([
                'labels' => $this->generateMonthLabels(),
                'totals' => $totals,
            ]);
        } catch (\Exception $e) {
            // Render用にstdoutに出力
            error_log('getMonthlyExpenseTotals error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());

            \Log::error('getMonthlyExpenseTotals error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * 月別収入合計グラフ
     */
    public function getMonthlyIncomeTotals(Request $request)
    {
        $year = (int) $request->query('year', Carbon::now()->year);
        $userId = Auth::id();

        $totals = $this->fetchMonthlyData(self::TABLE_INCOMES, self::COLUMN_INCOME_DATE, $userId, $year);

        return $this->jsonResponse([
            'labels' => $this->generateMonthLabels(),
            'totals' => $totals,
        ]);
    }

    /**
     * カテゴリー別の月次支出合計グラフ
     */
    public function getCategoryExpenseTotals(Request $request) {

        [$year, $month, $userId] = $this->getQueryParams($request);

        $categories = $this->fetchCategoryTotals($userId, $year, $month);
        $categoryData = $this->formatCategoryData($categories);
        $datasets = $this->createDataset($categories);

        return $this->jsonResponse([
            'labels' => $categoryData['labels'],
            'icons' => $categoryData['icons'],
            'colors' => $categoryData['colors'],
            'datasets' => $datasets,
        ]);

    }

    /**
     * ドーナツグラフ用カテゴリー別支出データ
     */
    public function doughnutGetCategoryExpenseTotals() {
        $now = Carbon::now(); // 現在日時
        $userId = Auth::id(); // ログインユーザーID

        // $categories = $this->getCategoryTotalsForMonth($userId, $now->year, $now->month);
        $categories = $this->fetchCategoryTotals($userId, $now->year, $now->month);

        // $chartData = $this->formatDoughnutChartData($categories);
        $chartData = [
            'labels' => $categories->pluck('name'),
            'totals' => $categories->pluck('expenses_sum_amount')->map(fn($value) => (int)$value),
            'colors' => $categories->pluck('color')->map(fn($color) => $color ?? self::DEFAULT_COLOR),
        ];

        return $this->jsonResponse($chartData);
    }

}
