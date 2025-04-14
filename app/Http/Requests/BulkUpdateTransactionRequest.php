<?php

// app/Http/Requests/BulkUpdateTransactionRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateTransactionRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'transactions' => 'required|array',
            'transactions.*.description' => 'sometimes|string|max:255',
            'transactions.*.total' => 'sometimes|numeric',
            'transactions.*.is_income' => 'sometimes|boolean',
            'transactions.*.recurring' => 'sometimes|boolean',
            'transactions.*.transaction_date' => 'sometimes|date',
            'transactions.*.transaction_items' => 'sometimes|array',
            'transactions.*.transaction_items.*.category_id' => 'required_with:transaction_items|exists:categories,id',
            'transactions.*.transaction_items.*.amount' => 'required_with:transaction_items|numeric|min:0',
        ];
    }
}
