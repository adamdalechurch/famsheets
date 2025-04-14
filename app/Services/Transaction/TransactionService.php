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

        foreach ($data['transaction_items'] as $item) {
            $transaction->transactionItems()->create($item);
        }

        return $transaction->load('transactionItems.category');
    }

    public function update(Transaction $transaction, array $data): Transaction
    {
        $transaction->update(array_merge($data, ['user_id' => Auth::id()]));

        if (isset($data['transaction_items'])) {
            $transaction->transactionItems()->delete();

            foreach ($data['transaction_items'] as $item) {
                $transaction->transactionItems()->create($item);
            }
        }

        return $transaction->load('transactionItems.category');
    }
}
