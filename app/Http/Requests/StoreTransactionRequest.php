<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'user_group_id' => 'nullable|exists:user_groups,id',
            'description' => 'required|string|max:255',
            'total' => 'required|numeric',
            'is_income' => 'required|boolean',
            'recurring' => 'required|boolean',
            'transaction_date' => 'required|date',
            'transaction_items' => 'required|array',
            'transaction_items.*.category_id' => 'required|exists:categories,id',
            'transaction_items.*.amount' => 'required|numeric|min:0',
        ];
    }
}
