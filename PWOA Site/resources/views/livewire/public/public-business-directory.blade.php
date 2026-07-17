<div>
    <section class="section-hero bg-brand-gradient py-4 py-md-5">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-12 text-center text-lg-start">
                    <h1 class="display-5 fw-bold mb-3">{{ ucfirst($type) }} Directory</h1>
                    <p class="lead text-white-50 mb-0 px-2 px-lg-0">
                        @if($type === 'contractor')
                            Search trusted contractor listings by specialty, location, and membership level.
                        @else
                            Browse suppliers, manufacturers, and retail partners serving pressure washing professionals.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <!-- Left Sidebar Filters -->
                <div class="col-lg-3">
                    <div class="card border-0 glass-card sticky-top shadow-sm" style="top: 100px;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Filter Results</h5>
                            
                            <!-- Search -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Search</label>
                                <div class="position-relative">
                                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control bg-light border-0 py-2 ps-5 rounded-3 w-100 shadow-none focus-ring" placeholder="Name, specialty, city...">
                                </div>
                            </div>
                            
                            <!-- State -->
                            <div class="mb-4" wire:ignore>
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">State</label>
                                <select id="state_select" class="form-select select2-searchable w-100">
                                    <option value="">All States</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}" {{ $state_id == $state->id ? 'selected' : '' }}>
                                            {{ $state->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- City -->
                            @if($state_id)
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">City</label>
                                <select wire:model.live="city_id" class="form-select bg-light border-0 rounded-3 py-2">
                                    <option value="">All Cities</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            
                            <!-- Category -->
                            <div class="mb-4" wire:ignore>
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Category</label>
                                <select id="category_select" class="form-select select2-searchable w-100">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ $category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Membership -->
                            <div class="mb-4" wire:ignore>
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Membership Tier</label>
                                <select id="membership_select" class="form-select select2-searchable w-100">
                                    <option value="">All Tiers</option>
                                    <option value="standard" {{ $membership_tier == 'standard' ? 'selected' : '' }}>Standard</option>
                                    <option value="gold" {{ $membership_tier == 'gold' ? 'selected' : '' }}>Gold</option>
                                </select>
                            </div>

                            @if($type === 'contractor')
                            <!-- Certification -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Certification</label>
                                <select wire:model.live="certification_id" class="form-select bg-light border-0 rounded-3 py-2">
                                    <option value="">All Certifications</option>
                                    @foreach($certifications as $cert)
                                        <option value="{{ $cert->id }}">{{ $cert->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Equipment Owned -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Equipment Owned</label>
                                <select wire:model.live="equipment_id" class="form-select bg-light border-0 rounded-3 py-2">
                                    <option value="">All Equipment</option>
                                    @foreach($equipments as $equip)
                                        <option value="{{ $equip->id }}">{{ $equip->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Service Radius -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Service Radius</label>
                                <select wire:model.live="service_radius_id" class="form-select bg-light border-0 rounded-3 py-2">
                                    <option value="">All Radii</option>
                                    @foreach($serviceRadii as $radius)
                                        <option value="{{ $radius->id }}">{{ $radius->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Features Switches -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Features</label>
                                
                                <div class="form-check form-switch mb-2 py-1">
                                    <input class="form-check-input" type="checkbox" wire:model.live="certified_only" id="certifiedOnly">
                                    <label class="form-check-label small fw-bold ms-1" for="certifiedOnly">PWOA Certified</label>
                                </div>

                                <div class="form-check form-switch mb-2 py-1">
                                    <input class="form-check-input" type="checkbox" wire:model.live="verified_only" id="verifiedOnly">
                                    <label class="form-check-label small fw-bold ms-1" for="verifiedOnly">Verified Contractor</label>
                                </div>

                                <div class="form-check form-switch mb-2 py-1">
                                    <input class="form-check-input" type="checkbox" wire:model.live="is_emergency" id="isEmergency">
                                    <label class="form-check-label small fw-bold ms-1" for="isEmergency">Emergency 24/7</label>
                                </div>

                                <div class="form-check form-switch mb-2 py-1">
                                    <input class="form-check-input" type="checkbox" wire:model.live="is_subcontracting" id="isSubcontracting">
                                    <label class="form-check-label small fw-bold ms-1" for="isSubcontracting">Subcontracting Available</label>
                                </div>

                                <div class="form-check form-switch mb-2 py-1">
                                    <input class="form-check-input" type="checkbox" wire:model.live="is_national_accounts" id="isNationalAccounts">
                                    <label class="form-check-label small fw-bold ms-1" for="isNationalAccounts">National Accounts</label>
                                </div>
                            </div>
                            @endif

                            @if($type === 'vendor')
                            <!-- Role -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Vendor Role</label>
                                <select wire:model.live="vendor_role" class="form-select bg-light border-0 rounded-3 py-2">
                                    <option value="">All Roles</option>
                                    <option value="manufacturer">Manufacturer</option>
                                    <option value="distributor">Distributor</option>
                                </select>
                            </div>

                            <!-- Features Switches -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Features</label>
                                
                                <div class="form-check form-switch mb-2 py-1">
                                    <input class="form-check-input" type="checkbox" wire:model.live="preferred_only" id="preferredOnly">
                                    <label class="form-check-label small fw-bold ms-1" for="preferredOnly">Preferred Vendor</label>
                                </div>

                                <div class="form-check form-switch mb-2 py-1">
                                    <input class="form-check-input" type="checkbox" wire:model.live="has_online_ordering" id="hasOnlineOrdering">
                                    <label class="form-check-label small fw-bold ms-1" for="hasOnlineOrdering">Online Ordering</label>
                                </div>

                                <div class="form-check form-switch mb-2 py-1">
                                    <input class="form-check-input" type="checkbox" wire:model.live="has_local_pickup" id="hasLocalPickup">
                                    <label class="form-check-label small fw-bold ms-1" for="hasLocalPickup">Local Pickup</label>
                                </div>

                                <div class="form-check form-switch mb-2 py-1">
                                    <input class="form-check-input" type="checkbox" wire:model.live="has_member_discounts" id="hasMemberDiscounts">
                                    <label class="form-check-label small fw-bold ms-1" for="hasMemberDiscounts">Member Discounts</label>
                                </div>
                            </div>
                            @endif

                            <div class="d-grid gap-2 pt-2 border-top mt-4">
                                <button wire:click="resetFilters" class="btn btn-outline-secondary btn-sm rounded-3">
                                    Reset Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Results Area -->
                <div class="col-lg-9">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
                        <h2 class="h4 mb-0 fw-bold text-center text-sm-start">
                            Results Found
                        </h2>
                    </div>

                    <div class="row g-4" wire:loading.class="opacity-50 transition-all">
                        @forelse($businesses as $business)
                        <div class="col-12 col-md-6 col-xl-4" wire:key="{{ $business->id }}">
                            <div class="card card-soft h-100 hover-scale overflow-hidden shadow-sm border-0 w-100">
                                <div class="bg-light position-relative" style="height: 160px; background: #f8fafc;">
                                    @if($type === 'vendor')
                                        @if($business->logo_path)
                                            <img src="{{ str_starts_with($business->logo_path, 'http') ? $business->logo_path : Storage::url($business->logo_path) }}" class="w-100 h-100 object-fit-contain p-4">
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-primary-subtle text-primary">
                                                <i class="bi bi-shop fs-1"></i>
                                            </div>
                                        @endif
                                    @else
                                        @if($business->cover_photo_path)
                                            <img src="{{ str_starts_with($business->cover_photo_path, 'http') ? $business->cover_photo_path : Storage::url($business->cover_photo_path) }}" class="w-100 h-100 object-fit-cover opacity-75">
                                        @else
                                            <div class="w-100 h-100 bg-brand-gradient opacity-25"></div>
                                        @endif
                                        <div class="position-absolute bottom-0 start-0 p-3">
                                            <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center overflow-hidden" style="width: 60px; height: 60px; border: 3px solid #fff;">
                                                @if($business->logo_path)
                                                    <img src="{{ str_starts_with($business->logo_path, 'http') ? $business->logo_path : Storage::url($business->logo_path) }}" class="w-100 h-100 object-fit-contain">
                                                @else
                                                    <i class="bi bi-person-workspace text-primary fs-4"></i>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    <div class="position-absolute top-0 end-0 p-3 d-flex flex-column align-items-end gap-1">
                                        <span class="badge bg-white text-dark shadow-sm rounded-pill small px-2 py-1">
                                            {{ ucfirst($business->membership_tier ?? 'Standard') }}
                                        </span>
                                        @if($business->featured)
                                            <span class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                                  style="width: 28px; height: 28px;">
                                                <i class="bi bi-star-fill text-warning small"></i>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="card-body p-3 p-sm-4 d-flex flex-column {{ $type === 'contractor' ? 'pt-5 mt-2' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h3 class="h5 mb-0 fw-bold text-truncate" style="max-width: 80%;">{{ $business->name }}</h3>
                                        @if($business->directoryCertifications->contains('slug', 'pwoa-certified'))
                                            <i class="bi bi-patch-check-fill text-primary" title="Certified"></i>
                                        @endif
                                    </div>

                                    <div class="small text-muted d-flex align-items-center gap-1 mb-3">
                                        <i class="bi bi-geo-alt"></i>
                                        <span class="text-truncate">{{ $business->city->name ?? 'N/A' }}, {{ $business->state->name ?? 'N/A' }}</span>
                                    </div>

                                    <div class="description-clamp small text-secondary mb-4">
                                        {!! strip_tags($business->description) !!}
                                    </div>

                                    <div class="mt-auto">
                                        <div class="d-flex gap-1 mb-4 flex-wrap">
                                            @foreach($business->categories->take(2) as $category)
                                                <span class="badge badge-soft-primary border-0 small">{{ $category->name }}</span>
                                            @endforeach
                                            @if($business->directoryCertifications->contains('slug', 'pwoa-certified'))
                                                <span class="badge badge-soft-success border-0 small">Certified</span>
                                            @endif
                                        </div>
                                        <a href="{{ route($type === 'vendor' ? 'vendors.show' : 'contractors.show', $business->slug) }}" class="btn btn-brand w-100 rounded-pill fw-bold">
                                            View Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center py-5">
                            <div class="text-muted opacity-25 mb-3">
                                <i class="bi bi-search" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="fw-bold">No results matched your filters</h4>
                            <p class="text-muted px-3">Try clearing your search or selecting a different state.</p>
                        </div>
                        @endforelse
                    </div>

                    <div class="mt-5">
                        @if($hasMore)
                            <div x-intersect="$wire.loadMore()" class="d-flex justify-content-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading more...</span>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted small">
                                <i class="bi bi-check-circle me-1"></i> You've reached the end of the directory
                            </div>
                        @endif
                    </div>
                </div> <!-- End col-lg-9 -->
            </div> <!-- End row g-4 -->
        </div>
    </section>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const initSelect2 = () => {
                $('.select2-searchable').select2({
                    theme: 'default',
                    width: '100%',
                    placeholder: function() {
                        return $(this).data('placeholder');
                    }
                });

                $('#state_select').on('change', function (e) {
                    @this.set('state_id', e.target.value);
                });

                $('#category_select').on('change', function (e) {
                    @this.set('category_id', e.target.value);
                });

                $('#membership_select').on('change', function (e) {
                    @this.set('membership_tier', e.target.value);
                });
            };

            initSelect2();

            // Re-init after Livewire updates
            document.addEventListener('livewire:navigated', initSelect2);
            
            // For filter resets or dynamic updates
            Livewire.on('filtersReset', () => {
                $('.select2-searchable').val(null).trigger('change');
            });
        });
    </script>
    <style>
        /* Select2 Responsive Fixes */
        .select2-container {
            width: 100% !important;
            max-width: 100% !important;
        }
        .select2-container--default .select2-selection--single {
            border: 0 !important;
            background-color: #f8f9fa !important;
            height: 42px !important;
            display: flex !important;
            align-items: center !important;
            border-radius: 0.5rem !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #212529 !important;
            padding-left: 12px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }
        .select2-dropdown {
            border: 1px solid #dee2e6 !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            border-radius: 0.5rem !important;
            z-index: 9999 !important;
        }
    </style>
    @endpush
</div>
