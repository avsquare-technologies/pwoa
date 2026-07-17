<?php

namespace App\Livewire;

use App\Actions\Membership\UpgradeMembership;
use App\Actions\Membership\DowngradeMembership;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class MembershipUpgrade extends Component
{
    public $currentTier;
    
    public function mount()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isActiveMember()) {
            return redirect()->route('membership.subscribe_form');
        }

        $this->currentTier = $user->membershipStatus?->plan ?? 'standard';
    }

    public function upgrade()
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            app(UpgradeMembership::class)->execute($user);
            session()->flash('status', 'Successfully upgraded to Gold Membership!');
            return redirect()->route('membership.status');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function downgrade()
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            app(DowngradeMembership::class)->execute($user);
            session()->flash('status', 'Successfully downgraded to Standard Membership.');
            return redirect()->route('membership.status');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.membership-upgrade');
    }
}
