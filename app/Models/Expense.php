<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    // Expenseモデル
    public function user() {
        return $this->belongsTo(User::class);
    }
}
