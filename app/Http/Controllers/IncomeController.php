<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Income;
use App\Models\IncomeCategory;
use Inertia\Inertia;
use App\Http\Requests\StoreIncomeRequest;
use DragonCode\Contracts\Cache\Store;

class IncomeController extends Controller
{

    // 登録処理(ログインユーザーに紐付けて保存)
    public function store(StoreIncomeRequest $request)
    {
        $validated = $request->validated();

        // ログインユーザーに紐づけて保存
        Income::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return $this->jsonResponse('登録しました');
    }

    // 更新処理
    public function update(StoreIncomeRequest $request, Income $income)
    {
        $this->authorizeIncomeOwner($income);

        $validated = $request->validated();
        $income->update($validated);

        return $this->jsonResponse('更新しました');
    }

    // 編集ページ表示
    public function edit(Request $request, Income $income)
    {
        $this->authorizeIncomeOwner($income);

        $income_categories = IncomeCategory::all();
        $backRoute = $request->input('back', 'dashboard');

        return Inertia::render('Incomes/Edit', [
            'income' => $income,
            'income_categories' => $income_categories,
            'back' => $backRoute,
            'submitUrl' => route('incomes.update', $income->id),
        ]);
    }

    // 削除処理はTransactionControllerが行う

    /**
     * アクセス制限
     */
    private function authorizeIncomeOwner(Income $income): void
    {
        if(Auth::id() !== $income->user_id) {
            abort(403, 'この操作は許可されていません。');
        }
    }

}
