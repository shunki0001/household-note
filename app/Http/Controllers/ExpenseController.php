<?php

namespace App\Http\Controllers;
use App\Models\Expense;
use App\Models\Category;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreExpenseRequest;

class ExpenseController extends Controller
{

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

    // 削除処理
    public function destroy(Expense $expense)
    {
        $this->authorizeExpenseOwner($expense);

        $expense->delete();
        return response()->json(['message' => '削除しました']);
    }

    // 編集処理
    public function edit(Expense $expense, Request $request)
    {
        $this->authorizeExpenseOwner($expense);

        $categories = Category::all();
        $backRoute = $request->input('back', 'dashboard');

        return Inertia::render('Expenses/Edit', [
            'expense' => $expense,
            'categories' => $categories,
            'back' => $backRoute,
            'submitUrl' => route('expenses.update', $expense->id),
        ]);
    }

    /**
     * アクセス制限
     */
    private function authorizeExpenseOwner(Expense $expense): void
    {
        if(Auth::id() !== $expense->user_id) {
            abort(403, 'この操作は許可されていません。');
        }
    }
}
