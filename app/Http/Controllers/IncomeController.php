<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Income;
use App\Models\IncomeCategory;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Http\Requests\StoreIncomeRequest;

class IncomeController extends Controller
{
    // 収入のみ一覧表示
    public function index()
    {

    }

    // 登録処理(ログインユーザーに紐付けて保存)
    public function store(StoreIncomeRequest $request)
    {
        $validated = $request->validated();

        // ログインユーザーに紐づけて保存
        Income::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => '登録しました',
        ]);
    }

    // 更新処理
    public function update(Request $request, Income $income)
    {
        // ログインユーザーのデータのみ更新処理
        if ($income->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric',
            'income_date' => 'required|date',
            'income_category_id' => 'required',
        ]);

        $income->update($validated);

        return response()->json([
            'message' => '更新しました',
        ]);
    }

    // 編集ページ表示
    public function edit(Request $request, Income $income)
    {
        if(Auth::id() !== $income->user_id) {
            abort(403);
        }

        $income_categories = IncomeCategory::all();
        $backRoute = $request->input('back', 'dashboard');

        return Inertia::render('Incomes/Edit', [
            'income' => $income,
            'income_categories' => $income_categories,
            'back' => $backRoute,
            'submitUrl' => route('incomes.update', $income->id),
        ]);
    }

    // 削除処理
    // public function destroy(Income $income)
    // {
    //     if(Auth::id() !== $income->user_id) {
    //         abort(403);
    //     }

    //     $income->delete();
    //     return response()->json(['message' => '削除しました']);
    // }

    // 今月の合計を算出
    // public function getTotalMonthlyIncomes()
    // {
    //     $now = Carbon::now();
    //     $userId = Auth::id();

    //     $totalIncome = Income::whereYear('income_date', $now->year)
    //         ->where('user_id', $userId)
    //         ->whereMonth('income_date', $now->month)
    //         ->sum('amount');

    //     return response()->json([
    //         'totalIncome' => $totalIncome,
    //     ]);
    // }
}
