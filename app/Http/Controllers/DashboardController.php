<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totals = DB::table('transactions')
            ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->selectRaw('
                SUM(CASE WHEN transactions.is_income = 1 THEN transaction_items.amount ELSE 0 END) as total_income,
                SUM(CASE WHEN transactions.is_income = 0 THEN transaction_items.amount ELSE 0 END) as total_expense,
                SUM(CASE 
                    WHEN transactions.transaction_date BETWEEN ? AND ? 
                    THEN transaction_items.amount ELSE 0 END) as monthly_total
            ', [now()->startOfMonth(), now()->endOfMonth()])
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
      
      $data = $this->get_chart_data($frequency);

        return response()->json([
            'labels' => $data->pluck('label'),
            'values' => $data->pluck('value'),
        ]);
    }

    private function get_chart_data($frequency){
      switch ($frequency) {
        case 'monthly':
          return $this->get_monthly_chart_data();
        case 'weekly':
          return $this->get_weekly_chart_data();
        case 'daily':
          return $this->get_daily_chart_data();
        case 'yearly':
          return $this->get_yearly_chart_data();
        default:
          throw new \Exception("Invalid frequency: $frequency");
      }
    }

    private function get_monthly_chart_data(){
      return  DB::table('transactions')
        ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
        ->selectRaw('MONTH(transactions.transaction_date) as month, SUM(transaction_items.amount) as total')
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->map(function ($row) {
            return [
                'label' => date("F", mktime(0, 0, 0, $row->month, 1)),
                'value' => round($row->total, 2)
            ];
        });
    }

    private function get_weekly_chart_data(){
      return   DB::table('transactions')
        ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
        ->selectRaw('WEEK(transactions.transaction_date) as week, SUM(transaction_items.amount) as total')
        ->groupBy('week')
        ->orderBy('week')
        ->get()
        ->map(function ($row) {
            return [
                'label' => "Week $row->week",
                'value' => round($row->total, 2)
            ];
        });
    }

    private function get_daily_chart_data(){
      return  DB::table('transactions')
        ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
        ->selectRaw('DATE(transactions.transaction_date) as date, SUM(transaction_items.amount) as total')
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->map(function ($row) {
            return [
                'label' => date("D, M j", strtotime($row->date)),
                'value' => round($row->total, 2)
            ];
        });
    }

    private function get_yearly_chart_data(){
      return  DB::table('transactions')
        ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
        ->selectRaw('YEAR(transactions.transaction_date) as year, SUM(transaction_items.amount) as total')
        ->groupBy('year')
        ->orderBy('year')
        ->get()
        ->map(function ($row) {
            return [
                'label' => $row->year,
                'value' => round($row->total, 2)
            ];
        });
    }
}
