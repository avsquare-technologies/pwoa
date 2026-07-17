<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TokenWorkflowService;
use Illuminate\Support\Facades\Log;

class RunTokenWorkflow extends Command
{
    protected $signature = 'token:run
        {currency : Token currency code (e.g. FEE)}
        {amount : Amount to mint}';

    protected $description = 'Run the full token issuance workflow (issuer → trustline → mint → verify)';

    protected TokenWorkflowService $service;

    public function __construct(TokenWorkflowService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function handle()
    {
        $currency = strtoupper($this->argument('currency'));
        $amount   = (float) $this->argument('amount');

        if ($amount <= 0) {
            $this->error('Amount must be greater than zero.');
            return self::FAILURE;
        }

        $this->info("🚀 Starting Token Workflow");
        $this->line("Currency: {$currency}");
        $this->line("Amount:   {$amount}");

        Log::info("CLI Token Workflow started", compact('currency', 'amount'));

        try {
            $this->info('1️⃣ Configuring issuer...');
            $step1 = $this->service->configureIssuer();
            $this->line('   ✓ Issuer configured');

            $this->info('2️⃣ Creating trust line...');
            $step2 = $this->service->makeTrustLine($currency);
            $this->line('   ✓ Trust line created');

            $this->info('3️⃣ Minting tokens...');
            $step3 = $this->service->sendToken($currency, $amount);
            $this->line('   ✓ Tokens sent');

            $this->info('4️⃣ Confirming balances...');
            $step4 = $this->service->confirmBalances($currency);
            $this->line('   ✓ Balances verified');

            $this->newLine();
            $this->info('✅ Token workflow completed successfully');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->newLine();
            $this->error('❌ Token workflow failed');
            $this->error($e->getMessage());

            Log::error('CLI Token Workflow Failed', [
                'currency' => $currency,
                'amount'   => $amount,
                'error'    => $e->getMessage(),
            ]);

            return self::FAILURE;
        }
    }
}
