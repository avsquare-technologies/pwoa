<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use App\Models\EmailTemplate;
use App\Mail\DynamicEmail;
use Illuminate\Support\Facades\Mail;

header('Content-Type: text/plain');

$email = $_GET['email'] ?? null;

if (!$email) {
    echo "Please provide an email address, e.g., ?email=yourname@example.com\n";
    exit;
}

echo "Sending test emails to: {$email}\n";

try {
    // Send Welcome Email
    $welcomeTemplate = EmailTemplate::where('type', 'welcome')->where('is_active', true)->first();
    if ($welcomeTemplate) {
        echo "Sending Welcome Email template...\n";
        Mail::to($email)->send(new DynamicEmail($welcomeTemplate, [
            'user_name' => 'Test User',
            'login_link' => route('dashboard'),
        ]));
        echo "Welcome Email sent successfully!\n";
    } else {
        echo "Welcome Email template not found or inactive.\n";
    }

    // Auto-update database record for testing the new promo code copy
    EmailTemplate::updateOrCreate(
        ['type' => 'promo_code'],
        [
            'content' => '<h1>Your Exclusive Promo Code Inside!</h1><p>Dear {user_name},</p><p>Thank you for choosing Pressure Washers of America (PWOA). We are excited to support your business journey and help you grow inside our professional community.</p><p>As a warm welcome, we are pleased to offer you an exclusive discount on your next membership purchase or transaction. Use the promo code below during checkout to claim your offer:</p><div style="background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 20px; text-align: center; margin: 25px 0;"><span style="font-size: 14px; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 5px; font-weight: bold;">Promo Code</span><strong style="font-size: 28px; color: #0f172a; letter-spacing: 2px;">{promo_code}</strong><span style="display: block; margin-top: 10px; font-size: 16px; color: #0284c7; font-weight: bold;">Save {discount_amount} Off Your Next Purchase!</span></div><p>This is a limited-time offer, so make sure to take advantage of it soon to unlock the full potential of your PWOA membership benefits, including verified business listings, quiz certifications, and exclusive member resources.</p><p>If you need any assistance applying your code, please don\'t hesitate to reach out to our dedicated support team.</p><p>Best regards,<br><strong>The PWOA Team</strong></p>'
        ]
    );

    // Send Promo Code Email
    $promoTemplate = EmailTemplate::where('type', 'promo_code')->where('is_active', true)->first();
    if ($promoTemplate) {
        echo "Sending Promo Code Email template...\n";
        sleep(1);
        Mail::to($email)->send(new DynamicEmail($promoTemplate, [
            'user_name' => 'Test User',
            'promo_code' => 'WELCOME10',
            'discount_amount' => '10%',
        ]));
        echo "Promo Code Email sent successfully!\n";
    } else {
        echo "Promo Code Email template not found or inactive.\n";
    }

    echo "All test emails sent successfully!\n";

} catch (\Exception $e) {
    echo "Failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
