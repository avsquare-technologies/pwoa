<?php

namespace App\Providers;

use App\Events\MembershipActivated;
use App\Events\MembershipCancelled;
use App\Events\PaymentReceived;
use App\Events\UserRegistered;
use App\Listeners\LogAuditListener;
use App\Listeners\SendMembershipActivatedNotification;
use App\Listeners\SendMembershipCancelledNotification;
use App\Listeners\SendPaymentSucceededNotification;
use App\Listeners\SendWelcomeNotification;
use App\Listeners\StripeWebhookListener;
use App\Listeners\UpdateLastLoginAt;
use Illuminate\Auth\Events\Login;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Events\WebhookReceived;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Css;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Event::listen(
            WebhookReceived::class,
            StripeWebhookListener::class
        );

        // Custom Platform Events
        // Event::listen(
        //     UserRegistered::class,
        //     [SendWelcomeNotification::class, 'handle']
        // );
        Event::listen(
            UserRegistered::class,
            [LogAuditListener::class, 'handle']
        );

        Event::listen(
            MembershipActivated::class,
            [SendMembershipActivatedNotification::class, 'handle']
        );
        Event::listen(
            MembershipActivated::class,
            [LogAuditListener::class, 'handle']
        );

        Event::listen(
            MembershipCancelled::class,
            [SendMembershipCancelledNotification::class, 'handle']
        );
        Event::listen(
            MembershipCancelled::class,
            [LogAuditListener::class, 'handle']
        );

        Event::listen(
            PaymentReceived::class,
            [SendPaymentSucceededNotification::class, 'handle']
        );
        Event::listen(
            PaymentReceived::class,
            [LogAuditListener::class, 'handle']
        );

        Event::listen(
            Login::class,
            UpdateLastLoginAt::class
        );

        // Register custom style for Filament Admin Panel
        FilamentAsset::register([
            Css::make('admin-custom-styles', asset('css/admin-custom.css')),
        ]);
    }
}
