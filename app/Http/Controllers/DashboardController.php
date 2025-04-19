<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
class DashboardController extends Controller
{
    public function index()
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        $totals = DB::table('transactions')
            ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->selectRaw('
                SUM(CASE WHEN transactions.is_income = 1 THEN transaction_items.amount ELSE 0 END) as total_income,
                SUM(CASE WHEN transactions.is_income = 0 THEN transaction_items.amount ELSE 0 END) as total_expense,
                SUM(CASE 
                    WHEN transactions.transaction_date BETWEEN ? AND ? 
                    THEN transaction_items.amount ELSE 0 END) as monthly_total
            ', [$start, $end])
            ->first();

        return response()->json([
            'total_income' => round($totals->total_income, 2),
            'total_expense' => round($totals->total_expense, 2),
            'monthly_total' => round($totals->monthly_total, 2),
        ]);
    }

    public function chartData(Request $request)
    {
        $frequency = $request->query('frequency', 'monthly');
        $data = $this->getChartData($frequency);

        return response()->json([
            'labels' => $data->pluck('label'),
            'values' => $data->pluck('value'),
        ]);
    }

    private function getChartData($frequency)
    {
        $frequencies = [
            'monthly' => ['column' => 'MONTH(transactions.transaction_date)', 'label' => fn($v) => date("F", mktime(0, 0, 0, $v, 1))],
            'weekly' => ['column' => 'WEEK(transactions.transaction_date)', 'label' => fn($v) => "Week $v"],
            'daily' => ['column' => 'DATE(transactions.transaction_date)', 'label' => fn($v) => date("D, M j", strtotime($v))],
            'yearly' => ['column' => 'YEAR(transactions.transaction_date)', 'label' => fn($v) => $v],
        ];

        if (!array_key_exists($frequency, $frequencies)) {
            throw new \Exception("Invalid frequency: $frequency");
        }

        $config = $frequencies[$frequency];
        return $this->aggregateTransactionData($config['column'], $config['label']);
    }

    private function aggregateTransactionData($groupColumn, $labelFormatter)
    {
        return DB::table('transactions')
            ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->selectRaw("{$groupColumn} as label_key, SUM(transaction_items.amount) as total")
            ->groupBy('label_key')
            ->orderBy('label_key')
            ->get()
            ->map(function ($row) use ($labelFormatter) {
                return [
                    'label' => $labelFormatter($row->label_key),
                    'value' => round($row->total, 2),
                ];
            });
    }
public function budgetReport(Request $request)
{
    // Get the date range from query string or default to this month
    $startDate = $request->input('start_date') 
        ? Carbon::parse($request->input('start_date')) 
        : now()->startOfMonth();

    $endDate = $request->input('end_date') 
        ? Carbon::parse($request->input('end_date')) 
        : now()->endOfMonth();

    $budgets = DB::table('budgets')->get();
    $result = [];

    foreach ($budgets as $budget) {
        // Get budget duration & overlap with the selected range
        $budgetStart = Carbon::parse($budget->start_date);
        $budgetEnd = Carbon::parse($budget->end_date);
        $rangeStart = $startDate->copy()->max($budgetStart);
        $rangeEnd = $endDate->copy()->min($budgetEnd);

        // Calculate overlap in months
        $overlapMonths = $rangeStart->lte($rangeEnd)
            ? $rangeStart->diffInMonths($rangeEnd) + 1
            : 0;

        // Skip if no overlap
        if ($overlapMonths === 0) continue;

        $spendingSubquery = DB::table('transaction_items')
    ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
    ->whereBetween('transactions.transaction_date', [$rangeStart->toDateString(), $rangeEnd->toDateString()])
    ->select(
        'transaction_items.category_id',
        DB::raw('SUM(transaction_items.amount) as spent')
    )
    ->groupBy('transaction_items.category_id');

$items = DB::table('budget_items')
    ->join('categories', 'budget_items.category_id', '=', 'categories.id')
    ->leftJoinSub($spendingSubquery, 'spending', function ($join) {
        $join->on('budget_items.category_id', '=', 'spending.category_id');
    })
    ->where('budget_items.budget_id', $budget->id)
    ->select(
        'budget_items.category_id',
        'categories.name as category_name',
        DB::raw('budget_items.amount * ' . $overlapMonths . ' as budgeted'),
        DB::raw('COALESCE(spending.spent, 0) as spent')
    )
    ->get();

        $result[] = [
            'id' => $budget->id,
            'name' => $budget->name,
            'start_date' => $budget->start_date,
            'end_date' => $budget->end_date,
            'items' => $items,
        ];
    }

    return response()->json($result);
}
}
