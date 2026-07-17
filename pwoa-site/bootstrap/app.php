<?php

use App\Http\Middleware\EnsureMembershipActive;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        \App\Console\Commands\CleanupLegacyTickets::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'active' => EnsureUserIsActive::class,
            'membership.active' => EnsureMembershipActive::class,
            'wash.balance' => \App\Http\Middleware\RequireWashBalance::class,
            'has_no_business' => \App\Http\Middleware\RedirectIfHasBusiness::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 1. Redirect to login on session/CSRF token mismatch
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->is('logout') || $request->is('admin/logout') || $request->is('*/logout')) {
                \Illuminate\Support\Facades\Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('status', 'You have been logged out.');
            }
            return redirect()->route('login')->with('error', 'Your session expired. Please log in again.');
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->is('logout') || $request->is('admin/logout') || $request->is('*/logout')) {
                    \Illuminate\Support\Facades\Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('login')->with('status', 'You have been logged out.');
                }
                return redirect()->route('login')->with('error', 'Your session expired. Please log in again.');
            }
            return null;
        });
    })->create();
