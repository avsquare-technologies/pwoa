<?php

namespace App\Livewire;

use App\Actions\Membership\SubscribeUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Subscribe extends Component
{
    public $plan = 'standard';
    public $intent;

    public function mount()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isActiveMember()) {
            return redirect()->route('dashboard');
        }

        $this->plan = 'standard';

        // Ensure user has a stripe customer instance
        if (! $user->hasStripeId()) {
            $user->createAsStripeCustomer();
        }

        $this->intent = $user->createSetupIntent()->client_secret;
    }

    public function subscribe($paymentMethodId)
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            app(SubscribeUser::class)->execute($user, $paymentMethodId, $this->plan);

            return redirect()->route('membership.success');
        } catch (\Exception $e) {
            session()->flash('error', 'Payment failed: '.$e->getMessage());
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.subscribe');
    }
}
