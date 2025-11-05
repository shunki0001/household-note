<?php

namespace App\Http\Controllers;
use App\Models\Expense;
use App\Models\Category;
use App\Models\Income;
use App\Models\IncomeCategory;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // 初回表示用
    public function index()
    {
        ['userId' => $userId, 'now' => $now] = $this->getCommonParams();

        // 入力フォームの収入用カテゴリーを呼び出し
        $income_categories = IncomeCategory::all();

        // 入力フォームのカテゴリーをsort_orderで並び替え
        $categories = Category::orderBy('sort_order', 'asc')->get();

        // 今月の合計支出
        $totalExpense = $this->calculateTotalExpense($userId, $now);

        // 今月の合計収入
        $totalIncome = $this->calculateTotalIncome($userId, $now);

        return Inertia::render('Dashboard', [
            'categories' => $categories,
            'income_categories' => $income_categories,
            'flash' => [
                'message' => session('message')
            ],
            'totalExpense' => (int) $totalExpense,
            'totalIncome' => (int) $totalIncome,
        ]);

    }

    // 今月の合計支出を取得するAPI(リアルタイム更新用)
    public function getTotalExpense()
    {

        ['userId' => $userId, 'now' => $now] = $this->getCommonParams();

        $totalExpense = $this->calculateTotalExpense($userId, $now);

        return response()->json([
            'totalExpense' => $totalExpense,
        ]);
        // return $this->jsonResponse([
        //     'totalExpense' => $totalExpense,
        // ]);
    }

    // 今月の合計収入を取得するAPI(リアルタイム更新用)
    public function getTotalMonthlyIncomes()
    {

        ['userId' => $userId, 'now' => $now] = $this->getCommonParams();

        $totalIncome = $this->calculateTotalIncome($userId, $now);

        return response()->json([
            'totalIncome' => $totalIncome,
        ]);
        // return $this->jsonResponse([
        //     'totalIncome' => $totalIncome,
        // ]);
    }

    // ============================
    // 共通ロジックをまとめたメソッド
    // ============================

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

    private function jsonResponse(array $data, int $status)
    {
        return response()->json($data, $status, [], JSON_UNESCAPED_UNICODE);
    }
}
