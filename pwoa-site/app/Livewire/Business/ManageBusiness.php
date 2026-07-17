<?php

namespace App\Livewire\Business;

use App\Models\Business;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use App\Models\ServiceRadius;
use App\Models\DirectoryCertification;
use App\Models\DirectoryEquipment;
use App\Models\BusinessCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\ProfileCompletionService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class ManageBusiness extends Component
{
    use WithFileUploads;

    // Wizard navigation
    public $currentStep = 1;
    public $totalSteps = 7;
    public $showForm = false;
    public $isEdit = false;
    public $businessId;
    public $status;
    public $completionData = [];

    // Step 1: Model Type
    public $type = 'contractor';

    // Step 2: Company details
    public $name;
    public $slug;
    public $email;
    public $phone;
    public $website;
    public $address;
    public $country_id;
    public $state_id;
    public $city_id;
    public $zip;
    public $years_in_business;
    public $license_number;
    public $is_insured = false;
    public $license_file;
    public $insurance_file;
    public $licensePath;
    public $insurancePath;
    public $license_status;
    public $insurance_status;

    // Step 3: Visual Identity
    public $logo;
    public $banner;
    public $logoPath;
    public $bannerPath;

    // Step 4: Profile Info
    public $tagline;
    public $short_description;
    public $description;

    // Step 5: Service Radius / Vendor Features
    public $service_radius_id;
    public $is_emergency_service = false;
    public $is_subcontracting = false;
    public $is_national_accounts = false;

    public $has_online_ordering = false;
    public $has_local_pickup = false;
    public $has_member_discounts = false;
    public $wants_preferred_program = false;
    public $wants_partnership = false;

    // Step 6: Categories & Certifications & Equipment Fleet
    public $selected_categories = [];
    public $selected_certifications = [];
    public $selected_equipments = [];
    public $equipment_quantities = [];
    public $equipment_specs = [];
    public $certification_files = [];
    public $certificationPaths = [];

    // Step 7: Socials
    public $facebook;
    public $instagram;
    public $linkedin;
    public $youtube;
    public $tiktok;

    // List datasets
    public $myBusinesses = [];
    public $countries = [];
    public $states = [];
    public $cities = [];
    
    public $allCategories = [];
    public $allCertifications = [];
    public $allEquipments = [];
    public $allServiceRadii = [];

    public function mount()
    {
        $this->ensureLookupsExist();
        $this->loadDropdowns();
        $this->loadMyBusinesses();

        if (Auth::user()->hasBusiness() && request()->query('create')) {
            return redirect()->route('business.manage')->with('error', 'You already have an active business listing.');
        }

        $editId = request()->query('edit');
        if ($editId) {
            $business = Auth::user()->businesses()->find($editId);
            if ($business) {
                $this->editListing($business->id);
            }
        } elseif (request()->query('create')) {
            $createType = request()->query('create');
            if (in_array($createType, ['contractor', 'vendor'])) {
                $this->type = $createType;
                $this->createListing();
            }
        }
    }

    public function loadMyBusinesses()
    {
        /** @var User $user */
        $user = Auth::user();
        $this->myBusinesses = $user->businesses()->with(['city', 'state', 'categories'])->get();

        $business = $this->myBusinesses->first();
        if ($business) {
            $completionService = app(ProfileCompletionService::class);
            $this->completionData = $completionService->getCompletionData($business);
        } else {
            $this->completionData = [];
        }
    }

    public function loadDropdowns()
    {
        $this->countries = Country::orderBy('name')->get();
        $this->allCategories = BusinessCategory::where('type', $this->type)->orderBy('name')->get();
        $this->allCertifications = DirectoryCertification::orderBy('name')->get();
        $this->allEquipments = DirectoryEquipment::orderBy('name')->get();
        $this->allServiceRadii = ServiceRadius::orderBy('value')->get();
    }

    public function updatedCountryId($value)
    {
        $this->state_id = null;
        $this->city_id = null;
        $this->states = $value ? State::where('country_id', $value)->orderBy('name')->get()->toArray() : [];
        $this->cities = [];
    }

    public function updatedStateId($value)
    {
        $this->city_id = null;
        $this->cities = $value ? City::where('state_id', $value)->orderBy('name')->get()->toArray() : [];
    }

    public function updatedName($value)
    {
        if (!$this->isEdit) {
            $this->slug = Str::slug($value);
        }
    }

    public function updatedType($value)
    {
        $this->selected_categories = [];
        $this->allCategories = BusinessCategory::where('type', $value)->orderBy('name')->get();
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetForm();
            $this->loadMyBusinesses();
        }
    }

    public function createListing()
    {
        if (Auth::user()->hasBusiness()) {
            session()->flash('error', 'You already have an active business listing.');
            return;
        }
        $this->resetForm();
        $this->isEdit = false;
        $this->totalSteps = 7;
        $this->currentStep = 1;
        $this->showForm = true;
    }

    public function editListing($id)
    {
        $this->resetForm();
        $this->isEdit = true;
        $this->businessId = $id;

        $business = Business::with([
            'contractorDetail',
            'vendorDetail',
            'categories',
            'directoryCertifications',
            'directoryEquipments'
        ])->findOrFail($id);

        $this->totalSteps = $business->type === 'vendor' ? 5 : 6;
        $this->currentStep = 1;

        $this->status = $business->status;
        $this->type = $business->type;
        $this->logoPath = $business->logo_path;
        $this->bannerPath = $business->cover_photo_path;

        $this->fill($business->only([
            'name', 'slug', 'type', 'tagline', 'short_description', 'description',
            'email', 'phone', 'website', 'address', 'country_id', 'state_id', 'city_id', 'zip',
            'facebook', 'instagram', 'linkedin', 'youtube', 'tiktok'
        ]));

        if ($this->country_id) {
            $this->states = State::where('country_id', $this->country_id)->orderBy('name')->get()->toArray();
        }
        if ($this->state_id) {
            $this->cities = City::where('state_id', $this->state_id)->orderBy('name')->get()->toArray();
        }

        // Child Details
        if ($business->type === 'contractor' && $business->contractorDetail) {
            $detail = $business->contractorDetail;
            $this->years_in_business = $detail->years_in_business;
            $this->license_number = $detail->license_number;
            $this->is_insured = (bool)$detail->is_insured;
            $this->licensePath = $detail->license_path;
            $this->insurancePath = $detail->insurance_path;
            $this->license_status = $detail->license_status;
            $this->insurance_status = $detail->insurance_status;
            $this->service_radius_id = $detail->service_radius_id;
            $this->is_emergency_service = (bool)$detail->is_emergency_service;
            $this->is_subcontracting = (bool)$detail->is_subcontracting;
            $this->is_national_accounts = (bool)$detail->is_national_accounts;
        } elseif ($business->type === 'vendor' && $business->vendorDetail) {
            $detail = $business->vendorDetail;
            $this->years_in_business = $detail->years_in_business;
            $this->has_online_ordering = (bool)$detail->has_online_ordering;
            $this->has_local_pickup = (bool)$detail->has_local_pickup;
            $this->has_member_discounts = (bool)$detail->has_member_discounts;
            $this->wants_preferred_program = (bool)$detail->wants_preferred_program;
            $this->wants_partnership = (bool)$detail->wants_partnership;
        }

        // M2M Relations
        $this->selected_categories = $business->categories->pluck('id')->toArray();
        $this->selected_certifications = $business->directoryCertifications->pluck('id')->toArray();
        $this->certificationPaths = [];
        foreach ($business->directoryCertifications as $cert) {
            $this->certificationPaths[$cert->id] = $cert->pivot->document_path;
        }
        $this->selected_equipments = $business->directoryEquipments->pluck('id')->toArray();

        foreach ($business->directoryEquipments as $equip) {
            $this->equipment_quantities[$equip->id] = $equip->pivot->quantity;
            $this->equipment_specs[$equip->id] = $equip->pivot->specifications;
        }

        $this->allCategories = BusinessCategory::where('type', $this->type)->orderBy('name')->get();
        $this->showForm = true;
    }

    public function continueProfile($id)
    {
        $business = Business::findOrFail($id);
        if ($business->user_id !== Auth::id()) {
            abort(403);
        }

        $completionService = app(ProfileCompletionService::class);
        $completion = $completionService->getCompletionData($business);

        $this->editListing($business->id);
        $this->currentStep = $completion['next_incomplete_edit_step'];
    }

    public function deleteListing($id)
    {
        $business = Business::findOrFail($id);
        if ($business->user_id === Auth::id()) {
            $business->delete();
            session()->flash('status', 'Listing deleted successfully.');
            $this->loadMyBusinesses();
        }
    }

    public function resetForm()
    {
        $this->reset([
            'currentStep', 'businessId', 'status', 'type', 'name', 'slug', 'email', 'phone', 'website',
            'address', 'country_id', 'state_id', 'city_id', 'zip', 'years_in_business', 'license_number', 'is_insured',
            'license_file', 'insurance_file', 'licensePath', 'insurancePath', 'license_status', 'insurance_status',
            'logo', 'banner', 'logoPath', 'bannerPath', 'tagline', 'short_description', 'description',
            'service_radius_id', 'is_emergency_service', 'is_subcontracting', 'is_national_accounts',
            'has_online_ordering', 'has_local_pickup', 'has_member_discounts', 'wants_preferred_program', 'wants_partnership',
            'selected_categories', 'selected_certifications', 'selected_equipments', 'equipment_quantities', 'equipment_specs',
            'certification_files', 'certificationPaths',
            'facebook', 'instagram', 'linkedin', 'youtube', 'tiktok'
        ]);
        $this->loadDropdowns();
    }

    public function nextStep()
    {
        $this->validateStep();
        $this->currentStep++;
    }

    public function prevStep()
    {
        if ($this->currentStep <= 1) {
            return;
        }
        $this->currentStep--;
    }

    protected function validateStep()
    {
        if ($this->isEdit) {
            $mediaStep = $this->type === 'vendor' ? 4 : 5;
            $reviewStep = $this->type === 'vendor' ? 5 : 6;

            if ($this->currentStep === 1) {
                // Edit Step 1: Company Details
                $this->validate([
                    'name' => 'required|min:3|max:255',
                    'slug' => 'required|alpha_dash|unique:businesses,slug,' . ($this->businessId ?? 'NULL'),
                    'email' => 'required|email',
                    'phone' => ['nullable', 'string', 'regex:/^[0-9\-\+\(\)\s]{10,20}$/'],
                    'website' => 'nullable|url',
                    'address' => 'required|string',
                    'country_id' => 'required|exists:countries,id',
                    'state_id' => 'required|exists:states,id',
                    'city_id' => 'required|exists:cities,id',
                    'zip' => ['required', 'string', 'regex:/^[a-zA-Z0-9\s\-]{3,10}$/'],
                    'years_in_business' => 'nullable|integer|min:0|max:100',
                    'license_number' => 'nullable|string|max:100',
                    'is_insured' => 'nullable|boolean',
                    'license_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                    'insurance_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                ], [
                    'phone.regex' => 'The phone number must be a valid 10 to 20-digit number.',
                    'zip.regex' => 'The zip/postal code must be between 3 and 10 alphanumeric characters.',
                ]);
            } elseif ($this->currentStep === 2) {
                // Edit Step 2: Profile Information
                $this->validate([
                    'tagline' => 'nullable|string|max:150',
                    'short_description' => 'nullable|string|max:250',
                    'description' => 'required|min:10',
                ]);
            } elseif ($this->currentStep === 3) {
                // Edit Step 3: Categories
                // No validation needed
            } elseif ($this->currentStep === 4) {
                // Edit Step 4: Certifications / Vendor Media
                if ($this->type === 'contractor') {
                    $this->validate([
                        'certification_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                    ]);
                } else {
                    $this->validate([
                        'logo' => 'nullable|image|max:1024',
                        'banner' => 'nullable|image|max:2048',
                    ]);
                }
            } elseif ($this->currentStep === $mediaStep) {
                // Edit Step 5 (Contractor): Media
                $this->validate([
                    'logo' => 'nullable|image|max:1024',
                    'banner' => 'nullable|image|max:2048',
                ]);
            } elseif ($this->currentStep === $reviewStep) {
                // Edit Step 5 (Vendor) / 6 (Contractor): Review
                if ($this->type === 'contractor') {
                    $this->validate([
                        'service_radius_id' => 'nullable|exists:service_radii,id',
                    ]);
                }
            }
        } else {
            // Create Flow
            if ($this->currentStep === 1) {
                $this->validate([
                    'type' => 'required|in:contractor,vendor',
                ]);
            } elseif ($this->currentStep === 2) {
                $this->validate([
                    'name' => 'required|min:3|max:255',
                    'slug' => 'required|alpha_dash|unique:businesses,slug,' . ($this->businessId ?? 'NULL'),
                    'email' => 'required|email',
                    'phone' => ['nullable', 'string', 'regex:/^[0-9\-\+\(\)\s]{10,20}$/'],
                    'website' => 'nullable|url',
                    'address' => 'required|string',
                    'country_id' => 'required|exists:countries,id',
                    'state_id' => 'required|exists:states,id',
                    'city_id' => 'required|exists:cities,id',
                    'zip' => ['required', 'string', 'regex:/^[a-zA-Z0-9\s\-]{3,10}$/'],
                    'years_in_business' => 'nullable|integer|min:0|max:100',
                    'license_number' => 'nullable|string|max:100',
                    'is_insured' => 'nullable|boolean',
                    'license_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                    'insurance_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                ], [
                    'phone.regex' => 'The phone number must be a valid 10 to 20-digit number.',
                    'zip.regex' => 'The zip/postal code must be between 3 and 10 alphanumeric characters.',
                ]);
            } elseif ($this->currentStep === 3) {
                $this->validate([
                    'logo' => 'nullable|image|max:1024',
                    'banner' => 'nullable|image|max:2048',
                ]);
            } elseif ($this->currentStep === 4) {
                $this->validate([
                    'tagline' => 'nullable|string|max:150',
                    'short_description' => 'nullable|string|max:250',
                    'description' => 'required|min:10',
                ]);
            } elseif ($this->currentStep === 5) {
                if ($this->type === 'contractor') {
                    $this->validate([
                        'service_radius_id' => 'nullable|exists:service_radii,id',
                    ]);
                }
            } elseif ($this->currentStep === 6) {
                if ($this->type === 'contractor') {
                    $this->validate([
                        'certification_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                    ]);
                }
            }
        }
    }

    public function save()
    {
        $this->validateStep(); // Validate final review step

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'tagline' => $this->tagline,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'address' => $this->address,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'zip' => $this->zip,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'linkedin' => $this->linkedin,
            'youtube' => $this->youtube,
            'tiktok' => $this->tiktok,
        ];

        if ($this->logo) {
            $data['logo_path'] = $this->logo->store('logos', 'public');
            $this->logoPath = $data['logo_path'];
        }

        if ($this->banner) {
            $data['cover_photo_path'] = $this->banner->store('banners', 'public');
            $this->bannerPath = $data['cover_photo_path'];
        }

        if ($this->isEdit) {
            $business = Business::findOrFail($this->businessId);
            if ($business->user_id !== Auth::id()) {
                abort(403);
            }
            unset($data['type']); // Directory type cannot be changed after creation
            $business->update($data);
            $message = 'Listing updated successfully!';
        } else {
            /** @var User $user */
            $user = Auth::user();
            if ($user->hasBusiness()) {
                session()->flash('error', 'You already have an active business listing.');
                $this->showForm = false;
                return;
            }
            $business = $user->businesses()->create($data);
            $message = 'Listing profile registered successfully and submitted for review!';
            $this->businessId = $business->id;
        }

        // Save Contractor/Vendor details
        if ($this->type === 'contractor') {
            $licensePath = $business->contractorDetail?->license_path;
            $licenseStatus = $business->contractorDetail?->license_status ?: 'pending';
            if ($this->license_file) {
                $licensePath = $this->license_file->store('business-licenses', 'public');
                $licenseStatus = 'pending';
            }

            $insurancePath = $business->contractorDetail?->insurance_path;
            $insuranceStatus = $business->contractorDetail?->insurance_status ?: 'pending';
            if ($this->insurance_file) {
                $insurancePath = $this->insurance_file->store('business-insurance', 'public');
                $insuranceStatus = 'pending';
            }

            $business->contractorDetail()->updateOrCreate(
                ['business_id' => $business->id],
                [
                    'years_in_business' => $this->years_in_business ?: null,
                    'license_number' => $this->license_number ?: null,
                    'license_path' => $licensePath,
                    'license_status' => $licenseStatus,
                    'is_insured' => $this->is_insured ? true : false,
                    'insurance_path' => $insurancePath,
                    'insurance_status' => $insuranceStatus,
                    'service_radius_id' => $this->service_radius_id ?: null,
                    'is_emergency_service' => $this->is_emergency_service ? true : false,
                    'is_subcontracting' => $this->is_subcontracting ? true : false,
                    'is_national_accounts' => $this->is_national_accounts ? true : false,
                ]
            );
        } else {
            $business->vendorDetail()->updateOrCreate(
                ['business_id' => $business->id],
                [
                    'years_in_business' => $this->years_in_business ?: null,
                    'has_online_ordering' => $this->has_online_ordering ? true : false,
                    'has_local_pickup' => $this->has_local_pickup ? true : false,
                    'has_member_discounts' => $this->has_member_discounts ? true : false,
                    'wants_preferred_program' => $this->wants_preferred_program ? true : false,
                    'wants_partnership' => $this->wants_partnership ? true : false,
                ]
            );
        }

        // Sync many-to-many categories
        $business->categories()->sync($this->selected_categories);

        // Sync certifications & equipment for contractors
        if ($this->type === 'contractor') {
            $certSyncData = [];
            foreach ($this->selected_certifications as $certId) {
                $docPath = $this->certificationPaths[$certId] ?? null;
                $status = 'pending';

                // Check existing status
                $existingPivot = $business->directoryCertifications()->where('directory_certification_id', $certId)->first()?->pivot;
                if ($existingPivot) {
                    $status = $existingPivot->status;
                }

                if (isset($this->certification_files[$certId]) && $this->certification_files[$certId]) {
                    $docPath = $this->certification_files[$certId]->store('business-certifications', 'public');
                    $status = 'pending'; // Reset to pending if a new file is uploaded
                }

                $certSyncData[$certId] = [
                    'document_path' => $docPath,
                    'status' => $status,
                ];
            }
            $business->directoryCertifications()->sync($certSyncData);

            $equipSyncData = [];
            foreach ($this->selected_equipments as $equipId) {
                $equipSyncData[$equipId] = [
                    'quantity' => $this->equipment_quantities[$equipId] ?? 1,
                    'specifications' => $this->equipment_specs[$equipId] ?? null,
                ];
            }
            $business->directoryEquipments()->sync($equipSyncData);
        }

        session()->flash('status', $message);
        $this->showForm = false;
        $this->isEdit = false;
        $this->resetForm();
        $this->loadMyBusinesses();
    }

    protected function ensureLookupsExist()
    {
        // 1. Service Radii
        if (ServiceRadius::count() === 0) {
            $radii = [
                ['name' => '25 Miles', 'value' => 25, 'slug' => '25_miles', 'description' => 'Within 25 miles of headquarters'],
                ['name' => '50 Miles', 'value' => 50, 'slug' => '50_miles', 'description' => 'Within 50 miles of headquarters'],
                ['name' => '100 Miles', 'value' => 100, 'slug' => '100_miles', 'description' => 'Within 100 miles of headquarters'],
                ['name' => 'Statewide', 'value' => null, 'slug' => 'statewide', 'description' => 'Statewide coverage'],
            ];
            foreach ($radii as $r) {
                ServiceRadius::create($r);
            }
        }

        // 2. Certifications
        if (DirectoryCertification::count() === 0) {
            $certs = [
                ['name' => 'PWOA Certified', 'slug' => 'pwoa-certified', 'description' => 'Power Washing Of America Certified'],
                ['name' => 'ECO Certified', 'slug' => 'eco-certified', 'description' => 'Environmental compliance certified'],
                ['name' => 'Roof Cleaning Certified', 'slug' => 'roof-cleaning-certified', 'description' => 'Specialist in low pressure roof cleaning'],
                ['name' => 'Water Recovery Certified', 'slug' => 'water-recovery-certified', 'description' => 'Certified in water reclamation and recycling'],
            ];
            foreach ($certs as $c) {
                DirectoryCertification::create($c);
            }
        }

        // 3. Equipment
        if (DirectoryEquipment::count() === 0) {
            $equips = [
                ['name' => 'Hot Water Unit', 'slug' => 'hot-water-unit', 'description' => 'Hot water pressure washer unit'],
                ['name' => 'Soft Wash System', 'slug' => 'soft-wash-system', 'description' => 'Low-pressure chemical soft washing skid'],
                ['name' => 'Water Recovery System', 'slug' => 'water-recovery-system', 'description' => 'Water recovery and filtration unit'],
                ['name' => 'Surface Cleaner', 'slug' => 'surface-cleaner', 'description' => 'Flat surface cleaning machine'],
                ['name' => 'Lift Certified', 'slug' => 'lift-certified', 'description' => 'Certified to operate boom and scissor lifts'],
                ['name' => 'CDL Driver', 'slug' => 'cdl-driver', 'description' => 'Commercial Driver License holder on staff'],
                ['name' => 'Vacuum Recovery System', 'slug' => 'vacuum-recovery-system', 'description' => 'High-powered vacuum recovery system'],
            ];
            foreach ($equips as $e) {
                DirectoryEquipment::create($e);
            }
        }

        // 4. Seeding Contractor & Vendor Categories dynamically if empty
        if (BusinessCategory::count() === 0) {
            $cats = [
                // Contractors
                ['name' => 'House Washing', 'slug' => 'house-washing', 'type' => 'contractor', 'category_type' => 'child'],
                ['name' => 'Roof Cleaning', 'slug' => 'roof-cleaning', 'type' => 'contractor', 'category_type' => 'child'],
                ['name' => 'Driveway Cleaning', 'slug' => 'driveway-cleaning', 'type' => 'contractor', 'category_type' => 'child'],
                ['name' => 'Patio Cleaning', 'slug' => 'patio-cleaning', 'type' => 'contractor', 'category_type' => 'child'],
                ['name' => 'Building Washing', 'slug' => 'building-washing', 'type' => 'contractor', 'category_type' => 'child'],
                ['name' => 'Fleet Washing', 'slug' => 'fleet-washing', 'type' => 'contractor', 'category_type' => 'child'],
                ['name' => 'Soft Washing', 'slug' => 'soft-washing', 'type' => 'contractor', 'category_type' => 'child'],
                ['name' => 'Pressure Washing', 'slug' => 'pressure-washing', 'type' => 'contractor', 'category_type' => 'child'],
                // Vendors
                ['name' => 'Hot Water Pressure Washers', 'slug' => 'hot-water-pressure-washers', 'type' => 'vendor', 'category_type' => 'child'],
                ['name' => 'Cold Water Pressure Washers', 'slug' => 'cold-water-pressure-washers', 'type' => 'vendor', 'category_type' => 'child'],
                ['name' => 'Soft Wash Systems', 'slug' => 'soft-wash-systems', 'type' => 'vendor', 'category_type' => 'child'],
                ['name' => 'Hoses & Fittings', 'slug' => 'hoses-fittings', 'type' => 'vendor', 'category_type' => 'child'],
                ['name' => 'Chemicals & Supplies', 'slug' => 'chemicals-supplies', 'type' => 'vendor', 'category_type' => 'child'],
                ['name' => 'Website Design', 'slug' => 'website-design', 'type' => 'vendor', 'category_type' => 'child'],
                ['name' => 'SEO Services', 'slug' => 'seo-services', 'type' => 'vendor', 'category_type' => 'child'],
            ];
            foreach ($cats as $c) {
                BusinessCategory::create($c);
            }
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.business.manage-business');
    }
}
