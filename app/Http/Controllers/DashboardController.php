<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}
