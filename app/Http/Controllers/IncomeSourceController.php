<?php

namespace App\Http\Controllers;

use App\Models\IncomeSource;
use Illuminate\Http\Request;

class IncomeSourceController extends Controller
{
    public function index()
    {
        return response()->json(IncomeSource::with('category')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'income_source_category_id' => 'required|exists:income_source_categories,id',
        ]);

        $incomeSource = IncomeSource::create($validated);

        return response()->json($incomeSource->load('category'), 201);
    }

    public function update(Request $request, IncomeSource $incomeSource)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'income_source_category_id' => 'sometimes|exists:income_source_categories,id',
        ]);

        $incomeSource->update($validated);

        return response()->json($incomeSource->load('category'));
    }

    public function destroy(IncomeSource $incomeSource)
    {
        $incomeSource->delete();
        return response()->json(['message' => 'Income source deleted']);
    }
}
