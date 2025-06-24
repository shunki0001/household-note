<?php

namespace App\Http\Controllers;
use App\Models\Expense;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    //
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ]);

        $request->user()->expenses()->create($validated);

        return redirect()->back();
    }
}
