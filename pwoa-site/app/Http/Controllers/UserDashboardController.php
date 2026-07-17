<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();
        if ($user && ($user->hasRole('super_admin') || $user->hasRole('admin'))) {
            return redirect('/admin');
        }

        return view('pages.dashboard');
    }
}
