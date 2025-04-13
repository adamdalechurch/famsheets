<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\DebugText;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Google\Client as Google_Client;
use Google\Service\Gmail as Google_Service_Gmail;
use Google\Service\Gmail\Message as Google_Service_Gmail_Message;

class TransactionController extends Controller
{
public function index(Request $request)
{
    $query = Transaction::with(['transactionItems.category']);

    if ($request->has('paginate') && $request->boolean('paginate')) {
        $perPage = $request->input('per_page', 10);
        return response()->json($query->paginate($perPage));
    }

    return response()->json($query->get());
}
    public function store(Request $request)
    {
        $validated = $request->validate([
            // 'user_id' => 'required|exists:users,id',
            'user_group_id' => 'nullable|exists:user_groups,id',
            'description' => 'required|string|max:255',
            'total' => 'required|numeric',
            'is_income' => 'required|boolean',
            'recurring' => 'required|boolean',
            // 'transaction_schedule_id' => 'nullable|exists:transaction_schedule,id',
            // 'income_source_id' => ['nullable', Rule::requiredIf($request->is_income), 'exists:income_sources,id'],
            'transaction_date' => 'required|date',
            'transaction_items' => 'required|array',
            'transaction_items.*.category_id' => 'required|exists:categories,id',
            'transaction_items.*.amount' => 'required|numeric|min:0',
        ]);

        $validated['user_id'] = Auth::id();

        $transaction = Transaction::create($validated);

        foreach ($validated['transaction_items'] as $item) {
            $transaction->transactionItems()->create($item);
        }

        return response()->json($transaction->load('transactionItems.category'), 201);
    }

    public function show(Transaction $transaction)
    {
        // return response()->json($transaction->load('transactionItems.category'));
       // with transaction items, and transaction schedule date
        return response()->json($transaction->load(['transactionItems.category', 'transactionSchedule', 'incomeSource']));
    }   

public function update(Request $request, Transaction $transaction)
{
    $validated = $request->validate([
        'description' => 'sometimes|string|max:255',
        'total' => 'sometimes|numeric',
        'is_income' => 'sometimes|boolean',
        'recurring' => 'sometimes|boolean',
        'transaction_date' => 'sometimes|date',
        'transaction_items' => 'sometimes|array',
        'transaction_items.*.category_id' => 'required_with:transaction_items|exists:categories,id',
        'transaction_items.*.amount' => 'required_with:transaction_items|numeric|min:0',
    ]);

    // Update base fields including user_id
    $transaction->update(array_merge($validated, [
        'user_id' => Auth::id(),
    ]));

    // If items provided, replace them
    if (isset($validated['transaction_items'])) {
        $transaction->transactionItems()->delete();

        foreach ($validated['transaction_items'] as $item) {
            $transaction->transactionItems()->create($item);
        }
    }

    return response()->json($transaction->load('transactionItems.category'));
}


    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return response()->json(['message' => 'Transaction deleted']);
    }

