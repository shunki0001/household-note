<?php

namespace App\Http\Controllers;
use App\Models\Expense;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //
    public function index()
{
    /** @var \App\Models\User $user */
    // $user = auth()->user();
    $user = Auth::user();

    $expenses = $user->expenses()->latest()->get();

    return Inertia::render('Dashboard', [
        'expenses' => $expenses,
    ]);
}
}
