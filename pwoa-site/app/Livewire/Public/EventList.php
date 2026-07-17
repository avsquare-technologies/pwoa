<?php

namespace App\Livewire\Public;

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class EventList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $category_id = '';
    public $time_filter = 'upcoming'; // upcoming, past, today

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryId()
    {
        $this->resetPage();
    }

    public function updatingTimeFilter()
    {
        $this->resetPage();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $events = Event::query()
            ->where('status', 'published')
            ->whereHas('attendees', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('location', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->category_id, function ($query) {
                $query->where('event_category_id', $this->category_id);
            })
            ->when($this->time_filter === 'upcoming', function ($query) {
                $query->where('starts_at', '>=', now());
            })
            ->when($this->time_filter === 'past', function ($query) {
                $query->where('ends_at', '<', now());
            })
            ->when($this->time_filter === 'today', function ($query) {
                $query->whereDate('starts_at', now()->toDateString());
            })
            ->orderBy($this->time_filter === 'past' ? 'starts_at' : 'starts_at', $this->time_filter === 'past' ? 'desc' : 'asc')
            ->paginate(12);

        return view('livewire.public.event-list', [
            'events' => $events,
            'categories' => \App\Models\EventCategory::orderBy('name')->get(),
            'isSubscribed' => Auth::check() && Auth::user()->isActiveMember(),
        ]);
    }
}
