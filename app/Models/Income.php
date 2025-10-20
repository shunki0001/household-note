<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Income extends Model
{
    use HasFactory;
    protected $fillable = ['amount', 'income_date', 'income_category_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        // return $this->belongsTo(IncomeCategory::class, 'income_category_id');
        return $this->belongsTo(IncomeCategory::class);
    }
}
