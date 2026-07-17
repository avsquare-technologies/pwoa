<?php

namespace App\Providers;

use App\Services\SystemWalletManager;
use App\Services\XrplConnection;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 1. Register the Base Connection (Singleton)
        $this->app->singleton(XrplConnection::class, function ($app) {
            return new XrplConnection();
        });

        // 2. Register the Manager (Injects Connection)
        $this->app->singleton(SystemWalletManager::class, function ($app) {
            return new SystemWalletManager(
                $app->make(XrplConnection::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
