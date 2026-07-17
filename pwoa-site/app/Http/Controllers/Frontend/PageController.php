<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Support\DemoCatalog;
use Illuminate\Http\Request;
use App\Models\Event;

class PageController extends Controller
{
    public function home()
    {
        $upcomingEvents = Event::where('status', 'published')
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at', 'asc')
            ->take(3)
            ->get();
        $featuredEvents = Event::where('status', 'published')
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->take(6) // featured section
            ->get();



        return view('frontend.home', [
            'upcomingEvents' => $upcomingEvents,
            'featuredEvents' => $featuredEvents,
        ]);
    }

    public function about()
    {
        return view('frontend.about');
    }


    public function compliance()
    {
        return view('frontend.compliance.index');
    }

    public function tokenomics()
    {
        return view('frontend.tokenomics.index');
    }

    public function privacyPolicy()
    {
        return view('frontend.privacy-policy');
    }

    public function termsAndConditions()
    {
        return view('frontend.terms-and-conditions');
    }
}
