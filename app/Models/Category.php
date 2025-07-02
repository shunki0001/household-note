<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name']; // nameを登録可能に

    public function expenses()
    {
        return $this->HasMany(Expense::class);
    }
}
