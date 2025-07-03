<?php

namespace App\Http\Controllers;
use App\Models\Expense;
// use App\Models\Category;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    // 一覧表示(ログインユーザーのみ)
    public function index(Request $request)
    {
        $expenses = $request->user()->expenses()->orderBy('date', 'desc')->paginate(5);
        return Inertia::render('Dashboard', [
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
            'category_id' => 'required|exists:categories,id',
        ]);

        // ログインユーザーに紐づけて保存
        Expense::create([
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'title' => $validated['title'],
            'category_id' => $validated['category_id'],
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('dashboard')->with('message', '登録しました');
    }
}
