<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class FrontBusinessController extends Controller
{
    public function directory(): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('contractors.index');
    }

    public function profile(string $slug): View
    {
        return view('pages.business.profile', compact('slug'));
    }

    public function manage(\Illuminate\Http\Request $request)
    {
        if ($request->has('create') && $request->user()->hasBusiness()) {
            return redirect()->route('business.manage')->with('error', 'You already have an active business listing.');
        }

        $business = $request->user()->business;

        return view('pages.business.manage', [
            'status' => $business?->status,
        ]);
    }
}
