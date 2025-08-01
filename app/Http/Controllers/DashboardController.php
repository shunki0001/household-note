<?php

namespace App\Http\Controllers;
use App\Models\Expense;
use App\Models\Category;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    //
    public function index(Request $request)
    {
        $now = Carbon::now();
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userId = Auth::id();
        $categories = Category::all();

        $expenses = $user->expenses()
            ->with('category')
            ->orderBy('created_at', 'desc') // 入力日から最新の５件表示
            ->paginate(5);

        // 今月の合計支出
        $totalExpense = Expense::whereYear('date', $now->year)
        ->where('user_id', $userId)
        ->whereMonth('date', $now->month)
        ->sum('amount');

        return Inertia::render('Dashboard', [
            'expenses' => $expenses,
            'categories' => $categories,
            'flash' => [
                'message' => session('message')
            ],
            'totalExpense' => $totalExpense,
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
        return response()->json(['message' => '削除しました']);
    }

    // 編集ページ表示
    public function edit(Expense $expense, Request $request)
    {
        if(Auth::id() !== $expense->user_id) {
            abort(403);
        }

        $categories = Category::all(); // 編集画面でカテゴリー一覧を渡す
        $backRoute = $request->input('back', 'list'); // デフォルト：dashboard

        return Inertia::render('Expenses/Edit', [
            'expense' => $expense,
            'categories' => $categories,
            'back' => $backRoute,
            'submitUrl' => route('expenses.update', $expense->id),
        ]);
    }

    // 今月の合計支出を取得するAPI
    public function getTotalExpense()
    {
        $now = Carbon::now();
        $userId = Auth::id();

        $totalExpense = Expense::whereYear('date', $now->year)
            ->where('user_id', $userId)
            ->whereMonth('date', $now->month)
            ->sum('amount');

        return response()->json([
            'totalExpense' => $totalExpense
        ]);
    }

}
