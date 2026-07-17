<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class MembershipController extends Controller
{

    public function index()
    {
        return view('frontend.membership.index', [
            'faqCategories' => $this->getFaqCategories(),
        ]);
    }

    public function gold()
    {
        return view('frontend.membership.index', [
            'faqCategories' => $this->getFaqCategories(),
        ]);
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan' => ['required', 'in:standard,gold'],
        ]);

        return redirect()->route('membership.subscribe_form');
    }

    public function success()
    {
        return redirect()->route('membership.success');
    }

    protected function getFaqCategories()
    {
        return FaqCategory::where('is_active', true)
            ->with(['faqs' => fn($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();
    }

    public function cancel()
    {
        return redirect()->route('membership.subscribe_form')->with('error', 'Membership checkout was cancelled.');
    }

    public function stripeWebhook()
    {
        return response()->json(['received' => true]);
    }
}
