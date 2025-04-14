<?php
namespace App\Services\CSV;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NFCU
{
    public function parse_csv($file)
    {
        $path = $file->store('temp');
        $data = array_map('str_getcsv', file(storage_path('app/' . $path)));
        // remove first row (header)
        array_shift($data);
        // remove empty rows
        $data = array_filter($data, function ($row) {
            return !empty(array_filter($row));
        });
        // map to array of transactions
        $transactions = array_map(function ($row) {
        $existing_category = \App\Models\Category::where('name', $row[10])->first();

        $category_id = $existing_category ? $existing_category->id : null;
        if (!$category_id) {
            $category = \App\Models\Category::create(['name' => $row[11]]);
            $category_id = $category->id;
        }

            return [
                'transaction_date' => \Carbon\Carbon::createFromFormat('m/d/Y', $row[1])->format('Y-m-d'),
                'total' => floatval($row[2]),
                'is_income' => ($row[3] == 'Credit') ? 1 : 0,
                'description' => $row[10] == "" ? "Unknown" : $row[10],
                'user_id' => Auth::id(),
                'transaction_items' => [
                    [
                        'category_id' => $category_id,
                        'amount' => floatval($row[2]),
                    ],
                ],
            ];
        }, $data);

        return $transactions;
    }

}