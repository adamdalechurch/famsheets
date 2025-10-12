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
            'amount' => 'required|numeric',
            'is_income' => 'required|boolean',
            'recurring' => 'required|boolean',
            'transaction_date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
        ];
    }
}
