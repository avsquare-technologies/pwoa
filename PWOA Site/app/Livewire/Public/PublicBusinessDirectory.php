<?php

namespace App\Livewire\Public;

use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\State;
use App\Models\City;
use App\Models\DirectoryCertification;
use App\Models\DirectoryEquipment;
use Livewire\Component;

class PublicBusinessDirectory extends Component
{
    public $type = 'contractor'; // 'contractor' or 'vendor'
    public $search = '';
    public $state_id = '';
    public $city_id = '';
    public $category_id = '';
    public $membership_tier = '';
    public $certified_only = false;
    
    // Contractor Filters
    public $certification_id = '';
    public $equipment_id = '';
    public $service_radius_id = '';
    public $is_emergency = false;
    public $is_subcontracting = false;
    public $is_national_accounts = false;
    public $verified_only = false;

    // Vendor Filters
    public $preferred_only = false;
    public $vendor_role = ''; // 'manufacturer' or 'distributor'
    public $has_online_ordering = false;
    public $has_local_pickup = false;
    public $has_member_discounts = false;
    
    public $perPage = 9;

    protected $queryString = [
        'search' => ['except' => ''],
        'state_id' => ['except' => ''],
        'city_id' => ['except' => ''],
        'category_id' => ['except' => ''],
        'membership_tier' => ['except' => ''],
        'certified_only' => ['except' => false],
        'certification_id' => ['except' => ''],
        'equipment_id' => ['except' => ''],
        'service_radius_id' => ['except' => ''],
        'is_emergency' => ['except' => false],
        'is_subcontracting' => ['except' => false],
        'is_national_accounts' => ['except' => false],
        'verified_only' => ['except' => false],
        'preferred_only' => ['except' => false],
        'vendor_role' => ['except' => ''],
        'has_online_ordering' => ['except' => false],
        'has_local_pickup' => ['except' => false],
        'has_member_discounts' => ['except' => false],
    ];

