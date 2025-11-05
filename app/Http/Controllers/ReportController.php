<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\JsonResponse;

use function Laravel\Prompts\select;

class ReportController extends Controller
{
    /**
     * 最新の支出・収入を5件取得
     */
    public function latestJson()
    {
        $userId = Auth::id();

        $transactions = $this->getBaseExpenseQuery($userId)
            ->unionAll($this->getBaseIncomeQuery($userId))
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return $this->jsonResponse(['transactions' => $transactions]);
    }

    /**
     * 指定年月の支出・収入一覧を取得
     */
    public function getMonthlyTransactions(Request $request): JsonResponse
    {
        $userId = Auth::id();
        [$year, $month] = $this->getYearMonth($request);

        $transactions = $this->getBaseExpenseQuery($userId)
            ->whereYear('expenses.date', $year)
            ->whereMonth('expenses.date', $month)
            ->unionAll(
                $this->getBaseIncomeQuery($userId)
                    ->whereYear('incomes.income_date', $year)
                    ->whereMonth('incomes.income_date', $month)
            )
            ->orderBy('date', 'desc')
            ->get();

        return $this->jsonResponse(['transactions' => $transactions]);
    }

    /**
     * 支出のベースクエリ
     */
    private function getBaseExpenseQuery(int $userId)
    {
        return DB::table('expenses')
            ->leftJoin('categories', 'expenses.category_id', '=', 'categories.id')
            ->where('expenses.user_id', $userId)
            ->select(
                'expenses.id',
                'expenses.amount',
                'expenses.date',
                'expenses.title',
                DB::raw("COALESCE(categories.name, '未分類') as category_name"),
                DB::raw("'expense' as type"),
                'expenses.created_at'
            );
    }

    /**
     * 収入のベースクエリ
     */
    private function getBaseIncomeQuery(int $userId)
    {
        return DB::table('incomes')
            ->leftJoin('income_categories', 'incomes.income_category_id', '=', 'income_categories.id')
            ->where('incomes.user_id', $userId)
            ->select(
                'incomes.id',
                'incomes.amount',
                'incomes.income_date as date',
                DB::raw("'収入' as title"),
                DB::raw("COALESCE(income_categories.name, '未分類') as category_name"),
                DB::raw("'income' as type"),
                'incomes.created_at'
            );
    }

    /**
     * 年月の取得（デフォルトは現在）
     */
    private function getYearMonth(Request $request): array
    {
        return [
            (int) $request->query('year', now()->year),
            (int) $request->query('month', now()->month),
        ];
    }

    private function jsonResponse(array $data)
    {
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

}
