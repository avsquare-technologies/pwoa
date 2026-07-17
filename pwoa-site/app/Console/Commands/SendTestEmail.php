<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use App\Mail\DynamicEmail;

class SendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {--type= : The type of the email template to send} {--to= : The email address to send the test to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email using a dynamic email template';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        
        if (!$type) {
            $availableTypes = EmailTemplate::pluck('type')->toArray();
            if (empty($availableTypes)) {
                $this->error("No email templates found in the database.");
                return 1;
            }
            $type = $this->choice('Which template would you like to test?', $availableTypes);
        }

        $to = $this->option('to');

        if (!$to) {
            $defaultEmail = config('services.admin_email', 'admin@example.com');
            $to = $this->ask('Enter the email address to send the test to', $defaultEmail);
        }

        $template = EmailTemplate::where('type', $type)->first();

        if (!$template) {
            $this->error("Email template with type '{$type}' not found.");
            $this->line("Available types:");
            foreach (EmailTemplate::pluck('type') as $availableType) {
                $this->line(" - {$availableType}");
            }
            return 1;
        }

        if (!$template->is_active) {
            $this->warn("Note: The template '{$type}' is currently marked as inactive in the database, but we will send it anyway for testing.");
        }

        $dummyData = $this->generateDummyData($template->available_variables ?? []);

        try {
            Mail::to($to)->send(new DynamicEmail($template, $dummyData));
            $this->info("✅ Test email sent successfully to {$to} using the '{$type}' template.");
            
            $this->line("\n--- Dummy Data Used ---");
            foreach ($dummyData as $key => $value) {
                $this->line("{{$key}}: {$value}");
            }
        } catch (\Exception $e) {
            $this->error("Failed to send email: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    protected function generateDummyData(array $variables)
    {
        $data = [];
        foreach ($variables as $var) {
            $key = trim($var, '{}');
            
            // Generate some sensible defaults based on the variable name
            $data[$key] = match ($key) {
                'user_name' => 'John Doe',
                'course_name' => 'Surface Cleaning Mastery',
                'certificate_number' => 'PWOA-CERT-987654321',
                'score' => '100',
                'certificate_image_url' => 'https://gateway.pinata.cloud/ipfs/QmSabfsnz4nSown2xEVipxpRJmoqW92cE5XGnsudEw6n3y',
                'nft_token_id' => '000900008E215CB97560BE5EF0830AE3E32BC58AA5CE6BAEED1E0A0B01029073',
                'nft_tx_hash' => '4F5EA8AD65C9FC466F119DE5E596FB157565DFD34FC8932C3BEACC8EA556AB08',
                'xrpl_explorer_link' => 'https://explorer.xahau.network/tx/4F5EA8AD65C9FC466F119DE5E596FB157565DFD34FC8932C3BEACC8EA556AB08',
                'login_link' => url('/login'),
                'promo_code' => 'SUMMER2026',
                'discount_amount' => '$50.00',
                'business_name' => 'Doe Pressure Washing LLC',
                'profile_link' => url('/business/doe-pressure-washing'),
                'rejection_reason' => 'Incomplete insurance documentation.',
                default => 'Sample ' . str_replace('_', ' ', \Illuminate\Support\Str::title($key))
            };
        }
        return $data;
    }
}
