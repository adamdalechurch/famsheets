<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        return response()->json(Transaction::with(['transactionItems.category'])->get());
        // return response()->json(Transaction::with(['transactionItems.category', 'transactionSchedule', 'incomeSource'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // 'user_id' => 'required|exists:users,id',
            'user_group_id' => 'nullable|exists:user_groups,id',
            'description' => 'required|string|max:255',
            'total' => 'required|numeric',
            'is_income' => 'required|boolean',
            'recurring' => 'required|boolean',
            // 'transaction_schedule_id' => 'nullable|exists:transaction_schedule,id',
            // 'income_source_id' => ['nullable', Rule::requiredIf($request->is_income), 'exists:income_sources,id'],
            'transaction_date' => 'required|date',
            'transaction_items' => 'required|array',
            'transaction_items.*.category_id' => 'required|exists:categories,id',
            'transaction_items.*.amount' => 'required|numeric|min:0',
        ]);

        $validated['user_id'] = Auth::id();

        $transaction = Transaction::create($validated);

        foreach ($validated['transaction_items'] as $item) {
            $transaction->transactionItems()->create($item);
        }

        return response()->json($transaction->load('transactionItems.category'), 201);
    }

    public function show(Transaction $transaction)
    {
        // return response()->json($transaction->load('transactionItems.category'));
       // with transaction items, and transaction schedule date
        return response()->json($transaction->load(['transactionItems.category', 'transactionSchedule', 'incomeSource']));
    }   

public function update(Request $request, Transaction $transaction)
{
    $validated = $request->validate([
        'description' => 'sometimes|string|max:255',
        'total' => 'sometimes|numeric',
        'is_income' => 'sometimes|boolean',
        'recurring' => 'sometimes|boolean',
        'transaction_date' => 'sometimes|date',
        'transaction_items' => 'sometimes|array',
        'transaction_items.*.category_id' => 'required_with:transaction_items|exists:categories,id',
        'transaction_items.*.amount' => 'required_with:transaction_items|numeric|min:0',
    ]);

    // Update base fields including user_id
    $transaction->update(array_merge($validated, [
        'user_id' => Auth::id(),
    ]));

    // If items provided, replace them
    if (isset($validated['transaction_items'])) {
        $transaction->transactionItems()->delete();

        foreach ($validated['transaction_items'] as $item) {
            $transaction->transactionItems()->create($item);
        }
    }

    return response()->json($transaction->load('transactionItems.category'));
}


    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return response()->json(['message' => 'Transaction deleted']);
    }
}
