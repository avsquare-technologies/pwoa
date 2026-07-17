<?php

namespace App\Livewire\Public;

use App\Models\Business;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\BusinessCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class BusinessDirectory extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public $country_id = '';
    public $state_id = '';
    public $city_id = '';

    public $type = '';
    public $category = '';
    public $featured = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'country_id' => ['except' => ''],
        'state_id' => ['except' => ''],
        'city_id' => ['except' => ''],
        'type' => ['except' => ''],
        'category' => ['except' => ''],
        'featured' => ['except' => null],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedCountryId()
    {
        $this->state_id = '';
        $this->city_id = '';
        $this->resetPage();
    }

    public function updatedStateId()
    {
        $this->city_id = '';
        $this->resetPage();
    }

    public function updatedCityId()
    {
        $this->resetPage();
    }

    public function updatedType($value)
    {
        $this->category = '';
        $this->resetPage();
    }

    public function updatedFeatured()
    {
        $this->resetPage();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $businesses = Business::query()
            ->where('status', 'approved')
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->country_id, fn ($q) => $q->where('country_id', $this->country_id))
            ->when($this->state_id, fn ($q) => $q->where('state_id', $this->state_id))
            ->when($this->city_id, fn ($q) => $q->where('city_id', $this->city_id))
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->when($this->category, fn ($q) => $q->whereHas('categories', fn($c) => $c->where('business_categories.id', $this->category)))
            ->when($this->featured !== null && $this->featured !== '', fn ($q) => $q->where('featured', $this->featured))
            ->with(['categories', 'country', 'state', 'city'])
            ->orderByDesc('featured')
            ->latest()
            ->paginate(12);

        // Fetch available countries that have approved businesses
        $countries = Country::whereHas('businesses', function($q) {
            $q->where('status', 'approved');
        })->orderBy('name')->get();

        // Fetch available states for the selected country (or all if no country selected)
        $states = collect();
        if ($this->country_id) {
            $states = State::where('country_id', $this->country_id)
                ->whereHas('businesses', function($q) {
                    $q->where('status', 'approved');
                })->orderBy('name')->get();
        }

        // Fetch available cities for the selected state
        $cities = collect();
        if ($this->state_id) {
            $cities = City::where('state_id', $this->state_id)
                ->whereHas('businesses', function($q) {
                    $q->where('status', 'approved');
                })->orderBy('name')->get();
        }

        $categoriesQuery = BusinessCategory::orderBy('name');
        if ($this->type) {
            $categoriesQuery->where('type', $this->type);
        }
        $categories = $categoriesQuery->get();

        return view('livewire.public.business-directory', [
            'businesses' => $businesses,
            'countries' => $countries,
            'states' => $states,
            'cities' => $cities,
            'categories' => $categories,
            'isSubscribed' => Auth::check() && Auth::user()->isActiveMember(),
        ]);
    }
}
