<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportBankEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:bank-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
   public function handle()
{
    $controller = app(\App\Http\Controllers\TransactionController::class);
    $controller->importBankEmails();
    $this->info('Bank emails imported successfully.');

    return Command::SUCCESS;
}
}
