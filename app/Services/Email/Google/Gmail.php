<?php

namespace App\Services\Email\Google;

use Google\Client as Google_Client;
use Google\Service\Gmail as Google_Service_Gmail;
use Google\Service\Gmail\Message as Google_Service_Gmail_Message;
use App\Models\DebugText;
use App\Models\Transaction;

class Gmail
{
public function import_bank_emails()
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
        DebugText::create(['text' => json_encode($decoded)]);

        $transaction = $this->parseEmailToTransaction($decoded);

        if ($transaction) {
            $transaction['user_id'] = 1;
            $created = Transaction::create($transaction);
            foreach ($transaction['transaction_items'] as $item) {
                $created->transactionItems()->create($item);
            }
        }
    }
}
public function parseEmailToTransaction($emailBody)
{
    // Customize parsing based on your bank's email format
    preg_match('/Transaction for \$([0-9,.]+)/', $emailBody, $amountMatch);
    // preg_match('/Date: (\d{2}\/\d{2}\/\d{4})/', $emailBody, $dateMatch);
    preg_match('/on (\d{2}\/\d{2}\/\d{2})/', $emailBody, $dateMatch);
    preg_match('/debit card \d{4} at (.*)/', $emailBody, $descMatch);

    if (!$amountMatch || !$dateMatch || !$descMatch) return null;

    $amount = floatval(str_replace(',', '', $amountMatch[1]));
    $date = \Carbon\Carbon::createFromFormat('m/d/y', $dateMatch[1])->format('Y-m-d');
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