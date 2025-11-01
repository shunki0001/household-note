<?php

use App\Http\Controllers\ChartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Artisan;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/', function () {
    return Inertia::render('TopPage', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/run-seeder', function () {
    Artisan::call('db:seed', ['--force' => true]);
    return 'Seeder executed successfully.';
});

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    // Route::delete('/expenses/{expense}', [DashboardController::class, 'destroy'])->name('expenses.destroy');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Route::get('/expenses/{expense}/edit', [DashboardController::class, 'edit'])->name('expenses.edit');
    Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
    // Route::get('/expenses/{id}/edit', [DashboardController::class, 'edit'])->name('expenses.edit');
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    // Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    // Route::get('/expenses/monthly', [ExpenseController::class, 'getMonthlyExpenses']);

    // Route::get('/api/expenses/latest-json', [ExpenseController::class, 'latestJson'])->name('expenses.latestJson');
    Route::get('/api/dashboard/total-expense', [DashboardController::class, 'getTotalExpense'])->name('dashboard.totalExpense');
    Route::get('/api/chart-data', [ChartController::class, 'getMonthlyExpenseTotals']);
    Route::get('/api/chart-data/category-monthly-single', [ChartController::class, 'getCategoryExpenseTotals']);
    Route::get('/api/chart-data/doughnut', [ChartController::class, 'doughnutGetCategoryExpenseTotals']);
    // Route::get('/api/chart-data/monthly-expenses', [ExpenseController::class, 'getMonthlyExpenses']);

    // ページ移動
    Route::get('/list', [PageController::class, 'list'])->name('list');
    Route::get('/graph/monthly', [PageController::class, 'monthlyGraph'])->name('graph.monthly');
    Route::get('/graph/category', [PageController::class, 'categoryGraph'])->name('graph.category');

    // 収入関連ルート、API
    Route::post('/incomes', [IncomeController::class, 'store'])->name('incomes.store'); // 登録
    Route::put('/incomes/{income}', [IncomeController::class, 'update'])->name('incomes.update'); // 更新
    // Route::put('/incomes/{id}', [IncomeController::class, 'update'])->name('incomes.update'); // 更新
    Route::get('/incomes/{income}/edit', [IncomeController::class, 'edit'])->name('incomes.edit'); // 編集
    // Route::get('/incomes/{id}/edit', [IncomeController::class, 'edit'])->name('incomes.edit'); // 編集
    Route::delete('/incomes/{income}', [IncomeController::class, 'destroy'])->name('incomes.destroy'); // 削除
    Route::get('/api/incomes/total-monthly-incomes', [DashboardController::class, 'getTotalMonthlyIncomes'])->name('dashboard.totalIncome'); // 合計
    Route::get('/api/report-data/latest-json', [ReportController::class, 'latestJson'])->name('transaction.latestJson'); // 支出+支出一覧(5件)
    Route::get('/api/report-data/monthly-transactions', [ReportController::class, 'getMonthlyTransactions']); // 支出+支出一覧(月別)

    // transaction関連
    Route::delete('/transactions/{type}/{id}', [TransactionController::class, 'destroy'])->name('transaction.destroy');
});

require __DIR__.'/auth.php';
