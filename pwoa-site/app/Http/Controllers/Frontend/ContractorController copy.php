<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Support\DemoCatalog;
use Illuminate\Http\Request;


class ContractorController extends Controller
{
    
    public function index(Request $request)
    {
        $contractors = DemoCatalog::contractors()
            ->filter(function ($contractor) use ($request): bool {
                $search = strtolower((string) $request->string('search'));

                if ($search !== '') {
                    $haystack = strtolower(implode(' ', [
                        $contractor->business_name,
                        $contractor->city,
                        $contractor->state,
                        implode(' ', $contractor->service_types),
                    ]));

                    if (!str_contains($haystack, $search)) {
                        return false;
                    }
                }

                if ($request->filled('state') && $contractor->state !== $request->string('state')->toString()) {
                    return false;
                }

                if ($request->filled('type') && !in_array($request->string('type')->toString(), $contractor->service_types, true)) {
                    return false;
                }

                if ($request->filled('tier') && $contractor->membership_tier !== $request->string('tier')->toString()) {
                    return false;
                }

                if ($request->boolean('certified') && !$contractor->is_pwoa_certified) {
                    return false;
                }

                return true;
            });

        $contractors = match ($request->string('sort')->toString()) {
            'name' => $contractors->sortBy('business_name', SORT_NATURAL | SORT_FLAG_CASE),
            'newest' => $contractors->sortByDesc('id'),
            'rating' => $contractors->sortByDesc('avg_rating'),
            default => $contractors->sortByDesc(fn($item) => ($item->is_featured ? 100 : 0) + ($item->membership_tier === 'gold' ? 10 : 0) + ($item->is_pwoa_certified ? 1 : 0)),
        };

        return view('frontend.contractors.index', [
            'contractors' => DemoCatalog::paginate($contractors->values(), 9),
            'states' => DemoCatalog::contractors()->pluck('state')->filter()->unique()->sort()->values(),
        ]);
    }

    public function show(string $slug)
    {
        return view('frontend.contractors.show', [
            'contractor' => DemoCatalog::findBySlug(DemoCatalog::contractors(), $slug),
        ]);
    }

    public function edit()
    {
        return redirect()->route('contractors.index')->with('success', 'Member listing editing can be wired next once account auth is added.');
    }

    public function update(Request $request)
    {
        return redirect()->route('contractors.index')->with('success', 'Listing update flow is ready for backend wiring when you connect real data.');
    }
}
