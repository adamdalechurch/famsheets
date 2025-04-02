<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'income_source_category_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(IncomeSourceCategory::class, 'income_source_category_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
