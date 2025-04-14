<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Requests\BulkUpdateTransactionRequest;
use Illuminate\Http\Request;
use App\Services\Email\Gmail;
use App\Services\CSV\NFCU;
use App\Services\Transaction\TransactionService;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['transactionItems.category']);

        if ($request->boolean('paginate')) {
            return response()->json($query->paginate($request->input('per_page', 10)));
        }

        return response()->json($query->get());
    }

    public function store(StoreTransactionRequest $request)
    {
        $transaction = $this->transactionService->create($request->validated());
        return response()->json($transaction, 201);
    }

    public function show(Transaction $transaction)
    {
        return response()->json($transaction->load([
            'transactionItems.category',
            'transactionSchedule',
            'incomeSource'
        ]));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $transaction = $this->transactionService->update($transaction, $request->validated());
        return response()->json($transaction);
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return response()->json(['message' => 'Transaction deleted']);
    }

    public function bulkUpdate(BulkUpdateTransactionRequest $request)
    {
        foreach ($request->validated()['transactions'] as $data) {
            $transaction = isset($data['id']) 
                ? Transaction::find($data['id'])->fill($data)
                : new Transaction(array_merge($data, ['user_id' => Auth::id()]));

            $transaction->save();

            if (isset($data['transaction_items'])) {
                $transaction->transactionItems()->delete();
                $transaction->transactionItems()->createMany($data['transaction_items']);
            }
        }

        return response()->json(['message' => 'Transactions updated successfully']);
    }

    public function parse_csv(Request $request)
    {
        $request->validate(['csv' => 'required|file|mimes:csv,txt']);

        $nfc = new NFCU;
        $transactions = $nfc->parse_csv($request->file('csv'));

        return response()->json($transactions);
    }

    public function importBankEmails()
    {
        (new Gmail)->getBankEmails();
        return response()->json(['status' => 'Import complete']);
    }
}
