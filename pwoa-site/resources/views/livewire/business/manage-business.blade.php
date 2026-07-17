<div class="row justify-content-center" x-data="imageCropper()">
    <div class="col-xl-10">
        @if(session('status'))
            <div class="alert alert-success border-0 glass-card d-flex align-items-center p-4 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-3 fs-3 text-success"></i>
                <div class="fw-bold">{{ session('status') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 glass-card d-flex align-items-center p-4 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-3 fs-3 text-danger"></i>
                <div class="fw-bold">{{ session('error') }}</div>
            </div>
        @endif

        @if($showForm)
            <!-- Wizard Headers & Step Indicators -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-4 text-primary">
                        <i class="bi bi-pencil-square fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $isEdit ? 'Edit Business Profile' : 'Business Registration' }}</h4>
                        <p class="text-muted small mb-0">Step {{ $currentStep }} of {{ $totalSteps }} - {{ 
                            $isEdit ? match($currentStep) {
                                1 => 'Company Details',
                                2 => 'Profile Information',
                                3 => 'Categories',
                                4 => $type === 'vendor' ? 'Media' : 'Certifications',
                                5 => $type === 'vendor' ? 'Review' : 'Media',
                                6 => 'Review',
                                default => ''
                            } : match($currentStep) {
                                1 => 'Choose Listing Type',
                                2 => 'Company Details',
                                3 => 'Brand Identity',
                                4 => 'Tagline & Bio',
                                5 => $type === 'contractor' ? 'Service Radius' : 'Vendor Features',
                                6 => $type === 'contractor' ? 'Categories, Badges & Fleet' : 'Vendor Categories',
                                7 => 'Social Media & Submit',
                                default => ''
                            }
                        }}</p>
                    </div>
                </div>
                <button wire:click="toggleForm" class="btn btn-outline-secondary rounded-pill px-4 btn-sm fw-bold shadow-sm">Cancel</button>
            </div>

            <!-- Progress Bar -->
            <div class="progress mb-4 rounded-pill" style="height: 8px;">
                <div class="progress-bar bg-primary rounded-pill progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ ($currentStep / $totalSteps) * 100 }}%"></div>
            </div>

            <div class="card border-0 glass-card overflow-hidden mb-5">
                <div class="card-body p-4 p-md-5">
                    <form wire:submit.prevent="save">
                        
                        <!-- Step 1: Listing Type selection (Create Flow Only) -->
                        @if(!$isEdit && $currentStep === 1)
                            <div class="text-center py-4">
                                <h5 class="fw-bold mb-4 text-dark fs-4">Select Directory Directory Type</h5>
                                <p class="text-secondary mb-5 mx-auto" style="max-width: 500px;">Which directory is this business listing for? Standard membership is $99/year and Gold is $300/year.</p>
                                
                                <div class="row g-4 justify-content-center">
                                    <div class="col-md-5">
                                        <div wire:click="$set('type', 'contractor')" 
                                            class="card cursor-pointer h-100 p-4 border-2 rounded-4 shadow-sm {{ $type === 'contractor' ? 'border-primary bg-brand bg-opacity-05' : 'border-light' }}"
                                            style="transition: all 0.3s ease;">
                                            <div class="card-body text-center">
                                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                                                    <i class="bi bi-person-workspace fs-2"></i>
                                                </div>
                                                <h5 class="fw-bold mb-2">Contractor Directory</h5>
                                                <p class="text-muted small mb-0">For companies performing residential, commercial, or specialty power washing and cleaning services.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div wire:click="$set('type', 'vendor')" 
                                            class="card cursor-pointer h-100 p-4 border-2 rounded-4 shadow-sm {{ $type === 'vendor' ? 'border-primary bg-brand bg-opacity-05' : 'border-light' }}"
                                            style="transition: all 0.3s ease;">
                                            <div class="card-body text-center">
                                                <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                                                    <i class="bi bi-shop fs-2"></i>
                                                </div>
                                                <h5 class="fw-bold mb-2">Vendor Directory</h5>
                                                <p class="text-muted small mb-0">For manufacturers, distributors, software providers, or consultants serving the power washing industry.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Step 2: Company Details (Create Step 2 / Edit Step 1) -->
                        @if((!$isEdit && $currentStep === 2) || ($isEdit && $currentStep === 1))
                            <div class="section-title mb-4">
                                <h5 class="fw-bold mb-1 text-dark">Company Information</h5>
                                <p class="text-muted small">Standard business details and headquarters address.</p>
                                <hr class="mt-2 opacity-10">
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary">Company Name</label>
                                    <input type="text" wire:model.live="name" class="form-control bg-light border-0 py-2.5 rounded-3 shadow-none focus-ring @error('name') is-invalid @enderror" placeholder="e.g. Wash Patrol">
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary">URL Slug</label>
                                    <input type="text" wire:model="slug" class="form-control bg-light border-0 py-2.5 rounded-3 shadow-none focus-ring @error('slug') is-invalid @enderror">
                                    @error('slug') <div class="invalid-feedback text-danger">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary">Company Email</label>
                                    <input type="email" wire:model="email" class="form-control bg-light border-0 py-2.5 rounded-3 shadow-none focus-ring @error('email') is-invalid @enderror" placeholder="office@company.com">
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary">Company Phone</label>
                                    <input type="text" wire:model="phone" class="form-control bg-light border-0 py-2.5 rounded-3 shadow-none focus-ring @error('phone') is-invalid @enderror" placeholder="(555) 000-0000">
                                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary">Website Link</label>
                                    <input type="url" wire:model="website" class="form-control bg-light border-0 py-2.5 rounded-3 shadow-none focus-ring @error('website') is-invalid @enderror" placeholder="https://company.com">
                                    @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary">Years in Business</label>
                                    <input type="number" wire:model="years_in_business" class="form-control bg-light border-0 py-2.5 rounded-3 shadow-none focus-ring @error('years_in_business') is-invalid @enderror" placeholder="e.g. 5">
                                    @error('years_in_business') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                @if($type === 'contractor')
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary">License Number (Optional)</label>
                                        <input type="text" wire:model="license_number" class="form-control bg-light border-0 py-2.5 rounded-3 shadow-none focus-ring @error('license_number') is-invalid @enderror" placeholder="e.g. LIC-12345">
                                        @error('license_number') <div class="invalid-feedback">{{ $message }}</div> @enderror

                                        <div class="mt-3">
                                            <label class="form-label small fw-semibold text-secondary mb-1">Upload License Document (PDF/Image)</label>
                                            <input type="file" wire:model="license_file"
                                                onchange="if(!validateFileSize(this, 2)) { event.stopImmediatePropagation(); }"
                                                class="form-control bg-light border-0 py-1.5 rounded-3 shadow-none focus-ring @error('license_file') is-invalid @enderror">
                                            <div wire:loading wire:target="license_file" class="text-primary mt-1 small">
                                                <span class="spinner-border spinner-border-sm me-1"></span> Uploading license...
                                            </div>
                                            @error('license_file') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                            @if($licensePath)
                                                <div class="mt-1 small">
                                                    <a href="{{ Storage::url($licensePath) }}" target="_blank" class="text-primary text-decoration-none fw-semibold">
                                                        <i class="bi bi-file-earmark-check me-1"></i> View Uploaded License
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-check form-switch py-2 mb-2">
                                            <input class="form-check-input" type="checkbox" wire:model="is_insured" id="is_insured">
                                            <label class="form-check-label fw-bold text-secondary ms-2" for="is_insured">Fully Insured</label>
                                        </div>

                                        <div class="mt-2">
                                            <label class="form-label small fw-semibold text-secondary mb-1">Upload Insurance Certificate (PDF/Image)</label>
                                            <input type="file" wire:model="insurance_file"
                                                onchange="if(!validateFileSize(this, 2)) { event.stopImmediatePropagation(); }"
                                                class="form-control bg-light border-0 py-1.5 rounded-3 shadow-none focus-ring @error('insurance_file') is-invalid @enderror">
                                            <div wire:loading wire:target="insurance_file" class="text-primary mt-1 small">
                                                <span class="spinner-border spinner-border-sm me-1"></span> Uploading insurance...
                                            </div>
                                            @error('insurance_file') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                            @if($insurancePath)
                                                <div class="mt-1 small">
                                                    <a href="{{ Storage::url($insurancePath) }}" target="_blank" class="text-primary text-decoration-none fw-semibold">
                                                        <i class="bi bi-file-earmark-check me-1"></i> View Uploaded Insurance
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <div class="col-12 mt-4">
                                    <h6 class="fw-bold text-dark mb-3">Headquarters Address</h6>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label small text-muted text-uppercase fw-bold mb-1">Street Address</label>
                                            <input type="text" wire:model="address" class="form-control bg-light border-0 py-2 rounded-3 shadow-none focus-ring @error('address') is-invalid @enderror" placeholder="123 Corporate Blvd">
                                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small text-muted text-uppercase fw-bold mb-1">Country</label>
                                            <select wire:model.live="country_id" class="form-select bg-light border-0 py-2.5 rounded-3 shadow-none focus-ring @error('country_id') is-invalid @enderror">
                                                <option value="">Select Country</option>
                                                @foreach($countries as $c)
                                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('country_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small text-muted text-uppercase fw-bold mb-1">Zip / Postal Code</label>
                                            <input type="text" wire:model="zip" class="form-control bg-light border-0 py-2 rounded-3 shadow-none focus-ring @error('zip') is-invalid @enderror" placeholder="10001">
                                            @error('zip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small text-muted text-uppercase fw-bold mb-1">State / Province</label>
                                            <select wire:model.live="state_id" class="form-select bg-light border-0 py-2.5 rounded-3 shadow-none focus-ring @error('state_id') is-invalid @enderror" {{ empty($states) ? 'disabled' : '' }}>
                                                <option value="">Select State</option>
                                                @foreach($states as $s)
                                                    <option value="{{ $s['id'] }}">{{ $s['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('state_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small text-muted text-uppercase fw-bold mb-1">City</label>
                                            <select wire:model.live="city_id" class="form-select bg-light border-0 py-2.5 rounded-3 shadow-none focus-ring @error('city_id') is-invalid @enderror" {{ empty($cities) ? 'disabled' : '' }}>
                                                <option value="">Select City</option>
                                                @foreach($cities as $ct)
                                                    <option value="{{ $ct['id'] }}">{{ $ct['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('city_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Step 3: Brand Identity (Logo / Banner) (Create Step 3 / Edit Step 4 for Vendor / Step 5 for Contractor) -->
                        @if((!$isEdit && $currentStep === 3) || ($isEdit && ($type === 'vendor' ? $currentStep === 4 : $currentStep === 5)))
                            <div class="section-title mb-4">
                                <h5 class="fw-bold mb-1 text-dark">Brand Assets</h5>
                                <p class="text-muted small">Logo and profile cover photo uploads.</p>
                                <hr class="mt-2 opacity-10">
                            </div>

                            <div class="row g-4">
                                <!-- Banner -->
                                <div class="col-12">
                                    <label class="form-label fw-bold text-secondary mb-2">Cover Photo Banner (16:9 Aspect Ratio Recommended)</label>
                                    <div class="banner-upload-container position-relative rounded-4 overflow-hidden bg-light border-2 border-dashed d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <input type="file" id="banner_input" class="d-none" accept="image/*" @change="handleImageUpload($event, 'banner', 16/9)">
                                        
                                        <!-- Loading Overlay -->
                                        <div :class="isUploadingBanner ? 'd-flex' : 'd-none'" class="position-absolute inset-0 bg-white bg-opacity-75 flex-column align-items-center justify-content-center" style="z-index: 5;" x-cloak>
                                            <div class="spinner-border text-primary mb-2" role="status"></div>
                                            <div class="fw-bold small text-secondary" x-text="'Uploading: ' + bannerProgress + '%'"></div>
                                            <div class="progress w-50 mt-1" style="height: 6px; border-radius: 4px;">
                                                <div class="progress-bar bg-primary" role="progressbar" :style="'width: ' + bannerProgress + '%'"></div>
                                            </div>
                                        </div>

                                        <!-- Image Display (Local preview during upload, fallback to temporaryUrl, then storage path) -->
                                        <img :src="localBannerPreview || '{{ $banner ? $banner->temporaryUrl() : ($bannerPath ? (str_starts_with($bannerPath, 'http') ? $bannerPath : Storage::url($bannerPath)) : '') }}'" 
                                             x-show="localBannerPreview || '{{ $banner || $bannerPath ? true : false }}'" 
                                             class="w-100 h-100 object-fit-cover" x-cloak>

                                        <!-- Placeholder -->
                                        <div x-show="!localBannerPreview && !'{{ $banner || $bannerPath ? true : false }}'" class="text-center opacity-50">
                                            <i class="bi bi-image display-4 mb-2"></i>
                                            <p class="fw-bold mb-0">Click to upload banner</p>
                                        </div>
                                        
                                        <label for="banner_input" class="stretched-link cursor-pointer" x-show="!isUploadingBanner"></label>
                                    </div>
                                    @error('banner') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <!-- Logo -->
                                <div class="col-12 mt-4">
                                    <label class="form-label fw-bold text-secondary mb-2">Company Logo (Square Format)</label>
                                    <div class="d-flex align-items-center gap-4">
                                        <div class="logo-upload-container bg-light rounded-4 border-2 border-dashed position-relative d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                            <input type="file" id="logo_input" class="d-none" accept="image/*" @change="handleImageUpload($event, 'logo', 1/1)">
                                            
                                            <!-- Loading Overlay -->
                                            <div :class="isUploadingLogo ? 'd-flex' : 'd-none'" class="position-absolute inset-0 bg-white bg-opacity-75 flex-column align-items-center justify-content-center rounded-4" style="z-index: 5;" x-cloak>
                                                <div class="spinner-border spinner-border-sm text-primary mb-1" role="status"></div>
                                                <span class="fw-bold text-secondary" style="font-size: 0.75rem;" x-text="logoProgress + '%'"></span>
                                            </div>

                                            <!-- Image Display (Local preview during upload, fallback to temporaryUrl, then storage path) -->
                                            <img :src="localLogoPreview || '{{ $logo ? $logo->temporaryUrl() : ($logoPath ? (str_starts_with($logoPath, 'http') ? $logoPath : Storage::url($logoPath)) : '') }}'" 
                                                 x-show="localLogoPreview || '{{ $logo || $logoPath ? true : false }}'" 
                                                 class="w-100 h-100 object-fit-contain rounded-3" x-cloak>

                                            <!-- Placeholder -->
                                            <div x-show="!localLogoPreview && !'{{ $logo || $logoPath ? true : false }}'">
                                                <i class="bi bi-shop fs-1 opacity-50"></i>
                                            </div>
                                            
                                            <label for="logo_input" class="stretched-link cursor-pointer" x-show="!isUploadingLogo"></label>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Select Square Logo</h6>
                                            <p class="text-muted small mb-0">Click to upload an image. Maximum size 1MB.</p>
                                        </div>
                                    </div>
                                    @error('logo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        @endif

                        <!-- Step 4: Tagline & Bio (Create Step 4 / Edit Step 2) -->
                        @if((!$isEdit && $currentStep === 4) || ($isEdit && $currentStep === 2))
                            <div class="section-title mb-4">
                                <h5 class="fw-bold mb-1 text-dark">Marketing Descriptions</h5>
                                <p class="text-muted small">Company tagline and full biography details.</p>
                                <hr class="mt-2 opacity-10">
                            </div>

                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-secondary">Company Tagline / Catchphrase</label>
                                    <input type="text" wire:model="tagline" class="form-control bg-light border-0 py-2.5 rounded-3 shadow-none focus-ring @error('tagline') is-invalid @enderror" placeholder="e.g. Quality washing at honest prices">
                                    @error('tagline') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold text-secondary">Short Description (Search Snippet)</label>
                                    <input type="text" wire:model="short_description" class="form-control bg-light border-0 py-2.5 rounded-3 shadow-none focus-ring @error('short_description') is-invalid @enderror" placeholder="A brief sentence describing your listing (max 250 chars)">
                                    @error('short_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold text-secondary">Full Company Biography</label>
                                    <textarea wire:model="description" rows="8" class="form-control bg-light border-0 py-2.5 rounded-3 shadow-none focus-ring @error('description') is-invalid @enderror" placeholder="Write about your company history, values, crew size, and service guarantees..."></textarea>
                                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        @endif

                        <!-- Step 5: Service Radius (Contractor) OR Vendor Features (Vendor) (Create Step 5 / Edit Step 5 for Vendor / Step 6 for Contractor) -->
                        @if((!$isEdit && $currentStep === 5) || ($isEdit && ($type === 'vendor' ? $currentStep === 5 : $currentStep === 6)))
                            @if($type === 'contractor')
                                <div class="section-title mb-4">
                                    <h5 class="fw-bold mb-1 text-dark">Service Range & Availability</h5>
                                    <p class="text-muted small">Set your operational radius and specialty services.</p>
                                    <hr class="mt-2 opacity-10">
                                </div>

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary">Service Radius Distance</label>
                                        <select wire:model="service_radius_id" class="form-select bg-light border-0 py-2.5 rounded-3 shadow-none focus-ring @error('service_radius_id') is-invalid @enderror">
                                            <option value="">Select Service Radius</option>
                                            @foreach($allServiceRadii as $radius)
                                                <option value="{{ $radius->id }}">{{ $radius->name }} - {{ $radius->description }}</option>
                                            @endforeach
                                        </select>
                                        @error('service_radius_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-12 mt-4">
                                        <h6 class="fw-bold text-dark mb-3">Availability Details</h6>
                                        
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" wire:model="is_emergency_service" id="is_emergency_service">
                                            <label class="form-check-label fw-semibold text-secondary ms-2" for="is_emergency_service">Emergency 24/7 Service Available</label>
                                        </div>

                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" wire:model="is_subcontracting" id="is_subcontracting">
                                            <label class="form-check-label fw-semibold text-secondary ms-2" for="is_subcontracting">Available For Subcontracting Work</label>
                                        </div>

                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" wire:model="is_national_accounts" id="is_national_accounts">
                                            <label class="form-check-label fw-semibold text-secondary ms-2" for="is_national_accounts">Available For National Accounts / Retail Contracts</label>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="section-title mb-4">
                                    <h5 class="fw-bold mb-1 text-dark">Vendor Profile Features</h5>
                                    <p class="text-muted small">Select options and programs that apply to your store.</p>
                                    <hr class="mt-2 opacity-10">
                                </div>

                                <div class="row g-4">
                                    <div class="col-12">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" wire:model="has_online_ordering" id="has_online_ordering">
                                            <label class="form-check-label fw-semibold text-secondary ms-2" for="has_online_ordering">Online Ordering E-Commerce Available</label>
                                        </div>

                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" wire:model="has_local_pickup" id="has_local_pickup">
                                            <label class="form-check-label fw-semibold text-secondary ms-2" for="has_local_pickup">Local Pickup Storefront Option Available</label>
                                        </div>

                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" wire:model="has_member_discounts" id="has_member_discounts">
                                            <label class="form-check-label fw-semibold text-secondary ms-2" for="has_member_discounts">Special Member Discounts offered to PWOA Members</label>
                                        </div>

                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" wire:model="wants_preferred_program" id="wants_preferred_program">
                                            <label class="form-check-label fw-semibold text-secondary ms-2" for="wants_preferred_program">Interested in Preferred Vendor Program</label>
                                        </div>

                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" wire:model="wants_partnership" id="wants_partnership">
                                            <label class="form-check-label fw-semibold text-secondary ms-2" for="wants_partnership">Interested in PowerWashing.com partnership</label>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <!-- Service Categories (Create Step 6 / Edit Step 3) -->
                        @if((!$isEdit && $currentStep === 6) || ($isEdit && $currentStep === 3))
                            <div class="section-title mb-4">
                                <h5 class="fw-bold mb-1 text-dark">Service / Product Categories</h5>
                                <p class="text-muted small">Select one or more categories that apply to your business.</p>
                                <hr class="mt-2 opacity-10">
                            </div>

                            <div class="row g-4 mb-4">
                                <!-- Categories -->
                                <div class="col-12">
                                    <h6 class="fw-bold text-dark mb-2">Service / Product Categories</h6>
                                    <p class="text-muted small mb-3">Select one or more categories that apply:</p>
                                    <div class="row g-2">
                                        @foreach($allCategories as $category)
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="{{ $category->id }}" wire:model="selected_categories" id="cat_{{ $category->id }}">
                                                    <label class="form-check-label small" for="cat_{{ $category->id }}">
                                                        {{ $category->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Certifications & Fleet (Create Step 6 / Edit Step 4) -->
                        @if($type === 'contractor' && ((!$isEdit && $currentStep === 6) || ($isEdit && $currentStep === 4)))
                            <div class="section-title {{ !$isEdit ? 'mt-4 pt-3 border-top' : '' }} mb-4">
                                <h5 class="fw-bold mb-1 text-dark">Certifications & Equipment Fleet</h5>
                                <p class="text-muted small">Select the certifications your business holds and manage your fleet inventory.</p>
                                <hr class="mt-2 opacity-10">
                            </div>

                            <div class="row g-4">
                                <!-- Certifications -->
                                <div class="col-12">
                                    <h6 class="fw-bold text-dark mb-2">Directory Certifications</h6>
                                    <p class="text-muted small mb-3">Select the certifications your business holds and upload proof files:</p>
                                    
                                    <div class="d-flex flex-column gap-3">
                                        @foreach($allCertifications as $cert)
                                            <div class="p-3 bg-light rounded-4 border">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="{{ $cert->id }}" wire:model.live="selected_certifications" id="cert_{{ $cert->id }}">
                                                    <label class="form-check-label small fw-bold text-dark text-decoration-none cursor-pointer" for="cert_{{ $cert->id }}">
                                                        {{ $cert->name }}
                                                    </label>
                                                    <div class="text-muted small ms-4">{{ $cert->description }}</div>
                                                </div>
                                                
                                                @if(in_array($cert->id, $selected_certifications))
                                                    <div class="mt-3 ps-4 border-start border-primary border-3">
                                                        <label class="form-label small fw-semibold text-secondary mb-1">Upload Certification Proof (PDF/Image)</label>
                                                        <input type="file" wire:model="certification_files.{{ $cert->id }}" onchange="if(!validateFileSize(this, 2)) { event.stopImmediatePropagation(); }" class="form-control bg-white border py-1.5 rounded-3">
                                                        <div wire:loading wire:target="certification_files.{{ $cert->id }}" class="text-primary mt-1 small">
                                                            <span class="spinner-border spinner-border-sm me-1"></span> Uploading proof...
                                                        </div>
                                                        @error('certification_files.'.$cert->id) <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                                        @if(isset($certificationPaths[$cert->id]) && $certificationPaths[$cert->id])
                                                            <div class="mt-2 small">
                                                                <a href="{{ Storage::url($certificationPaths[$cert->id]) }}" target="_blank" class="text-primary text-decoration-none fw-semibold">
                                                                    <i class="bi bi-file-earmark-check me-1"></i> View Uploaded Proof
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Equipment Fleet -->
                                <div class="col-12 mt-4 pt-3 border-top">
                                    <h6 class="fw-bold text-dark mb-2">Equipment Owned (Fleet Inventory)</h6>
                                    <p class="text-muted small mb-3">Select equipment owned and provide quantity and specifications:</p>
                                    
                                    <div class="d-flex flex-column gap-3">
                                        @foreach($allEquipments as $equip)
                                            <div class="p-3 bg-light rounded-4 border">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="{{ $equip->id }}" wire:model.live="selected_equipments" id="equip_{{ $equip->id }}">
                                                    <label class="form-check-label fw-bold text-dark" for="equip_{{ $equip->id }}">
                                                        {{ $equip->name }}
                                                    </label>
                                                </div>
                                                
                                                @if(in_array($equip->id, $selected_equipments))
                                                    <div class="row g-2 mt-2 px-3">
                                                        <div class="col-sm-4">
                                                            <label class="small text-secondary mb-1">Fleet Quantity</label>
                                                            <input type="number" wire:model="equipment_quantities.{{ $equip->id }}" class="form-control bg-white border py-1.5 rounded-3" min="1" placeholder="1">
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <label class="small text-secondary mb-1">Specifications (e.g. 8 GPM, Landa skid)</label>
                                                            <input type="text" wire:model="equipment_specs.{{ $equip->id }}" class="form-control bg-white border py-1.5 rounded-3" placeholder="Specs or brand details...">
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Step 7: Social Media & Final Review (Create Step 7 / Edit Step 5 for Vendor / Step 6 for Contractor) -->
                        @if((!$isEdit && $currentStep === 7) || ($isEdit && ($type === 'vendor' ? $currentStep === 5 : $currentStep === 6)))
                            <div class="section-title mb-4">
                                <h5 class="fw-bold mb-1 text-dark">Social Profiles & Final Review</h5>
                                <p class="text-muted small">Link your socials and review your company detail card.</p>
                                <hr class="mt-2 opacity-10">
                            </div>

                            <div class="row g-4 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted text-uppercase fw-bold mb-1">Facebook</label>
                                    <input type="url" wire:model="facebook" class="form-control bg-light border-0 py-2 rounded-3" placeholder="https://facebook.com/company">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted text-uppercase fw-bold mb-1">Instagram</label>
                                    <input type="url" wire:model="instagram" class="form-control bg-light border-0 py-2 rounded-3" placeholder="https://instagram.com/company">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted text-uppercase fw-bold mb-1">LinkedIn</label>
                                    <input type="url" wire:model="linkedin" class="form-control bg-light border-0 py-2 rounded-3" placeholder="https://linkedin.com/company">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted text-uppercase fw-bold mb-1">YouTube</label>
                                    <input type="url" wire:model="youtube" class="form-control bg-light border-0 py-2 rounded-3" placeholder="https://youtube.com/company">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted text-uppercase fw-bold mb-1">TikTok</label>
                                    <input type="url" wire:model="tiktok" class="form-control bg-light border-0 py-2 rounded-3" placeholder="https://tiktok.com/@company">
                                </div>
                            </div>

                            <div class="p-4 bg-light rounded-4 border mt-4">
                                <h6 class="fw-bold mb-3"><i class="bi bi-shield-check text-primary me-2"></i> Review Submission</h6>
                                <p class="small text-secondary mb-0">By clicking save, your listing will be updated or sent to our administrative moderation queue. Verify all details before publishing.</p>
                            </div>
                        @endif

                        <!-- Step navigation controls -->
                        <div class="pt-5 border-top d-flex justify-content-between align-items-center mt-4">
                            @if($currentStep > 1)
                                <button type="button" wire:click="prevStep" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" wire:loading.attr="disabled" wire:target="prevStep">
                                    <span wire:loading.remove wire:target="prevStep">Previous Step</span>
                                    <span wire:loading wire:target="prevStep"><span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Loading...</span>
                                </button>
                            @else
                                <div></div>
                            @endif

                            @if($currentStep < $totalSteps)
                                <button type="button" wire:click="nextStep" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" wire:loading.attr="disabled" wire:target="nextStep">
                                    <span wire:loading.remove wire:target="nextStep">Next Step <i class="bi bi-arrow-right ms-2"></i></span>
                                    <span wire:loading wire:target="nextStep"><span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...</span>
                                </button>
                            @else
                                <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm" wire:loading.attr="disabled" wire:target="save">
                                    <span wire:loading.remove wire:target="save">Save & Submit <i class="bi bi-check-circle ms-2"></i></span>
                                    <span wire:loading wire:target="save"><span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Saving...</span>
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        @else
            <!-- Multi-Listing Dashboard Overview -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-4 text-primary">
                        <i class="bi bi-speedometer2 fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Business Listings Directory Panel</h4>
                        <p class="text-muted small mb-0">Manage all of your power washing contractor and vendor profiles in one dashboard.</p>
                    </div>
                </div>
                @if(auth()->user()->hasBusiness())
                    <span class="text-muted small fw-bold bg-light px-3 py-2 rounded-pill border">
                        <i class="bi bi-info-circle me-1 text-primary"></i> You already have an active business listing.
                    </span>
                @else
                    <button wire:click="createListing" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm hvr-grow">
                        <i class="bi bi-building-add me-2"></i>Add Listing
                    </button>
                @endif
            </div>

            @if(count($myBusinesses) > 0)
                @if(!empty($completionData))
                    <!-- Profile Completion Widget (Modern SaaS Dashboard Style) -->
                    <div class="card border-0 glass-card shadow-sm mb-4 overflow-hidden position-relative" style="background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.5) !important;">
                        <div class="card-body p-4">
                            <div class="row align-items-center g-4">
                                <!-- Left side: 100px SVG circular progress -->
                                <div class="col-md-auto col-12 d-flex justify-content-center">
                                    <div class="position-relative d-inline-flex align-items-center justify-content-center">
                                        <svg width="100" height="100" viewBox="0 0 100 100" style="transform: rotate(-90deg);">
                                            <!-- Track circle -->
                                            <circle cx="50" cy="50" r="44" stroke="#f1f5f9" stroke-width="6" fill="transparent" />
                                            <!-- Progress circle -->
                                            <circle cx="50" cy="50" r="44" stroke="{{ $completionData['color_hex'] }}" stroke-width="6" fill="transparent"
                                                    stroke-dasharray="276.46"
                                                    stroke-dashoffset="{{ 276.46 * (1 - $completionData['percentage'] / 100) }}"
                                                    stroke-linecap="round"
                                                    style="transition: stroke-dashoffset 0.8s ease-in-out;" />
                                        </svg>
                                        <div class="position-absolute text-center">
                                            <span class="fs-4 fw-extrabold text-dark mb-0 d-block">{{ $completionData['percentage'] }}%</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Middle side: SaaS Details & Missing Items list -->
                                <div class="col-md col-12">
                                    <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                        <h5 class="fw-bold mb-0 text-dark">Profile Completion</h5>
                                        <span class="badge rounded-pill bg-{{ $completionData['status_class'] }}-subtle text-{{ $completionData['status_class'] }} fw-bold" style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">
                                            {{ $completionData['status_label'] }}
                                        </span>
                                        <span class="text-secondary small">
                                            @if($completionData['percentage'] === 100)
                                                • Complete Profile
                                            @else
                                                • {{ count($completionData['missing_items']) }} {{ count($completionData['missing_items']) === 1 ? 'item' : 'items' }} remaining
                                            @endif
                                        </span>
                                    </div>

                                    @if($completionData['percentage'] === 100)
                                        <p class="text-success small mb-0 mt-1"><i class="bi bi-check-circle-fill me-1.5"></i>Your profile is 100% complete and fully optimized for discovery!</p>
                                    @elseif(count($completionData['missing_items']) === 1)
                                        <div class="small">
                                            <span class="text-primary-emphasis fw-semibold">Almost Complete 🎉</span>
                                            <span class="text-secondary">{{ $completionData['missing_items'][0]['name'] }} to reach 100%.</span>
                                        </div>
                                    @else
                                        <div class="mt-2">
                                            <span class="text-secondary small fw-bold">Missing Items:</span>
                                            <ul class="list-unstyled mb-0 mt-1 small">
                                                @foreach($completionData['missing_items'] as $item)
                                                    <li class="text-secondary mb-1">
                                                        <span class="text-muted me-1.5">•</span> {{ $item['name'] }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>

                                <!-- Right side: Clean merged action button (no cards or extra borders) -->
                                <div class="col-md-auto col-12 text-md-end text-center">
                                    @if($completionData['percentage'] === 100)
                                        <button class="btn btn-success rounded-pill px-4 py-2.5 fw-bold shadow-sm" type="button" disabled>
                                            <i class="bi bi-check-all me-1"></i> Profile Complete
                                        </button>
                                    @else
                                        <button wire:click="continueProfile({{ $myBusinesses->first()->id }})" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm hvr-grow" type="button" wire:loading.attr="disabled" wire:target="continueProfile">
                                            <span wire:loading.remove wire:target="continueProfile"><i class="bi bi-arrow-right-circle me-1"></i> Complete Profile</span>
                                            <span wire:loading wire:target="continueProfile"><span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Loading...</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row g-4">
                    @foreach($myBusinesses as $biz)
                        <div class="col-md-6" wire:key="card_{{ $biz->id }}">
                            <div class="card border-0 glass-card overflow-hidden shadow-sm h-100">
                                <div class="position-relative" style="height: 120px; background-color: #f8fafc;">
                                    @if($biz->cover_photo_path)
                                        <img src="{{ str_starts_with($biz->cover_photo_path, 'http') ? $biz->cover_photo_path : Storage::url($biz->cover_photo_path) }}" class="w-100 h-100 object-fit-cover opacity-75">
                                    @else
                                        <div class="w-100 h-100 bg-brand-gradient opacity-25"></div>
                                    @endif
                                    
                                    <div class="position-absolute bottom-0 start-0 p-3">
                                        <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center overflow-hidden" style="width: 50px; height: 50px; border: 2px solid #fff;">
                                            @if($biz->logo_path)
                                                <img src="{{ str_starts_with($biz->logo_path, 'http') ? $biz->logo_path : Storage::url($biz->logo_path) }}" class="w-100 h-100 object-fit-contain">
                                            @else
                                                <i class="bi bi-building text-primary fs-5"></i>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="position-absolute top-0 end-0 p-3">
                                        <span class="badge rounded-pill bg-{{ $biz->status === 'approved' ? 'success' : ($biz->status === 'pending' ? 'warning' : 'danger') }}-subtle text-{{ $biz->status === 'approved' ? 'success' : ($biz->status === 'pending' ? 'warning' : 'danger') }} fw-bold text-uppercase">
                                            {{ $biz->status }}
                                        </span>
                                    </div>
                                </div>

                                <div class="card-body p-4 pt-4 mt-2 d-flex flex-column h-100">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="fw-bold mb-0 text-truncate" style="max-width: 80%;">{{ $biz->name }}</h5>
                                        <span class="badge bg-secondary text-uppercase small">{{ $biz->type }}</span>
                                    </div>
                                    
                                    <p class="small text-muted mb-3"><i class="bi bi-geo-alt me-1"></i> {{ $biz->city->name ?? 'N/A' }}, {{ $biz->state->name ?? 'N/A' }}</p>
                                    
                                    @if($biz->status === 'rejected' && $biz->rejection_reason)
                                        <div class="alert alert-danger border-0 rounded-4 p-3 mb-3" style="font-size: 0.85rem; background: rgba(239, 68, 68, 0.08); border-left: 4px solid #ef4444 !important;">
                                            <div class="fw-bold text-danger mb-1"><i class="bi bi-exclamation-octagon-fill me-1"></i> Revision Required:</div>
                                            <p class="mb-0 text-secondary" style="color: #4b5563 !important;">{{ $biz->rejection_reason }}</p>
                                        </div>
                                    @endif
                                    
                                    <p class="text-secondary small line-clamp-2 flex-grow-1">{{ strip_tags($biz->description) }}</p>

                                    @php
                                        $bizCompletion = app(\App\Services\ProfileCompletionService::class)->getCompletionData($biz);
                                    @endphp

                                    <div class="my-3 p-3 bg-light bg-opacity-50 rounded-4 border">
                                        <div class="d-flex justify-content-between align-items-center mb-1.5">
                                            <span class="small text-secondary fw-bold text-uppercase tracking-wider" style="font-size: 0.7rem;">Profile Progress</span>
                                            <span class="small fw-extrabold text-{{ $bizCompletion['status_class'] }}">{{ $bizCompletion['percentage'] }}% ({{ $bizCompletion['status_label'] }})</span>
                                        </div>
                                        <div class="progress rounded-pill shadow-inner" style="height: 6px;">
                                            <div class="progress-bar bg-{{ $bizCompletion['status_class'] }} rounded-pill" role="progressbar" style="width: {{ $bizCompletion['percentage'] }}%"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex flex-wrap gap-1 mb-4 mt-2">
                                        @foreach($biz->categories->take(3) as $c)
                                            <span class="badge bg-light text-dark border px-2 py-1 rounded">{{ $c->name }}</span>
                                        @endforeach
                                    </div>

                                    <div class="d-flex gap-2 border-top pt-3 mt-auto">
                                        <button wire:click="editListing({{ $biz->id }})" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold flex-grow-1"><i class="bi bi-pencil-square me-1"></i> Edit</button>
                                        
                                        @if($biz->status === 'approved')
                                            <a href="{{ route($biz->type === 'vendor' ? 'vendors.show' : 'contractors.show', $biz->slug) }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold flex-grow-1"><i class="bi bi-box-arrow-up-right me-1"></i> View</a>
                                        @endif

                                        <button onclick="confirm('Are you sure you want to delete this listing?') || event.stopImmediatePropagation()" wire:click="deleteListing({{ $biz->id }})" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5 glass-card rounded-5 border-0 p-5 mt-4 shadow-lg bg-white position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-05 pointer-events-none">
                        <i class="bi bi-shop position-absolute" style="font-size: 20rem; top: -5rem; left: -5rem;"></i>
                    </div>
                    <div class="position-relative z-1">
                        <div class="bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center rounded-circle mb-4" style="width: 120px; height: 120px;">
                            <i class="bi bi-building-add text-primary display-4"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-3">No Listings Found</h2>
                        <p class="text-muted mb-5 mx-auto fs-5" style="max-width: 550px;">You haven't listed your company in our directories yet. Add a contractor or vendor listing to connect with clients.</p>
                        <button wire:click="createListing" class="btn btn-primary btn-lg rounded-pill px-5 py-3 fw-bold shadow-lg hvr-grow">
                            Start Registration Wizard <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            @endif
        @endif
    </div>

    <!-- Cropper Modal -->
    <div class="modal fade" id="cropperModal" tabindex="-1" aria-hidden="true" x-ref="cropperModal" wire:ignore>
        <div class="modal-dialog modal-dialog-centered cropper-custom-dialog">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header border-0 bg-light p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0">Crop Your Image</h5>
                        <p class="text-muted small mb-0" x-text="currentType === 'logo' ? 'Aspect Ratio 1:1' : 'Aspect Ratio 16:9'"></p>
                    </div>
                    <div class="badge bg-primary-subtle text-primary border border-primary-subtle p-2 rounded-pill fw-semibold me-3">
                        <i class="bi bi-info-circle me-1"></i>
                        <span x-text="currentType === 'logo' ? 'Recommended: 500x500 px (Logo)' : 'Recommended: 1600x900 px (Cover Banner)'"></span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4 align-items-stretch">
                        <!-- Crop Area: at least 70% of modal width (9/12 cols is 75%) -->
                        <div class="col-md-9 col-12">
                            <div class="cropper-custom-container position-relative">
                                <img id="cropperImage" src="" style="max-width: 100%; display: block;">
                            </div>
                            
                            <!-- Zoom Slider -->
                            <div class="d-flex align-items-center justify-content-center gap-3 mt-3 w-100 px-4">
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center" @click="cropper && cropper.zoom(-0.1)" style="width: 32px; height: 32px; padding: 0;">
                                    <i class="bi bi-zoom-out"></i>
                                </button>
                                <input type="range" class="form-range" id="cropperZoomSlider" style="max-width: 350px;" 
                                       min="0.01" max="5" step="0.01" value="1"
                                       @input="cropper && cropper.zoomTo($event.target.value)">
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center" @click="cropper && cropper.zoom(0.1)" style="width: 32px; height: 32px; padding: 0;">
                                    <i class="bi bi-zoom-in"></i>
                                </button>
                            </div>

                            <!-- Control Buttons -->
                            <div class="d-flex flex-wrap gap-2 justify-content-center mt-3">
                                <div class="btn-group shadow-sm">
                                    <button type="button" class="btn btn-outline-secondary" @click="cropper && cropper.setDragMode('move')" title="Drag Mode">
                                        <i class="bi bi-arrows-move me-1"></i> Drag Image
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" @click="cropper && cropper.setDragMode('crop')" title="Crop Mode">
                                        <i class="bi bi-crop me-1"></i> Crop Box
                                    </button>
                                </div>
                                <button type="button" class="btn btn-outline-danger shadow-sm" @click="cropper && cropper.reset()" title="Reset Image">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                                </button>
                            </div>
                        </div>

                        <!-- Live Preview Area: 25% of modal width (3/12 cols) -->
                        <div class="col-md-3 col-12">
                            <div class="d-flex flex-column align-items-center justify-content-center h-100 bg-light p-3 rounded-4 border text-center">
                                <h6 class="fw-bold text-secondary mb-3 small text-uppercase tracking-wider">Live Preview</h6>
                                <div class="img-preview shadow-sm" :style="currentType === 'logo' ? 'width: 150px; height: 150px; border-radius: 16px; overflow: hidden; background-color: #fff; border: 2px solid #e2e8f0;' : 'width: 160px; height: 90px; border-radius: 8px; overflow: hidden; background-color: #fff; border: 2px solid #e2e8f0;'"></div>
                                <p class="text-muted small mt-3 mb-0" x-text="currentType === 'logo' ? 'Square Logo' : 'Cover Photo'"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" @click="cropAndUpload()">Crop & Upload</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .modal, 
        .modal-dialog, 
        .modal-content, 
        .cropper-custom-container, 
        .cropper-container {
            pointer-events: auto !important;
        }

        @media (min-width: 992px) {
            .cropper-custom-dialog {
                max-width: 900px !important;
                width: 900px !important;
            }
        }
        @media (min-width: 768px) and (max-width: 991.98px) {
            .cropper-custom-dialog {
                max-width: 750px !important;
                width: 750px !important;
            }
        }
        @media (max-width: 767.98px) {
            .cropper-custom-dialog {
                max-width: 100% !important;
                margin: 0.5rem !important;
                padding: 0 !important;
            }
            .cropper-custom-dialog .modal-content {
                border-radius: 1rem !important;
            }
        }
        .cropper-custom-container {
            width: 100%;
            background-color: #f1f5f9;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #cbd5e1;
        }
        @media (min-width: 768px) {
            .cropper-custom-container {
                height: 500px;
            }
        }
        @media (max-width: 767.98px) {
            .cropper-custom-container {
                height: 350px;
            }
        }
        .img-preview {
            overflow: hidden;
            background-color: #fff;
            border: 1px solid #cbd5e1;
        }
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function validateFileSize(input, maxMb = 2) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const maxSize = maxMb * 1024 * 1024;
                if (file.size > maxSize) {
                    const message = `${file.name} is too large. Maximum allowed size is ${maxMb}MB.`;
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Too Large',
                            text: message,
                            confirmButtonColor: '#0d6efd'
                        });
                    } else {
                        alert(message);
                    }
                    input.value = '';
                    return false;
                }
            }
            return true;
        }

        function imageCropper() {
            return {
                cropper: null,
                currentType: null,
                currentAspectRatio: null,
                imageSrc: null,
                modal: null,
                isUploadingLogo: false,
                isUploadingBanner: false,
                logoProgress: 0,
                bannerProgress: 0,
                localLogoPreview: null,
                localBannerPreview: null,

                init() {
                    const el = document.getElementById('cropperModal');
                    
                    // Listen for bootstrap modal fully shown event
                    el.addEventListener('shown.bs.modal', () => {
                        const img = document.getElementById('cropperImage');
                        img.src = this.imageSrc;
                        
                        // Destroy previous cropper just in case
                        if (this.cropper) {
                            this.cropper.destroy();
                            this.cropper = null;
                        }

                        // Initialize Cropper only when modal is fully visible and dimensions are calculated
                        this.cropper = new Cropper(img, {
                            aspectRatio: this.currentAspectRatio,
                            viewMode: 1,
                            dragMode: 'move',
                            autoCropArea: 0.8,
                            responsive: true,
                            restore: false,
                            checkCrossOrigin: false,
                            zoomable: true,
                            zoomOnWheel: true,
                            zoomOnTouch: true,
                            toggleDragModeOnDblclick: false,
                            preview: '.img-preview',
                            ready: () => {
                                const imageData = this.cropper.getImageData();
                                const initialRatio = imageData.width / imageData.naturalWidth;
                                const slider = document.getElementById('cropperZoomSlider');
                                if (slider) {
                                    slider.value = initialRatio.toFixed(3);
                                }
                            },
                            zoom: (e) => {
                                const slider = document.getElementById('cropperZoomSlider');
                                if (slider) {
                                    slider.value = e.detail.ratio.toFixed(3);
                                }
                            }
                        });
                    });

                    // Clean up when modal is hidden
                    el.addEventListener('hidden.bs.modal', () => {
                        if (this.cropper) {
                            this.cropper.destroy();
                            this.cropper = null;
                        }
                        const img = document.getElementById('cropperImage');
                        img.src = '';
                    });
                },

                getModal() {
                    const el = document.getElementById('cropperModal');
                    return bootstrap.Modal.getOrCreateInstance(el);
                },

                handleImageUpload(event, type, aspectRatio) {
                    const file = event.target.files[0];
                    if (!file) return;

                    // Validate image file size before starting cropper (Logo: max 1MB, Banner: max 2MB)
                    const maxMb = type === 'logo' ? 1 : 2;
                    if (!validateFileSize(event.target, maxMb)) {
                        return;
                    }

                    this.currentType = type;
                    this.currentAspectRatio = aspectRatio;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imageSrc = e.target.result;
                        
                        if (this.cropper) {
                            this.cropper.destroy();
                            this.cropper = null;
                        }

                        this.getModal().show();
                        
                        // Clear the input value so the change event can fire again for the same file
                        event.target.value = '';
                    };
                    reader.readAsDataURL(file);
                },

                cropAndUpload() {
                    if (!this.cropper) return;

                    const canvas = this.cropper.getCroppedCanvas({
                        width: this.currentType === 'banner' ? 1600 : 500,
                        height: this.currentType === 'banner' ? 900 : 500,
                    });

                    canvas.toBlob((blob) => {
                        const file = new File([blob], `${this.currentType}.jpg`, { type: 'image/jpeg' });
                        const localUrl = URL.createObjectURL(blob);
                        
                        if (this.currentType === 'logo') {
                            this.localLogoPreview = localUrl;
                            this.isUploadingLogo = true;
                            this.logoProgress = 0;
                            @this.upload(
                                'logo',
                                file,
                                () => {
                                    this.isUploadingLogo = false;
                                    this.localLogoPreview = null;
                                },
                                () => {
                                    this.isUploadingLogo = false;
                                    this.localLogoPreview = null;
                                    alert('Logo upload failed.');
                                },
                                (event) => {
                                    this.logoProgress = event.detail.progress;
                                }
                            );
                        } else {
                            this.localBannerPreview = localUrl;
                            this.isUploadingBanner = true;
                            this.bannerProgress = 0;
                            @this.upload(
                                'banner',
                                file,
                                () => {
                                    this.isUploadingBanner = false;
                                    this.localBannerPreview = null;
                                },
                                () => {
                                    this.isUploadingBanner = false;
                                    this.localBannerPreview = null;
                                    alert('Banner upload failed.');
                                },
                                (event) => {
                                    this.bannerProgress = event.detail.progress;
                                }
                            );
                        }

                        this.getModal().hide();
                    }, 'image/jpeg', 0.9);
                }
            }
        }
    </script>
</div>