<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;

class ContractorController extends Controller
{
    public function index(Request $request)
    {
        return view('frontend.contractors.index');
    }

    public function show(string $slug)
    {
        $contractor = Business::where('type', 'contractor')
            ->where('slug', $slug)
            ->with([
                'city',
                'state',
                'contractorDetail',
                'contractorDetail.serviceRadius',
                'categories',
                'directoryCertifications',
                'directoryEquipments',
                'badges'
            ])
            ->firstOrFail();

        // Increment profile views
        $contractor->increment('views_count');

        return view('frontend.contractors.show', compact('contractor'));
    }

    public function edit(Request $request)
    {
        $business = $request->user()->businesses()->where('type', 'contractor')->first();
        if ($business) {
            return redirect()->route('business.manage', ['edit' => $business->id]);
        }

        if ($request->user()->hasBusiness()) {
            return redirect()->route('business.manage')->with('error', 'You already have an active business listing.');
        }

        return redirect()->route('business.manage', ['create' => 'contractor']);
    }
}

