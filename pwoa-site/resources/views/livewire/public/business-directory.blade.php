<x-slot name="header">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0 text-dark fw-bold">
            Business Directory
        </h2>
        <div class="d-flex align-items-center gap-3">
            {{-- SEARCH BOX MOVED TO SIDEBAR FILTER --}}
            {{-- <div class="search-box position-relative" style="width: 300px;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0 shadow-sm"><i
                            class="bi bi-search text-muted"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        class="form-control border-0 shadow-sm py-2" placeholder="Search businesses...">
                </div>
                <div wire:loading wire:target="search" class="position-absolute top-50 end-0 translate-middle-y pe-3">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                </div>
            </div> --}}
            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 d-none d-md-inline-block">
                {{ $businesses->total() }} Verified Partners
            </span>
        </div>
    </div>
</x-slot>

<div class="row g-4">
    <!-- Filters Sidebar -->
    <div class="col-lg-3">
        <div class="card border-0 glass-card sticky-top" style="top: 100px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Search & Filter</h5>
                    <div wire:loading wire:target="search, country_id, state_id, city_id, type, category"
                        class="spinner-border spinner-border-sm text-primary" role="status"></div>
                </div>

                <!-- Search -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">Company Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search py-1"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="form-control bg-light border-0" placeholder="Search businesses...">
                    </div>
                </div>

                <!-- Location Filters -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">Location</label>
                    <div class="d-flex flex-column gap-3">
                        <select wire:model.live="country_id"
                            class="form-select form-select-sm bg-light border-0 rounded-3">
                            <option value="">All Countries</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>

                        <select wire:model.live="state_id"
                            class="form-select form-select-sm bg-light border-0 rounded-3" {{ empty($country_id) ? 'disabled' : '' }}>
                            <option value="">All States</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>

                        <select wire:model.live="city_id" class="form-select form-select-sm bg-light border-0 rounded-3"
                            {{ empty($state_id) ? 'disabled' : '' }}>
                            <option value="">All Cities</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Type -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">Service Type</label>
                    <div class="d-flex flex-column gap-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model.live="type" value="" id="type_all">
                            <label class="form-check-label small" for="type_all">All Entities</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model.live="type" value="contractor"
                                id="type_contractor">
                            <label class="form-check-label small" for="type_contractor">Contractors</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model.live="type" value="vendor"
                                id="type_vendor">
                            <label class="form-check-label small" for="type_vendor">Vendors</label>
                        </div>
                    </div>
                </div>

                <!-- Categories -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">Category</label>
                    <div class="d-flex flex-column gap-2" style="max-height: 300px; overflow-y: auto;">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model.live="category" value=""
                                id="cat_all">
                            <label class="form-check-label small" for="cat_all">All Categories</label>
                        </div>
                        @foreach($categories as $cat)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" wire:model.live="category"
                                    value="{{ $cat->id }}" id="cat_{{ $cat->id }}">
                                <label class="form-check-label small" for="cat_{{ $cat->id }}">{{ $cat->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Featured Status -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">
                        Featured Status
                    </label>

                    <select 
                        wire:model.live="featured"
                        class="form-select form-select-sm bg-light border-0 rounded-3">

                        <option value="">All</option>
                        <option value="1">Featured</option>
                        <option value="0">Non-Featured</option>

                    </select>
                </div>

                <div class="d-grid gap-2 pt-2 border-top">
                    <button
                        wire:click="$set('search', ''); $set('country_id', ''); $set('state_id', ''); $set('city_id', ''); $set('type', ''); $set('category', ''); $set('featured', '');"
                        class="btn btn-outline-secondary btn-sm rounded-3">
                        Reset Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Results Area -->
    <div class="col-lg-9 position-relative">
        @if(!$isSubscribed)
            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center z-3"
                style="background: rgba(255,255,255,0.2); backdrop-filter: blur(4px);">
                <div class="card border-0 shadow-lg text-center p-5 max-w-md mx-auto" style="max-width: 450px;">
                    <div class="mb-4">
                        <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                            style="width: 80px; height: 80px;">
                            <i class="bi bi-lock-fill display-5"></i>
                        </div>
                        <h3 class="fw-bold">Exclusive Directory Access</h3>
                        <p class="text-muted">Our full business directory and member contact information are exclusive to
                            active PWOA members. Join our community to connect with hundreds of verified partners.</p>
                    </div>
                    <div class="d-grid gap-3">
                        <a href="{{ route('membership.index') }}"
                            class="btn btn-primary btn-lg rounded-pill fw-bold px-5 py-3 shadow-sm">
                            Become a Member Now
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-link text-decoration-none">Already a member? Sign
                            in</a>
                    </div>
                </div>
            </div>
        @endif

        <div class="row g-4 @if(!$isSubscribed) pe-none user-select-none @endif"
            style="@if(!$isSubscribed) filter: blur(8px); opacity: 0.6; @endif"
            wire:loading.class="opacity-50 transition-all">
            @forelse($businesses as $business)
                <div class="col-md-6 col-lg-4" wire:key="{{ $business->id }}">
                    <div class="card border-0 glass-card h-100 hover-scale overflow-hidden shadow-sm">
                        <div class="business-card-header position-relative" style="height: 140px; background: #f8fafc;">
                            @if($business->logo_path)
                                <img src="{{ \Illuminate\Support\Str::startsWith($business->logo_path, ['http://', 'https://']) ? $business->logo_path : Storage::url($business->logo_path) }}"
                                    class="h-100 object-fit-contain rounded">
                            @else
                                <div class="bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm"
                                    style="width: 50px; height: 50px;">
                                    <i class="bi bi-building fs-4"></i>
                                </div>
                            @endif
                            <div class="position-absolute top-0 end-0 p-3 d-flex flex-column align-items-end gap-1">
                                <span class="badge bg-white text-dark shadow-sm rounded-pill small px-2 py-1">
                                    {{ ucfirst($business->membership_tier ?? ucfirst($business->type ?? 'Standard')) }}
                                </span>
                                @if($business->featured)
                                    <span class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                          style="width: 28px; height: 28px;">
                                        <i class="bi bi-star-fill text-warning small"></i>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-1 text-truncate">{{ $business->name }}</h5>
                            <div class="d-flex align-items-center gap-2 text-muted small mb-3">
                                <i class="bi bi-geo-alt"></i>
                                {{ $business->city->name ?? 'Unknown City' }},
                                {{ $business->state->name ?? 'N/A' }}
                            </div>

                            <p class="text-muted small mb-4 line-clamp-2">
                                {!! $business->description !!}
                            </p>

                            <div class="d-flex gap-1 mb-4 flex-wrap">
                                @if($business->categories->isNotEmpty())
                                    <span
                                        class="badge bg-primary-subtle text-primary border-0 small d-flex align-items-center gap-1">
                                        {{ $business->categories->first()->name }}
                                    </span>
                                @endif
                                <span class="badge bg-success-subtle text-success border-0 small">Verified</span>
                            </div>

                            <a href="{{ route('business.profile', $business->slug) }}"
                                class="btn btn-primary w-100 rounded-pill fw-bold">
                                View Profile
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="text-muted opacity-25 mb-4">
                        <i class="bi bi-search" style="font-size: 5rem;"></i>
                    </div>
                    <h4 class="fw-bold">No results found</h4>
                    <p class="text-muted mb-0">Try different criteria or expanding your area.</p>
                </div>
            @endforelse
        </div>

        @if($isSubscribed)
            <div class="mt-5">
                {{ $businesses->links() }}
            </div>
        @endif
    </div>
</div>