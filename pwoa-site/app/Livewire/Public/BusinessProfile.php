<?php

namespace App\Livewire\Public;

use App\Models\Business;
use Livewire\Attributes\Layout;
use Livewire\Component;

class BusinessProfile extends Component
{
    public Business $business;

    public function mount($slug)
    {
        $this->business = Business::where('slug', $slug)
            ->where('status', 'approved')
            ->with(['city', 'state', 'categories'])
            ->firstOrFail();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.public.business-profile');
    }
}
