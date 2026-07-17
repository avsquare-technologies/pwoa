<?php

namespace App\Console\Commands;

use App\Services\TokenWorkflowService;
use Illuminate\Console\Command;

class IssuerAccountSetCommand extends Command
{
    protected $signature = 'issuer:accountset';

    protected $description = 'Interactive issuer AccountSet manager (fields + flags)';

    protected TokenWorkflowService $service;

    public function __construct(TokenWorkflowService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function handle()
    {
        $this->info("\n📡 Loading issuer account settings...\n");

        $settings = $this->service->getIssuerFullSettings();

        $this->line("Issuer Address: {$settings['address']}\n");

        $this->table(
            ['Field', 'Current Value'],
            [
                ['Domain', $settings['Domain'] ?? 'Not Set'],

                ['TransferRate',
                    $settings['TransferRatePercent'] !== null
                        ? $settings['TransferRatePercent'].'%'
                        : 'Not Set',
                ],

                ['TickSize', $settings['TickSize'] ?? 'Not Set'],
                ['EmailHash', $settings['EmailHash'] ?? 'Not Set'],
                ['MessageKey', $settings['MessageKey'] ?? 'Not Set'],
                ['NFTokenMinter', $settings['NFTokenMinter'] ?? 'Not Set'],
            ]
        );

        $mode = $this->choice(
            "\nWhat do you want to update?",
            ['Field Value', 'Flag (Set/Clear)']
        );

        /*
        |--------------------------------------------------------------------------
        | FIELD MODE
        |--------------------------------------------------------------------------
        */
        if ($mode === 'Field Value') {

            $field = $this->choice(
                'Select field to update:',
                ['Domain', 'TransferRate', 'TickSize', 'EmailHash', 'MessageKey', 'NFTokenMinter']
            );

            /*
            |--------------------------------------------------------------------------
            | TransferRate Special Input
            |--------------------------------------------------------------------------
            */
            if ($field === 'TransferRate') {

                $percent = (float) $this->ask(
                    'Enter transfer fee percent (example: 2.5)'
                );

                $newValue = $this->service->percentToTransferRate($percent);

                $this->line("Stored XRPL TransferRate: {$newValue}");
            } else {
                $newValue = $this->ask("Enter new value for {$field}");
            }

            $confirm = $this->confirm('Apply this update?', true);

            if (! $confirm) {
                $this->warn('Cancelled.');

                return self::SUCCESS;
            }

            $this->info("\nSubmitting update...\n");

            $res = $this->service->updateIssuerAccountSet(
                'field',
                $field,
                $newValue
            );

            $this->info('✅ Updated successfully!');
            $this->line("TX Hash: {$res['tx_hash']}");
        }

        /*
        |--------------------------------------------------------------------------
        | FLAG MODE
        |--------------------------------------------------------------------------
        */
        if ($mode === 'Flag (Set/Clear)') {

            $flag = $this->choice(
                'Select flag:',
                [
                    'RequireDestTag',
                    'RequireAuth',
                    'DisallowXRP',
                    'DisableMasterKey',
                    'DefaultRipple',
                    'DepositAuth',
                    'GlobalFreeze',
                    'NoFreeze',
                ]
            );

            $action = $this->choice(
                'Set or Clear?',
                ['set', 'clear']
            );

            $confirm = $this->confirm('Apply this flag update?', true);

            if (! $confirm) {
                $this->warn('Cancelled.');

                return self::SUCCESS;
            }

            $this->info("\nSubmitting flag update...\n");

            $res = $this->service->updateIssuerAccountSet(
                'flag',
                $flag,
                $action
            );

            $this->info('✅ Flag updated successfully!');
            $this->line("TX Hash: {$res['tx_hash']}");
        }

        return self::SUCCESS;
    }
}
