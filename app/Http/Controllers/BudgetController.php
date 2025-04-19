<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

public function createSmartBudget()
{
    $userId = Auth::id();

    $startDate = Carbon::now()->subMonths(12)->startOfMonth();
    $endDate = Carbon::now()->subMonth()->endOfMonth();

    // Get average monthly income
    // $monthlyIncome = DB::table('transactions')
    //     ->join('transaction_items as ti', 't.id', '=', 'ti.transaction_id')
    //     ->where('ti.amount', '>', 0) // Income is positive
    //     ->select(DB::raw('SUM(ti.amount) / 3 as avg_income'))
    //     ->value('avg_income') ?? 0;
$monthlyIncomeObj = DB::table('transactions')
    ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
    ->where('transactions.user_id', $userId)
    ->where('transactions.is_income', true)
    ->whereBetween('transactions.transaction_date', [$startDate, now()])
    ->groupBy('transactions.user_id')
    ->select(DB::raw('SUM(transaction_items.amount) / 12 as avg_income'))
    ->first();

    $monthlyIncome = $monthlyIncomeObj ? (float) $monthlyIncomeObj->avg_income : 0;
    
    if ($monthlyIncome <= 0) {
        return response()->json(['message' => 'No income data available to create a smart budget.'], 400);
    }

    // Get average monthly expenses by category
    $categoryAverages = DB::table('transaction_items as ti')
        ->join('transactions as t', 'ti.transaction_id', '=', 't.id')
        ->whereBetween('t.transaction_date', [$startDate, $endDate])
        ->where('t.user_id', $userId)
        ->where('t.is_income', false)
        ->groupBy('ti.category_id')
        ->select('ti.category_id', DB::raw('ABS(SUM(ti.amount) / 12) as monthly_average'))
        ->get();

    if ($categoryAverages->isEmpty()) {
        return response()->json(['message' => 'No spending data found.'], 404);
    }

    $totalPlanned = $categoryAverages->sum('monthly_average');

    // Scale down if over budget
    $scalingFactor = $totalPlanned > $monthlyIncome ? $monthlyIncome / $totalPlanned : 1;

    // Create new budget
    $nextMonth = Carbon::now()->addMonth()->startOfMonth();
    $budget = Budget::create([
        'user_id' => $userId,
        'name' => 'Smart Budget - ' . $nextMonth->format('F Y'),
        'start_date' => $nextMonth->toDateString(),
        'end_date' => $nextMonth->copy()->endOfMonth()->toDateString(),
    ]);

    foreach ($categoryAverages as $category) {
        BudgetItem::create([
            'budget_id' => $budget->id,
            'category_id' => $category->category_id,
            'amount' => round($category->monthly_average * $scalingFactor, 2),
        ]);
    }

    return response()->json([
        'message' => 'Smart budget created successfully.',
        'budget_id' => $budget->id,
        'income_cap' => round($monthlyIncome, 2),
        'scaling_factor' => round($scalingFactor, 3),
    ]);
}

}
