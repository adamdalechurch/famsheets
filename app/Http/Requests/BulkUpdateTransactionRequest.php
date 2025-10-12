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
            'transactions.*.amount' => 'sometimes|numeric',
            'transactions.*.is_income' => 'sometimes|boolean',
            'transactions.*.recurring' => 'sometimes|boolean',
            'transactions.*.transaction_date' => 'sometimes|date',
            'transactions.*.category_id' => 'sometimes|exists:categories,id',
        ];
    }
}