    public function mount()
    {
        $this->perPage = $this->type === 'vendor' ? 12 : 9;
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'state_id') {
            $this->city_id = '';
        }
        $this->perPage = $this->type === 'vendor' ? 12 : 9;
    }

    public function loadMore()
    {
        $this->perPage += ($this->type === 'vendor' ? 12 : 9);
    }

    public function resetFilters()
    {
        $this->reset([
            'search',
            'state_id',
            'city_id',
            'category_id',
            'membership_tier',
            'certified_only',
            'certification_id',
            'equipment_id',
            'service_radius_id',
            'is_emergency',
            'is_subcontracting',
            'is_national_accounts',
            'verified_only',
            'preferred_only',
            'vendor_role',
            'has_online_ordering',
            'has_local_pickup',
            'has_member_discounts',
        ]);
        $this->dispatch('filtersReset');
    }

    public function render()
    {
        $query = Business::query()
            ->where('type', $this->type)
            ->where('status', 'approved')
            ->with(['city', 'state', 'categories', 'directoryCertifications']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('tagline', 'like', '%' . $this->search . '%')
                  ->orWhere('zip', 'like', '%' . $this->search . '%')
                  ->orWhereHas('city', fn($sub) => $sub->where('name', 'like', '%' . $this->search . '%'))
                  ->orWhereHas('state', fn($sub) => $sub->where('name', 'like', '%' . $this->search . '%')
                                                         ->orWhere('iso2', 'like', '%' . $this->search . '%'))
                  ->orWhereHas('categories', fn($sub) => $sub->where('name', 'like', '%' . $this->search . '%'))
                  ->orWhereHas('directoryCertifications', fn($sub) => $sub->where('name', 'like', '%' . $this->search . '%'));
            });
        }

        if ($this->state_id) {
            $query->where('state_id', $this->state_id);
        }

        if ($this->city_id) {
            $query->where('city_id', $this->city_id);
        }

        if ($this->category_id) {
            $query->whereHas('categories', function($q) {
                $q->where('business_categories.id', $this->category_id)
                  ->orWhere('business_categories.parent_id', $this->category_id);
            });
        }

        if ($this->membership_tier) {
            $query->where('membership_tier', $this->membership_tier);
        }

        // Contractor specific filters
        if ($this->type === 'contractor') {
            if ($this->certified_only) {
                $query->whereHas('directoryCertifications', function($q) {
                    $q->where('directory_certifications.slug', 'pwoa-certified');
                });
            }

            if ($this->certification_id) {
                $query->whereHas('directoryCertifications', function($q) {
                    $q->where('directory_certifications.id', $this->certification_id);
                });
            }

            if ($this->equipment_id) {
                $query->whereHas('directoryEquipments', function($q) {
                    $q->where('directory_equipments.id', $this->equipment_id);
                });
            }

            if ($this->service_radius_id) {
                $query->whereHas('contractorDetail', function($q) {
                    $q->where('service_radius_id', $this->service_radius_id);
                });
            }

            if ($this->is_emergency) {
                $query->whereHas('contractorDetail', function($q) {
                    $q->where('is_emergency_service', true);
                });
            }

            if ($this->is_subcontracting) {
                $query->whereHas('contractorDetail', function($q) {
                    $q->where('is_subcontracting', true);
                });
            }

            if ($this->is_national_accounts) {
                $query->whereHas('contractorDetail', function($q) {
                    $q->where('is_national_accounts', true);
                });
            }

            if ($this->verified_only) {
                $query->where('is_verified', true);
            }
        }

        // Vendor specific filters
        if ($this->type === 'vendor') {
            if ($this->preferred_only) {
                $query->where('is_preferred', true);
            }

            if ($this->has_online_ordering) {
                $query->whereHas('vendorDetail', function($q) {
                    $q->where('has_online_ordering', true);
                });
            }

            if ($this->has_local_pickup) {
                $query->whereHas('vendorDetail', function($q) {
                    $q->where('has_local_pickup', true);
                });
            }

            if ($this->has_member_discounts) {
                $query->whereHas('vendorDetail', function($q) {
                    $q->where('has_member_discounts', true);
                });
            }

            if ($this->vendor_role === 'manufacturer') {
                $query->whereHas('categories', function($q) {
                    $q->where(function($sub) {
                        $sub->where('business_categories.slug', 'manufacturers')
                            ->orWhereHas('parent', fn($p) => $p->where('slug', 'manufacturers'));
                    });
                });
            } elseif ($this->vendor_role === 'distributor') {
                $query->whereDoesntHave('categories', function($q) {
                    $q->where(function($sub) {
                        $sub->where('business_categories.slug', 'manufacturers')
                            ->orWhereHas('parent', fn($p) => $p->where('slug', 'manufacturers'));
                    });
                });
            }
        }

        $totalCount = $query->count();
        
        // Priority placement: Gold Members first, then Featured, then latest
        $businesses = $query->orderByRaw("CASE WHEN membership_tier = 'gold' THEN 1 ELSE 0 END DESC")
            ->orderByDesc('featured')
            ->latest()
            ->take($this->perPage)
            ->get();

        $states = State::whereHas('businesses', function($q) {
            $q->where('type', $this->type)->where('status', 'approved');
        })->orderBy('name')->get();

        $cities = [];
        if ($this->state_id) {
            $cities = City::where('state_id', $this->state_id)
                ->whereHas('businesses', function($q) {
                    $q->where('type', $this->type)->where('status', 'approved');
                })
                ->orderBy('name')
                ->get();
        }

        $categories = BusinessCategory::where('type', $this->type)->orderBy('name')->get();
        $certifications = DirectoryCertification::orderBy('name')->get();
        $equipments = DirectoryEquipment::orderBy('name')->get();
        $serviceRadii = \App\Models\ServiceRadius::orderBy('value')->get();

        return view('livewire.public.public-business-directory', [
            'businesses' => $businesses,
            'states' => $states,
            'cities' => $cities,
            'categories' => $categories,
            'certifications' => $certifications,
            'equipments' => $equipments,
            'serviceRadii' => $serviceRadii,
            'hasMore' => $totalCount > $this->perPage,
        ]);
    }
}

