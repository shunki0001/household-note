<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // use HasFactory;

    protected $fillable = ['name', 'icon_path', 'sort_order','color'];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
