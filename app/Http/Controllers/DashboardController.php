<?php

namespace App\Http\Controllers;
use App\Models\Expense;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        // $user = auth()->user();
        $user = Auth::user();

        $expenses = $user->expenses()->latest()->get();

        return Inertia::render('Dashboard', [
            'expenses' => $expenses,
            'flash' => [
                'message' => session('message')
            ]
        ]);

        // return Inertia::render('Dashboard', [
        //     'expenses' => $expenses,
        // ]);

        // $expenses = $request->user()->expenses()->latest()->get();

        // return Inertia::render('Expenses/Index', [
        //     'expenses' => $expenses,
        // ]);
    }

    public function destroy(Expense $expense)
    {
        // 自分のデータだけ削除可能にする
        if(Auth::id() !== $expense->user_id) {
            abort(403);
        }

        $expense->delete();
        return redirect()->route('dashboard')->with('message', '削除しました');
    }

    // 編集ページ表示
    public function edit(Expense $expense)
    {
        if(Auth::id() !== $expense->user_id) {
            abort(403);
        }

        return Inertia::render('Expenses/Edit', [
            'expense' => $expense,
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
            'category' => 'required|string|max:255',
        ]);

        $expense->update($validated);

        return redirect()->route('dashboard')->with('message', '更新しました');
    }
}
