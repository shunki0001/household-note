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

        return $this->jsonResponse('登録しました');
    }

    // 更新処理
    public function update(StoreExpenseRequest $request, Expense $expense)
    {
        $this->authorizeExpenseOwner($expense);

        $validated = $request->validated();
        $expense->update($validated);

        return $this->jsonResponse('更新しました');
    }

    // 削除処理
    public function destroy(Expense $expense)
    {
        $this->authorizeExpenseOwner($expense);

        $expense->delete();
        return $this->jsonResponse('削除しました');
    }

    // 編集処理
    public function edit(Expense $expense, Request $request)
    {
        $this->authorizeExpenseOwner($expense);

        $categories = Category::orderBy('sort_order')->get();
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

    /**
     * JSONレスポンス共通化
     */
    private function jsonResponse(string $message, int $status = 200)
    {
        return response()->json(['message' => $message], $status, [], JSON_UNESCAPED_UNICODE);
    }
}
