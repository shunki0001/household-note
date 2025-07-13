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
        // $user = auth()->user();
        $user = Auth::user();
        $userId = Auth::id();
        $categories = Category::all();

        // $expenses = $user->expenses()->orderBy('date', 'desc')->paginate(5);
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
        return redirect()->back()->with('message', '削除しました');
        // return redirect()->route('dashboard')->with('message', '削除しました');
    }

    // 編集ページ表示
    public function edit(Expense $expense)
    {
        if(Auth::id() !== $expense->user_id) {
            abort(403);
        }

        $categories = Category::all(); // 編集画面でカテゴリー一覧を渡す

        return Inertia::render('Expenses/Edit', [
            'expense' => $expense,
            'categories' => $categories,
        ]);
    }

    // 更新処理
    public function update(Request $request, Expense $expense)
    {
        if(Auth::id() !== $expense->user_id) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        $expense->update($validated);

        return redirect()->route('dashboard')->with('message', '更新しました');
    }
}
