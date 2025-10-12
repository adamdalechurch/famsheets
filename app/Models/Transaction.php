<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_group_id',
        'description',
        'amount',
        'is_income',
        'recurring',
        'transaction_schedule_id',
        'income_source_id',
        'transaction_date',
        'category_id',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'is_income' => 'boolean',
        'recurring' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class);
    }

    public function transactionSchedule()
    {
        return $this->belongsTo(TransactionSchedule::class);
    }

    public function incomeSource()
    {
        return $this->belongsTo(IncomeSource::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
