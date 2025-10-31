<?php

namespace App\Http\Controllers;
use App\Models\Expense;
use App\Models\Category;
use App\Models\Income;
use App\Models\IncomeCategory;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // 初回表示用
    public function index()
    // public function index(Request $request)
    {
        ['userId' => $userId, 'now' => $now] = $this->getCommonParams();
        // $now = Carbon::now();
        /** @var \App\Models\User $user */
        // $user = Auth::user();
        // $userId = Auth::id();
        // $categories = Category::all();

        // 入力フォームの収入用カテゴリーを呼び出し
        $income_categories = IncomeCategory::all();

        // $expenses = $user->expenses()
        //     ->with('category')
        //     ->orderBy('created_at', 'desc') // 入力日から最新の５件表示
        //     ->paginate(5);

        // 入力フォームのカテゴリーをsort_orderで並び替え
        $categories = Category::orderBy('sort_order', 'asc')->get();

        // 今月の合計支出
        $totalExpense = $this->calculateTotalExpense($userId, $now);

        // $totalExpense = Expense::whereYear('date', $now->year)
        // ->where('user_id', $userId)
        // ->whereMonth('date', $now->month)
        // ->sum('amount');

        // 今月の合計収入
        $totalIncome = $this->calculateTotalIncome($userId, $now);
        // $totalIncome = Income::whereYear('income_date', $now->year)
        // ->where('user_id', $userId)
        // ->whereMonth('income_date', $now->month)
        // ->sum('amount');

        // // 支出
        // $expensesQuery = DB::table('expenses')
        //     ->join('categories', 'expenses.category_id', '=', 'categories.id')
        //     ->where('expenses.user_id', $userId)
        //     ->select(
        //         'expenses.id',
        //         'expenses.amount',
        //         'expenses.date',
        //         'expenses.title',
        //         DB::raw("categories.name as category_name"),
        //         DB::raw("'expense' as type"),
        //         'expenses.created_at'
        //     );

        // // 収入
        // $incomesQuery = DB::table('incomes')
        //     ->join('income_categories', 'incomes.income_category_id', '=', 'income_categories.id')
        //     ->where('incomes.user_id', $userId)
        //     ->select(
        //         'incomes.id',
        //         'incomes.amount',
        //         'incomes.income_date as date',
        //         DB::raw("'収入' as title"),
        //         // DB::raw("Null as title"),
        //         DB::raw("income_categories.name as category_name"),
        //         DB::raw("'income' as type"),
        //         'incomes.created_at'
        //     );

        // // 支出と収入を合体して日付順
        // $transactions = $expensesQuery
        //     ->unionAll($incomesQuery);

        // $transactions = DB::query()
        //     ->fromSub($transactions, 'sort')
        //     ->orderBy('sort.created_at', 'desc')
        //     ->paginate(5);

        return Inertia::render('Dashboard', [
            // 'expenses' => $expenses,
            'categories' => $categories,
            'income_categories' => $income_categories,
            'flash' => [
                'message' => session('message')
            ],
            'totalExpense' => $totalExpense,
            'totalIncome' => $totalIncome,
            // 'transactions' => $transactions,
            // 'latestTransactions' => $transactions->items(),
        ]);

    }

    public function destroy(Expense $expense)
    {
        // 自分のデータだけ削除可能にする
        if(Auth::id() !== $expense->user_id) {
            abort(403);
        }

        $expense->delete();
        // ページリダイレクトではなく、JSONを返す(Inertia用)
        // return response()->json(['message' => '削除しました']);
        return $this->jsonResponse([
            'message' => '削除しました'
        ]);
    }

    // 編集ページ表示
    public function edit(Expense $expense, Request $request)
    {
        if(Auth::id() !== $expense->user_id) {
            abort(403);
        }

        $categories = Category::all(); // 編集画面でカテゴリー一覧を渡す
        $backRoute = $request->input('back', 'dashboard'); // デフォルト：dashboard

        return Inertia::render('Expenses/Edit', [
            'expense' => $expense,
            'categories' => $categories,
            'back' => $backRoute,
            'submitUrl' => route('expenses.update', $expense->id),
        ]);
    }

    // 今月の合計支出を取得するAPI(リアルタイム更新用)
    public function getTotalExpense()
    {
        // $now = Carbon::now();
        // $userId = Auth::id();

        ['userId' => $userId, 'now' => $now] = $this->getCommonParams();

        $totalExpense = $this->calculateTotalExpense($userId, $now);
        // $totalExpense = Expense::whereYear('date', $now->year)
        //     ->where('user_id', $userId)
        //     ->whereMonth('date', $now->month)
        //     ->sum('amount');

        // return response()->json([
        //     'totalExpense' => $totalExpense
        // ]);
        return $this->jsonResponse([
            'totalExpense' => $totalExpense,
        ]);
    }

    // 今月の合計収入を取得するAPI(リアルタイム更新用)
    public function getTotalMonthlyIncomes()
    {
        // $now = Carbon::now();
        // $userId = Auth::id();

        ['userId' => $userId, 'now' => $now] = $this->getCommonParams();

        $totalIncome = $this->calculateTotalIncome($userId, $now);

        // return response()->json([
        //     'totalIncome' => $totalIncome,
        // ]);
        return $this->jsonResponse([
            'totalIncome' => $totalIncome,
        ]);
    }

    // ============================
    // 共通ロジックをまとめたメソッド
    // ============================

    /**
     * JSONレスポンス生成
     */
    private function jsonResponse(array $data)
    {
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }
    /**
     * 今月の支出合計を計算
     */
    private function calculateTotalExpense($userId, $now)
    {
        return Expense::whereYear('date', $now->year)
            ->where('user_id', $userId)
            ->whereMonth('date', $now->month)
            ->sum('amount');
    }

    /**
     * 今月の収入合計を計算
     */
    private function calculateTotalIncome($userId, $now)
    {
        return Income::whereYear('income_date', $now->year)
            ->where('user_id', $userId)
            ->whereMonth('income_date', $now->month)
            ->sum('amount');
    }

    /**
     * 任意の年月、ユーザーIDを取得
     */
    private function getCommonParams(): array
    {
        return [
            'userId' => Auth::id(),
            'now' => Carbon::now(),
        ];
    }

}
