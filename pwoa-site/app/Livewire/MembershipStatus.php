<?php

namespace App\Livewire;

use App\Actions\Membership\CancelMembership;
use App\Actions\Membership\ResumeMembership;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MembershipStatus extends Component
{
    public function cancel()
    {
        /** @var User $user */
        $user = Auth::user();
        app(CancelMembership::class)->execute($user);

        session()->flash('status', 'Membership cancelled successfully. You will have access until your current billing period ends.');

        return redirect()->route('dashboard');
    }

    public function resume()
    {
        /** @var User $user */
        $user = Auth::user();
        app(ResumeMembership::class)->execute($user);

        session()->flash('status', 'Membership resumed successfully.');

        return redirect()->route('dashboard');
    }

    public function render()
    {
        /** @var User $user */
        $user = Auth::user();

        return view('livewire.membership-status', [
            'membership' => $user->membershipStatus ?? new \App\Models\MembershipStatus,
        ]);
    }
}
