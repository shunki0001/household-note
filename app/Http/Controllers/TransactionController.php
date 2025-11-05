<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function destroy($type, $id)
    {
        // $type -> income or expense

        if($type === 'income') {
            \App\Models\Income::findOrFail($id)->delete();
        } else if($type === 'expense') {
            \App\Models\Expense::findOrFail($id)->delete();
        } else {
            abort(400, 'Invalid transaction type');
        }

        return $this->jsonResponse('削除しました');
    }

    private function jsonResponse(string $message, int $status = 200)
    {
        return response()->json(['message' => $message], $status, [], JSON_UNESCAPED_UNICODE);
    }
}
