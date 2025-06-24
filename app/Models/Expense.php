<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['amount', 'date', 'title', 'category'];

    // Expenseモデル
    public function user() {
        return $this->belongsTo(User::class);
    }
}
