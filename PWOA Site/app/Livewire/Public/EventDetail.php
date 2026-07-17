<?php

namespace App\Livewire\Public;

use App\Models\Event;
use App\Models\EventAttendee;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class EventDetail extends Component
{
    public Event $event;

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function register()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        // Check if already registered
        if ($this->event->attendees()->where('user_id', Auth::id())->exists()) {
            session()->flash('error', 'You are already registered for this event.');

            return;
        }

        // Check capacity
        if ($this->event->capacity && $this->event->attendees()->count() >= $this->event->capacity) {
            session()->flash('error', 'This event is at full capacity.');

            return;
        }

        EventAttendee::create([
            'event_id' => $this->event->id,
            'user_id' => Auth::id(),
            'status' => 'registered',
        ]);

        session()->flash('status', 'Registration successful! See you there.');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.public.event-detail');
    }
}
