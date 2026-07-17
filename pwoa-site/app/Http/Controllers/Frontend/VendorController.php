<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        return view('frontend.vendors.index');
    }

    public function show(string $slug)
    {
        $vendor = Business::where('type', 'vendor')
            ->where('slug', $slug)
            ->with([
                'city',
                'state',
                'vendorDetail',
                'categories',
                'badges'
            ])
            ->firstOrFail();

        // Increment profile views
        $vendor->increment('views_count');

        return view('frontend.vendors.show', compact('vendor'));
    }

    public function edit(Request $request)
    {
        $business = $request->user()->businesses()->where('type', 'vendor')->first();
        if ($business) {
            return redirect()->route('business.manage', ['edit' => $business->id]);
        }

        if ($request->user()->hasBusiness()) {
            return redirect()->route('business.manage')->with('error', 'You already have an active business listing.');
        }

        return redirect()->route('business.manage', ['create' => 'vendor']);
    }
}
