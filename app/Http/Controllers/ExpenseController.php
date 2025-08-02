<?php

namespace App\Http\Controllers;
use App\Models\Expense;
use App\Models\Category;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Requests\StoreExpenseRequest;

class ExpenseController extends Controller
{

    public function latestJson(Request $request)
    {
        $expenses = $request->user()->expenses()
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

            return response()->json([
                'expenses' => $expenses,
            ]);
    }

    // 登録処理(ログインユーザーに紐付けて保存)
    public function store(StoreExpenseRequest $request)
    {
        $validated = $request->validated();

        // ログインユーザーに紐づけて保存
        Expense::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => '登録しました',
        ]);
    }

    // 一覧表示
    public function getMonthlyExpenses(Request $request)
    {
        $userId = Auth::id();
        $year = $request->query('year', now()->year);
        $month = $request->query('month', now()->month);

        $expenses = Expense::with('category')
            ->where('user_id', $userId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'desc')
            ->get();

            return response()->json($expenses);
    }

    // 更新処理
    public function update(StoreExpenseRequest $request, Expense $expense)
    {
        // ログインユーザーのデータのみ更新許可
        if ($expense->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validated();

        $expense->update($validated);

        return response()->json([
            'message' => '更新しました',
        ]);
    }
}
