<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        /** @var User $user */
        $user = Auth::user();

        $stats = [
            'upcoming_events' => Event::query()
                ->where('status', 'published')
                ->where('starts_at', '>', now())
                ->whereHas('attendees', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->count(),
            'total_courses' => Course::query()->where('is_published', true)->count(),
            'enrolled_courses' => $user->enrolledCourses()->count(),
            'certificates_earned' => $user->certificates()->count(),
            'complaints_filed' => $user->complaints()->count(),
            'tickets_owned' => \App\Models\EventTicket::where('user_id', $user->id)->count(),
            'business_status' => $user->business?->status ?? 'Not Registered',
        ];

        return view('livewire.dashboard', [
            'stats' => $stats,
            'wallet' => $user->wallet,
            'tokenTransactions' => $user->tokenTransactions()->latest()->take(5)->get(),
            'tickets' => \App\Models\EventTicket::where('user_id', $user->id)->with('event')->latest()->get(),
        ]);
    }

    public function createWallet(\App\Actions\Wallet\CreateWalletAction $action)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $wallet = $action->execute($user);

        if ($wallet) {
            session()->flash('success', 'Your XRPL wallet has been created successfully!');
        } else {
            session()->flash('error', 'Failed to create wallet. Please try again later.');
        }

        return redirect()->route('dashboard');
    }
}
