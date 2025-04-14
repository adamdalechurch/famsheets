<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'description' => 'sometimes|string|max:255',
            'total' => 'sometimes|numeric',
            'is_income' => 'sometimes|boolean',
            'recurring' => 'sometimes|boolean',
            'transaction_date' => 'sometimes|date',
            'transaction_items' => 'sometimes|array',
            'transaction_items.*.category_id' => 'required_with:transaction_items|exists:categories,id',
            'transaction_items.*.amount' => 'required_with:transaction_items|numeric|min:0',
        ];
    }
}
