<?php

namespace App\Http\Controllers;
use App\Models\Expense;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    // 一覧表示(ログインユーザーのみ)
    public function index(Request $request)
    {
        $expenses = $request->user()->expenses()->latest()->get();

        return Inertia::render('Expenses/Index', [
            'expenses' => $expenses,
        ]);
    }

    // 登録処理(ログインユーザーに紐付けて保存)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ]);

        // ログインユーザーに紐づけて保存
        $request->user()->expenses()->create($validated);

        return redirect()->route('dashboard')->with('message', '登録しました');
    }
}
