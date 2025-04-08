<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function index()
    {
        return response()->json(Transaction::with(['transactionItems.category', 'transactionSchedule', 'incomeSource'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'user_group_id' => 'nullable|exists:user_groups,id',
            'description' => 'required|string|max:255',
            'total' => 'required|numeric',
            'is_income' => 'required|boolean',
            'recurring' => 'required|boolean',
            'transaction_schedule_id' => 'nullable|exists:transaction_schedule,id',
            'income_source_id' => ['nullable', Rule::requiredIf($request->is_income), 'exists:income_sources,id'],
            'transaction_date' => 'required|date',
            'items' => 'required|array',
            'items.*.category_id' => 'required|exists:categories,id',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        $transaction = Transaction::create($validated);

        foreach ($validated['items'] as $item) {
            $transaction->transactionItems()->create($item);
        }

        return response()->json($transaction->load('transactionItems.category'), 201);
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'description' => 'sometimes|string|max:255',
            'total' => 'sometimes|numeric',
            'is_income' => 'sometimes|boolean',
            'recurring' => 'sometimes|boolean',
            // 'transaction_schedule_id' => 'nullable|exists:transaction_schedule,id',
            // 'income_source_id' => ['nullable', Rule::requiredIf($request->is_income), 'exists:income_sources,id'],
            'transaction_date' => 'sometimes|date',
            'items' => 'sometimes|array',
            'items.*.category_id' => 'required_with:items|exists:categories,id',
            'items.*.amount' => 'required_with:items|numeric|min:0',
        ]);

        $transaction->update($validated);

        if (isset($validated['items'])) {
            $transaction->transactionItems()->delete();
            foreach ($validated['items'] as $item) {
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
