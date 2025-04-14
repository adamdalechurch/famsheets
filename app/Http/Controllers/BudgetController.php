<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $query = Budget::with(['budgetItems.category']);

        if ($request->boolean('paginate')) {
            $perPage = $request->input('per_page', 10);
            return response()->json($query->paginate($perPage));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'total' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'budget_items' => 'required|array',
            'budget_items.*.category_id' => 'required|exists:categories,id',
            'budget_items.*.amount' => 'required|numeric|min:0',
        ]);

        $validated['user_id'] = Auth::id();

        $budget = Budget::create($validated);

        foreach ($validated['budget_items'] as $item) {
            $budget->budgetItems()->create($item);
        }

        return response()->json($budget->load('budgetItems.category'), 201);
    }

    public function show(Budget $budget)
    {
        return response()->json($budget->load('budgetItems.category'));
    }

    public function update(Request $request, Budget $budget)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date',
            'budget_items' => 'sometimes|array',
            'budget_items.*.category_id' => 'required_with:budget_items|exists:categories,id',
            'budget_items.*.amount' => 'required_with:budget_items|numeric|min:0',
        ]);

        $budget->update(array_merge($validated, ['user_id' => Auth::id()]));

        if (isset($validated['budget_items'])) {
            $budget->budgetItems()->delete();

            foreach ($validated['budget_items'] as $item) {
                $budget->budgetItems()->create($item);
            }
        }

        return response()->json($budget->load('budgetItems.category'));
    }

    public function destroy(Budget $budget)
    {
        $budget->delete();
        return response()->json(['message' => 'Budget deleted']);
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'budgets' => 'required|array',
            'budgets.*.name' => 'required|string|max:255',
            'budgets.*.start_date' => 'sometimes|date',
            'budgets.*.end_date' => 'nullable|date',
            'budgets.*.budget_items' => 'sometimes|array',
            'budgets.*.budget_items.*.category_id' => 'required_with:budget_items|exists:categories,id',
            'budgets.*.budget_items.*.amount' => 'required_with:budget_items|numeric|min:0',
        ]);

        foreach ($validated['budgets'] as $data) {
            $budget = isset($data['id']) ? Budget::find($data['id']) : new Budget(['user_id' => Auth::id()]);

            $budget->fill($data)->save();

            if (isset($data['budget_items'])) {
                $budget->budgetItems()->delete();
                foreach ($data['budget_items'] as $item) {
                    $budget->budgetItems()->create($item);
                }
            }
        }

        return response()->json(['message' => 'Budgets updated successfully']);
    }
}
