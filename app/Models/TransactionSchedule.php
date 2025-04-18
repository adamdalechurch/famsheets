<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionSchedule extends Model
{
    use HasFactory;

   //tablename: transaction_schedule
   protected $table = 'transaction_schedule';

    protected $fillable = [
        'frequency',
        'next_due_date',
    ];

    protected $casts = [
        'next_due_date' => 'datetime',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
