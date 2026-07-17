<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class MembershipController extends Controller
{
    public function status(): View
    {
        return view('pages.membership.status');
    }

    public function subscribe(): View
    {
        return view('pages.membership.subscribe');
    }

    public function success(): View
    {
        return view('pages.membership.success');
    }
}
