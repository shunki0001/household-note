<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

use function Laravel\Prompts\select;

class ReportController extends Controller
{
    // 支出と収入をまとめて一覧表示(5件表示)
    // 日付順に並び替え
    public function latestJson()
    {
        $userId = Auth::id();

        // 支出クエリ
        $expensesQuery = DB::table('expenses')
            ->leftJoin('categories', 'expenses.category_id', '=', 'categories.id')
            ->where('expenses.user_id', $userId)
            ->select('expenses.user_id', $userId)
            ->select(
                'expenses.id',
                'expenses.amount',
                'expenses.date',
                'expenses.title',
                DB::raw("COALESCE(categories.name, '未分類') as category_name"),
                DB::raw("'expense' as type")
            );

        // 収入クエリ
        $incomesQuery = DB::table('incomes')
            ->leftJoin('income_categories', 'incomes.income_category_id', '=', 'income_categories.id')
            ->where('incomes.user_id', $userId)
            ->select(
                'incomes.id',
                'incomes.amount',
                'incomes.income_date as date',
                DB::raw("'収入' as title"),
                DB::raw("COALESCE(income_categories.name, '未分類') as category_name"),
                DB::raw("'income' as type")
            );

        // UNION + 並び替え + ページネーション
        $transactions = $expensesQuery
            ->unionAll($incomesQuery)
            ->orderBy('date', 'desc')
            ->paginate(5);

        return response()->json([
            'transactions' => $transactions,
        ]);
    }

    // public function dashboard()
    // {
    //     $userId = Auth::id();

    //     // 支出
    //     $expensesQuery = DB::table('expenses')
    //         ->join('categories', 'expenses.category_id', '=', 'categories.id')
    //         ->where('expenses.user_id', $userId)
    //         ->select(
    //             'expenses.id',
    //             'expenses.amount',
    //             'expenses.date',
    //             'expenses.title',
    //             DB::raw("JSON_OBJECT('name', categories.name) as category"),
    //             DB::raw("'expense' as type")
    //         );

    //     // 収入
    //     $incomesQuery = DB::table('incomes')
    //         ->join('income_categories', 'incomes.income_category_id', '=', 'income_categories.id')
    //         ->where('incomes.user_id', $userId)
    //         ->select(
    //             'incomes.id',
    //             'incomes.amount',
    //             'incomes.income_date as date',
    //             DB::raw("Null as title"),
    //             DB::raw("JSON_OBJECT('name', income_categories.income) as category"),
    //             DB::raw("'income' as type")
    //         );

    //     // 支出と収入うを合体して日付順
    //     $transactions = $expensesQuery
    //         ->unionAll($incomesQuery)
    //         ->orderBy('date', 'desc')
    //         ->paginate(5);

    //     return Inertia::render('Dashboard', [
    //         'transactions' => $transactions, // ページネーションオブジェクト
    //         'latestTransactions' => $transactions->items(), // 配列のみ
    //         // 他のprops(expenses, categories, totalIncome など)
    //     ]);
    // }

    // 支出と収入を一覧表示
    // 月別に表示
    public function getMonthlyTransactions(Request $request)
    {
        $userId = Auth::id();
        $year = $request->query('year', now()->year);
        $month = $request->query('month', now()->month);

        $expensesQuery = DB::table('expenses')
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->select(
                'expenses.id',
                'expenses.amount',
                'expenses.date',
                'expenses.title',
                DB::raw("JSON_OBJECT('name', categories.name) as category"),
                DB::raw("'expense' as type")
            )
            ->where('expenses.user_id', $userId)
            ->whereYear('expenses.date', $year)
            ->whereMonth('expenses.date', $month);

        $incomesQuery = DB::table('incomes')
            ->join('income_categories', 'incomes.income_category_id', '=', 'income_categories.id')
            ->select(
                'incomes.id',
                'incomes.amount',
                'incomes.income_date as date',
                DB::raw("NULL as title"),
                DB::raw("JSON_OBJECT('name', income_categories.name) as category"),
                DB::raw("'income' as type")
            )
            ->where('incomes.user_id', $userId)
            ->whereYear('incomes.income_date', $year)
            ->whereMonth('incomes.income_date', $month);

        $transactions = $expensesQuery
            ->unionAll($incomesQuery)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($transactions);
    }
}
