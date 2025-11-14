<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Expense;
use App\Models\Income;

class TransactionController extends Controller
{
    public function destroy($type, $id)
    {
        // $type -> income or expense

        if($type === 'income') {
            // \App\Models\Income::findOrFail($id)->delete();
            $item = Income::findOrFail($id);
        } else if($type === 'expense') {
            // \App\Models\Expense::findOrFail($id)->delete();
            $item = Expense::findOrFail($id);
        } else {
            abort(400, 'Invalid transaction type');
        }

        // 認可チェック: 他人のデータは削除できないように
        if ($item->user_id !== Auth::id()) {
            abort(403);
        }

        $item->delete();

        return $this->jsonResponse('削除しました');
    }

    private function jsonResponse(string $message, int $status = 200)
    {
        return response()->json(['message' => $message], $status, [], JSON_UNESCAPED_UNICODE);
    }
}
