<?php

use App\Http\Controllers\ChartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
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
    Route::delete('/expenses/{expense}', [DashboardController::class, 'destroy'])->name('expenses.destroy');

    Route::get('/expenses/{expense}/edit', [DashboardController::class, 'edit'])->name('expenses.edit');
    Route::put('/expenses/{expense}', [DashboardController::class, 'update'])->name('expenses.update');
    Route::get('/expenses/monthly', [ExpenseController::class, 'getMonthlyExpenses']);

    Route::get('/api/chart-data', [ChartController::class, 'getMonthlyTotals']);
    Route::get('/api/chart-data/category-monthly-single', [ChartController::class, 'getCategoryTotals']);
    Route::get('/api/chart-data/doughnut', [ChartController::class, 'doughnutGetCategoryTotals']);
    Route::get('/api/chart-data/monthly-expenses', [ExpenseController::class, 'getMonthlyExpenses']);

    // ページ移動
    Route::get('/list', [PageController::class, 'list'])->name('list');
    Route::get('/graph/monthly', [PageController::class, 'monthlyGraph'])->name('graph.monthly');
    Route::get('/graph/category', [PageController::class, 'categoryGraph'])->name('graph.category');
});

require __DIR__.'/auth.php';
