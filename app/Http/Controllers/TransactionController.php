<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
public function index(Request $request)
{
    $query = Transaction::with(['transactionItems.category']);

    if ($request->has('paginate') && $request->boolean('paginate')) {
        $perPage = $request->input('per_page', 10);
        return response()->json($query->paginate($perPage));
    }

    return response()->json($query->get());
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

// bulk update:
public function bulkUpdate(Request $request)
{
    $validated = $request->validate([
        'transactions' => 'required|array',
        // 'transactions.*.id' => 'required|exists:transactions,id',
        'transactions.*.description' => 'sometimes|string|max:255',
        'transactions.*.total' => 'sometimes|numeric',
        'transactions.*.is_income' => 'sometimes|boolean',
        'transactions.*.recurring' => 'sometimes|boolean',
        'transactions.*.transaction_date' => 'sometimes|date',
        'transactions.*.transaction_items' => 'sometimes|array',
        'transactions.*.transaction_items.*.category_id' => 'required_with:transaction_items|exists:categories,id',
        'transactions.*.transaction_items.*.amount' => 'required_with:transaction_items|numeric|min:0',
    ]);

    foreach ($validated['transactions'] as $data) {
        $transaction = new Transaction;
        if(isset($data['id'])){
            $transaction = Transaction::find($data['id']);
            $transaction->update($data);
        } else{
            $data['user_id'] = Auth::id();
            $transaction = $transaction->create($data);
            $transaction->save();
        }

        if (isset($data['transaction_items'])) {
            $transaction->transactionItems()->delete();

            foreach ($data['transaction_items'] as $item) {
                if(!isset($item['transaction_id'])){
                    $item['transaction_id'] = $transaction->id;
                }

                $transaction->transactionItems()->create($item);
            }
        }
    }

    return response()->json(['message' => 'Transactions updated successfully']);
    }

    public function parse_csv(Request $request)
    {
        $validated = $request->validate([
            'csv' => 'required|file|mimes:csv,txt',
        ]);

        $file = $validated['csv'];
        $path = $file->store('temp');
        $data = array_map('str_getcsv', file(storage_path('app/' . $path)));
        // remove first row (header)
        array_shift($data);
        // remove empty rows
        $data = array_filter($data, function ($row) {
            return !empty(array_filter($row));
        });
        // map to array of transactions
        $transactions = array_map(function ($row) {
        $existing_category = \App\Models\Category::where('name', $row[10])->first();

        $category_id = $existing_category ? $existing_category->id : null;
        if (!$category_id) {
            $category = \App\Models\Category::create(['name' => $row[10]]);
            $category_id = $category->id;
        }

            return [
                'transaction_date' => \Carbon\Carbon::createFromFormat('m/d/Y', $row[1])->format('Y-m-d'),
                'total' => floatval($row[2]),
                'is_income' => ($row[3] == 'Credit') ? 1 : 0,
                'description' => $row[11] == "" ? "Unknown" : $row[11],
                'user_id' => Auth::id(),
                'transaction_items' => [
                    [
                        'category_id' => $category_id,
                        'amount' => floatval($row[2]),
                    ],
                ],
            ];
        }, $data);

        return response()->json($transactions);
    }
}
