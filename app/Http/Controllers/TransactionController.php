<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function destroy($type, $id)
    {
        // $type = $request->query('type'); // income or expense

        if($type === 'income') {
            // DB::table('incomes')->where('id', $id)->delete();
            \App\Models\Income::findOrFail($id)->delete();
        } else if($type === 'expense') {
            // DB::table('expenses')->where('id', $id)->delete();
            \App\Models\Expense::findOrFail($id)->delete();
        } else {
            // return response()->json(['message' => 'Invalid type'], 400);
            abort(400, 'Invalid transaction type');
        }

        // return redirect()->back()->with('message', '削除しました');
        return response()->json(['message' => '削除しました']);
    }
}
