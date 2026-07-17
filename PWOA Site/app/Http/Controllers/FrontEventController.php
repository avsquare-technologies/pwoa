<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FrontEventController extends Controller
{
    /**
     * Display the list of events in the user dashboard.
     */
    public function index(Request $request): View
    {
        $query = Event::with('category')
            ->where('status', 'published');

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        $events = $query->orderBy('starts_at')->get();

        return view('pages.events.list', [
            'upcomingEvents' => $events->filter(fn($e) => $e->starts_at->isFuture())->values(),
            'pastEvents' => $events->filter(fn($e) => $e->starts_at->isPast())
                ->sortByDesc('starts_at')
                ->values(),
        ]);
    }

    /**
     * Display a specific event detail in the user dashboard.
     * Acts as a fallback/redirect handler to prevent 404s.
     */
    public function detail(string $event): RedirectResponse
    {
        $query = Event::where('status', 'published');

        if (is_numeric($event)) {
            $eventObj = $query->find($event);
        } else {
            $eventObj = $query->where('slug', $event)->first();
        }

        if ($eventObj) {
            return redirect()->route('events.show', $eventObj->slug);
        }

        return redirect()->route('events.index')
            ->with('error', 'Event not found or is no longer available.');
    }

    /**
     * Placeholder for event registration/purchase from dashboard.
     */
    public function purchase(string $slug): RedirectResponse
    {
        return redirect()->route('events.show', $slug)
            ->with('success', 'Ticket checkout can be connected next. The event page itself is working now.');
    }

    /**
     * Placeholder for viewing a ticket.
     */
    public function ticket(string $slug, string $ticketId): RedirectResponse
    {
        return redirect()->route('events.show', $slug)
            ->with('success', 'Ticket delivery placeholder complete.');
    }
}
