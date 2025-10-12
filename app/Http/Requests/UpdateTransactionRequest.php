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
            'amount' => 'sometimes|numeric',
            'is_income' => 'sometimes|boolean',
            'recurring' => 'sometimes|boolean',
            'transaction_date' => 'sometimes|date',
            'category_id' => 'sometimes|exists:categories,id',
        ];
    }
}
