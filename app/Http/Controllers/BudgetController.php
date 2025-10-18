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

    // Count distinct months with income data
    $incomeMonths = DB::table('transactions')
        ->where('user_id', $userId)
        ->where('is_income', true)
        ->whereBetween('transaction_date', [$startDate, now()])
        ->selectRaw('COUNT(DISTINCT DATE_FORMAT(transaction_date, "%Y-%m")) as month_count')
        ->value('month_count');

    if (!$incomeMonths || $incomeMonths == 0) {
        return response()->json(['message' => 'No income data available to create a smart budget.'], 400);
    }

    // Calculate average monthly income
    $monthlyIncomeObj = DB::table('transactions')
        ->where('user_id', $userId)
        ->where('is_income', true)
        ->whereBetween('transaction_date', [$startDate, now()])
        ->selectRaw('SUM(amount) / ? as avg_income', [$incomeMonths])
        ->first();

    $monthlyIncome = $monthlyIncomeObj ? (float) $monthlyIncomeObj->avg_income : 0;

    if ($monthlyIncome <= 0) {
        return response()->json(['message' => 'No income data available to create a smart budget.'], 400);
    }

    // Count distinct months with expense data
    $expenseMonths = DB::table('transactions')
        ->where('user_id', $userId)
        ->where('is_income', false)
        ->whereBetween('transaction_date', [$startDate, $endDate])
        ->selectRaw('COUNT(DISTINCT DATE_FORMAT(transaction_date, "%Y-%m")) as month_count')
        ->value('month_count');

    if (!$expenseMonths || $expenseMonths == 0) {
        return response()->json(['message' => 'No spending data found.'], 404);
    }

    // Get average monthly expenses by category
    $categoryAverages = DB::table('transactions')
        ->whereBetween('transaction_date', [$startDate, $endDate])
        ->where('user_id', $userId)
        ->where('is_income', false)
        ->groupBy('category_id')
        ->select('category_id', DB::raw("ABS(SUM(amount) / $expenseMonths) as monthly_average"))
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
        'months_used_for_income' => $incomeMonths,
        'months_used_for_expenses' => $expenseMonths,
    ]);
}


}
