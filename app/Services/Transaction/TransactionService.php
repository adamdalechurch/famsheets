<?php

namespace App\Services\Transaction;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionService
{
    public function create(array $data): Transaction
    {
        $data['user_id'] = Auth::id();
        $transaction = Transaction::create($data);

        return $transaction->load('category');
    }

    public function update(Transaction $transaction, array $data): Transaction
    {
        $transaction->update(array_merge($data, ['user_id' => Auth::id()]));

        return $transaction->load('category');
    }
}