// bulk update:
public function bulkUpdate(Request $request)
{
    $validated = $request->validate([
        'transactions' => 'required|array',
        // 'transactions.*.id' => 'required|exists:transactions,id',
        'transactions.*.description' => 'sometimes|string|max:255',
        'transactions.*.total' => 'sometimes|numeric',
        'transactions.*.is_income' => 'sometimes|boolean',
        'transactions.*.recurring' => 'sometimes|boolean',
        'transactions.*.transaction_date' => 'sometimes|date',
        'transactions.*.transaction_items' => 'sometimes|array',
        'transactions.*.transaction_items.*.category_id' => 'required_with:transaction_items|exists:categories,id',
        'transactions.*.transaction_items.*.amount' => 'required_with:transaction_items|numeric|min:0',
    ]);

    foreach ($validated['transactions'] as $data) {
        $transaction = new Transaction;
        if(isset($data['id'])){
            $transaction = Transaction::find($data['id']);
            $transaction->update($data);
        } else{
            $data['user_id'] = Auth::id();
            $transaction = $transaction->create($data);
            $transaction->save();
        }

        if (isset($data['transaction_items'])) {
            $transaction->transactionItems()->delete();

            foreach ($data['transaction_items'] as $item) {
                if(!isset($item['transaction_id'])){
                    $item['transaction_id'] = $transaction->id;
                }

                $transaction->transactionItems()->create($item);
            }
        }
    }

    return response()->json(['message' => 'Transactions updated successfully']);
    }

    public function parse_csv(Request $request)
    {
        $validated = $request->validate([
            'csv' => 'required|file|mimes:csv,txt',
        ]);

        $file = $validated['csv'];
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
            $category = \App\Models\Category::create(['name' => $row[10]]);
            $category_id = $category->id;
        }

            return [
                'transaction_date' => \Carbon\Carbon::createFromFormat('m/d/Y', $row[1])->format('Y-m-d'),
                'total' => floatval($row[2]),
                'is_income' => ($row[3] == 'Credit') ? 1 : 0,
                'description' => $row[11] == "" ? "Unknown" : $row[11],
                'user_id' => Auth::id(),
                'transaction_items' => [
                    [
                        'category_id' => $category_id,
                        'amount' => floatval($row[2]),
                    ],
                ],
            ];
        }, $data);

        return response()->json($transactions);
    }

public function importBankEmails()
{
    $client = $this->authenticate_google();
    $service = new Google_Service_Gmail($client);

    $query = 'from:adamdchurch92@gmail.com subject:Transaction newer_than:7d';
    $messages = $service->users_messages->listUsersMessages('me', ['q' => $query]);

    foreach ($messages->getMessages() as $msg) {
        $message = $service->users_messages->get('me', $msg->getId(), ['format' => 'full']);
        $payload = $message->getPayload();

        // Save full payload for debugging
        DebugText::create(['text' => json_encode($payload)]);

        $bodyData = null;

        // Try to find the 'text/plain' part
        foreach ($payload->getParts() as $part) {
            if ($part->getMimeType() === 'text/plain') {
                $bodyData = $part->getBody()->getData();
                break;
            }
        }

        if (!$bodyData) continue;

        $decoded = base64_decode(strtr($bodyData, '-_', '+/'));

        $transaction = $this->parseEmailToTransaction($decoded);

        if ($transaction) {
            $transaction['user_id'] = 1;
            $created = Transaction::create($transaction);
            foreach ($transaction['transaction_items'] as $item) {
                $created->transactionItems()->create($item);
            }
        }
    }

    return response()->json(['status' => 'Import complete']);
}

protected function parseEmailToTransaction($emailBody)
{
    // Customize parsing based on your bank's email format
    preg_match('/Amount: \$([0-9,.]+)/', $emailBody, $amountMatch);
    preg_match('/Date: (\d{2}\/\d{2}\/\d{4})/', $emailBody, $dateMatch);
    preg_match('/Description: (.*)/', $emailBody, $descMatch);

    if (!$amountMatch || !$dateMatch || !$descMatch) return null;

    $amount = floatval(str_replace(',', '', $amountMatch[1]));
    $date = \Carbon\Carbon::createFromFormat('m/d/Y', $dateMatch[1])->format('Y-m-d');
    $description = trim($descMatch[1]);

    $category = \App\Models\Category::firstOrCreate(['name' => 'Bank Import']);

    return [
        'transaction_date' => $date,
        'total' => $amount,
        'is_income' => false, // or detect income from body
        'description' => $description,
        'recurring' => false,
        'transaction_items' => [
            [
                'category_id' => $category->id,
                'amount' => $amount,
            ]
        ],
    ];
}

private function authenticate_google()
{
    $client = new Google_Client();
    $client->setApplicationName('Gmail API PHP Quickstart');
    $client->setScopes(Google_Service_Gmail::GMAIL_READONLY);
    echo storage_path('app/google/credentials.json');
    $client->setAuthConfig(storage_path('app/google/credentials.json'));
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Load previously authorized credentials from a file.
    $credentialsPath = storage_path('app/google/token.json');
    if (file_exists($credentialsPath)) {
        $accessToken = json_decode(file_get_contents($credentialsPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new \Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0700, true);
        }
        file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
    }

    return $client;
 }
}