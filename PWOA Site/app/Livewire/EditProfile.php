<?php

namespace App\Livewire;

use App\Actions\Auth\UpdateProfile;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class EditProfile extends Component
{
    public string $name = '';
    public string $phone = '';
    
    public $country_id;
    public $state_id;
    public $city_id;
    
    public $countries = [];
    public $states = [];
    public $cities = [];

    public string $address = '';
    public string $zip = '';
    public ?string $date_of_birth = null;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;

        $detail = $user->detail;
        if ($detail) {
            $this->phone = $detail->phone ?? '';
            $this->country_id = $detail->country_id;
            $this->state_id = $detail->state_id;
            $this->city_id = $detail->city_id;
            $this->address = $detail->address ?? '';
            $this->zip = $detail->zip ?? '';
            $this->date_of_birth = $detail->date_of_birth?->format('Y-m-d');
        }

        $this->countries = Country::orderBy('name')->get();
        
        if ($this->country_id) {
            $this->states = State::where('country_id', $this->country_id)->orderBy('name')->get(['id', 'name'])->toArray();
        }
        
        if ($this->state_id) {
            $this->cities = City::where('state_id', $this->state_id)->orderBy('name')->get(['id', 'name'])->toArray();
        }
    }

    public function updatedCountryId($value)
    {
        $this->state_id = null;
        $this->city_id = null;
        $this->states = $value ? State::where('country_id', $value)->orderBy('name')->get(['id', 'name'])->toArray() : [];
        $this->cities = [];
    }

    public function updatedStateId($value)
    {
        $this->city_id = null;
        $this->cities = $value ? City::where('state_id', $value)->orderBy('name')->get(['id', 'name'])->toArray() : [];
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'address' => 'nullable|string|max:500',
            'zip' => 'nullable|string|max:10',
            'date_of_birth' => 'nullable|date|before:today',
        ]);

        app(UpdateProfile::class)->execute(Auth::user(), [
            'name' => $this->name,
            'phone' => $this->phone,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'address' => $this->address,
            'zip' => $this->zip,
            'date_of_birth' => $this->date_of_birth,
        ]);

        session()->flash('status', 'Profile updated successfully.');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.edit-profile');
    }
}
