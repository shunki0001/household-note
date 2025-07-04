<?php

use App\Http\Controllers\ChartController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/chart/monthly', [ChartController::class, 'getMonthlyExpenses']);
// Route::middleware('auth:sanctum')->get('/chart/category/{month}', [ChartController::class, 'getCategoryExpenses']);

