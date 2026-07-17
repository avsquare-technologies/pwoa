@extends('layouts.front')

@section('title', $vendor->name . ' - Vendor Profile')

@section('content')
<section class="vendor-profile py-5">
    <div class="container">
        <!-- Hero Section -->
        <div class="card border-0 glass-card overflow-hidden transition-all mb-4 shadow-sm">
            <div class="business-hero">
                <div class="hero-pattern"></div>
                <div class="hero-gradient"></div>
                @if($vendor->cover_photo_path)
                    <img src="{{ str_starts_with($vendor->cover_photo_path, 'http') ? $vendor->cover_photo_path : Storage::url($vendor->cover_photo_path) }}" class="w-100 h-100 object-fit-cover position-absolute top-0 start-0">
                @else
                    <div class="w-100 h-100 bg-dark position-absolute top-0 start-0 opacity-25"></div>
                @endif
                <div class="position-absolute bottom-0 start-0 p-4 d-flex align-items-end gap-4 profile-header-content">
                    <div class="business-logo-container bg-white rounded-4 p-2 shadow-lg">
                        @if($vendor->logo_path)
                            <img src="{{ str_starts_with($vendor->logo_path, 'http') ? $vendor->logo_path : Storage::url($vendor->logo_path) }}" class="w-100 h-100 object-fit-contain rounded-3">
                        @else
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light text-primary rounded-3">
                                <i class="bi bi-shop fs-1"></i>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-body p-4 pt-5 mt-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h1 class="fw-bold mb-0 display-5">{{ $vendor->name }}</h1>
                            @if($vendor->is_verified)
                                <i class="bi bi-patch-check-fill text-primary fs-3" title="Verified Vendor"></i>
                            @endif
                        </div>
                        
                        <div class="d-flex align-items-center gap-1 text-muted small mb-3">
                            <i class="bi bi-eye"></i>
                            <span>{{ number_format($vendor->views_count) }} profile views</span>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                            @if(($vendor->membership_tier ?? 'standard') === 'gold')
                                <span class="badge rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1 shadow-sm" style="background: linear-gradient(135deg, #FBBF24 0%, #D97706 100%); color: #fff;"><i class="bi bi-award-fill"></i> Gold Vendor</span>
                            @else
                                <span class="badge rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1 border" style="background: #F3F4F6; color: #4B5563;"><i class="bi bi-shield"></i> Standard Vendor</span>
                            @endif

                            @if($vendor->is_verified)
                                <span class="badge rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1 shadow-sm" style="background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%); color: #fff;"><i class="bi bi-patch-check-fill"></i> Verified Vendor</span>
                            @endif

                            @if($vendor->is_preferred)
                                <span class="badge rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1 shadow-sm" style="background: linear-gradient(135deg, #F97316 0%, #EA580C 100%); color: #fff;"><i class="bi bi-star-fill"></i> Preferred Vendor</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap w-100 w-md-auto">
                        @auth
                            @if(auth()->id() === $vendor->user_id)
                                <a href="{{ route('vendors.edit') }}" class="btn btn-warning rounded-pill px-4 fw-bold flex-grow-1 flex-md-grow-0">
                                    <i class="bi bi-pencil-square me-1"></i> Edit Profile
                                </a>
                            @endif
                        @endauth
                        @if($vendor->website)
                            <a href="{{ $vendor->website }}" target="_blank" class="btn btn-brand rounded-pill px-4 fw-bold flex-grow-1 flex-md-grow-0">Visit Website</a>
                        @endif
                        @if($vendor->email)
                            <a href="mailto:{{ $vendor->email }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold flex-grow-1 flex-md-grow-0">Contact Email</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card card-soft mb-4">
                    <div class="card-body p-4 p-lg-5">
                        @if($vendor->tagline)
                            <h4 class="fw-bold mb-4 italic text-primary">"{{ $vendor->tagline }}"</h4>
                        @endif
                        <h5 class="fw-bold mb-3 border-start border-4 border-primary ps-3">About the Vendor</h5>
                        <div class="text-secondary fs-5 lh-base mb-4">
                            {!! $vendor->description !!}
                        </div>

                        <h6 class="fw-bold text-muted small text-uppercase mb-3">Vendor Product Lines & Categories</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @forelse($vendor->categories as $category)
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-semibold">{{ $category->name }}</span>
                            @empty
                                <span class="text-muted small">No categories assigned.</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Dynamic Badges Section --}}
                @if($vendor->badges && count($vendor->badges) > 0)
                    <div class="card card-soft mb-4 border-0 shadow-sm mt-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4 border-start border-4 border-primary ps-3">Special Badges</h5>
                            <div class="row g-2 g-md-3">
                                @foreach($vendor->badges as $badge)
                                    <div class="col-12 col-md-6">
                                        <div class="p-3 bg-white rounded-4 shadow-xs border d-flex align-items-center gap-3">
                                            @if($badge->icon_path)
                                                <img src="{{ Storage::url($badge->icon_path) }}" alt="{{ $badge->name }}" style="height: 36px; width: 36px;" class="flex-shrink-0">
                                            @else
                                                <div class="bg-{{ $badge->color }}-subtle text-{{ $badge->color }} rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                    <i class="bi bi-patch-check-fill fs-5"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <span class="fw-bold text-break d-block">{{ $badge->name }}</span>
                                                @if($badge->description)
                                                    <small class="text-muted d-block">{{ $badge->description }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card card-soft sticky-top" style="top: 90px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Vendor Information</h5>
                        <div class="d-flex flex-column gap-4">
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-light rounded-circle p-3 text-primary">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </div>
                                <div>
                                    <p class="mb-0 small text-muted text-uppercase fw-bold ls-wide">Location</p>
                                    <p class="mb-0 fw-bold">{{ $vendor->city ? ($vendor->city->name . ', ' . ($vendor->state->name ?? '')) : 'Ships Nationwide' }}</p>
                                    @if($vendor->address)
                                        <small class="text-secondary">{{ $vendor->address }} {{ $vendor->zip }}</small>
                                    @endif
                                </div>
                            </div>

                            @if($vendor->phone)
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-light rounded-circle p-3 text-primary">
                                        <i class="bi bi-telephone-fill"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 small text-muted text-uppercase fw-bold ls-wide">Phone</p>
                                        <p class="mb-0 fw-bold">{{ $vendor->phone }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($vendor->email)
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-light rounded-circle p-3 text-primary">
                                        <i class="bi bi-envelope-fill"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 small text-muted text-uppercase fw-bold ls-wide">Email</p>
                                        <p class="mb-0 fw-bold text-break">{{ $vendor->email }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($vendor->vendorDetail)
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-light rounded-circle p-3 text-primary">
                                        <i class="bi bi-calendar-event-fill"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 small text-muted text-uppercase fw-bold ls-wide">Years in Business</p>
                                        <p class="mb-0 fw-bold">{{ $vendor->vendorDetail->years_in_business ?? 'N/A' }} years</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-light rounded-circle p-3 text-primary">
                                        <i class="bi bi-cart-fill"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 small text-muted text-uppercase fw-bold ls-wide">Online Ordering</p>
                                        <p class="mb-0 fw-bold">{{ $vendor->vendorDetail->has_online_ordering ? 'Yes, Available' : 'No' }}</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-light rounded-circle p-3 text-primary">
                                        <i class="bi bi-bag-check-fill"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 small text-muted text-uppercase fw-bold ls-wide">Local Pickup</p>
                                        <p class="mb-0 fw-bold">{{ $vendor->vendorDetail->has_local_pickup ? 'Yes, Available' : 'No' }}</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-light rounded-circle p-3 text-primary">
                                        <i class="bi bi-percent"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 small text-muted text-uppercase fw-bold ls-wide">PWOA Member Discounts</p>
                                        <p class="mb-0 fw-bold">{{ $vendor->vendorDetail->has_member_discounts ? 'Yes, Offered' : 'No' }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Social Media Links --}}
                        @if($vendor->facebook || $vendor->instagram || $vendor->linkedin || $vendor->youtube || $vendor->tiktok)
                            <hr class="my-4 opacity-10">
                            <div>
                                <h6 class="fw-bold text-muted small text-uppercase mb-3">Connect on Social Media</h6>
                                <div class="d-flex gap-2">
                                    @if($vendor->facebook)
                                        <a href="{{ $vendor->facebook }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-facebook"></i></a>
                                    @endif
                                    @if($vendor->instagram)
                                        <a href="{{ $vendor->instagram }}" target="_blank" class="btn btn-outline-danger btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-instagram"></i></a>
                                    @endif
                                    @if($vendor->linkedin)
                                        <a href="{{ $vendor->linkedin }}" target="_blank" class="btn btn-outline-info btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-linkedin"></i></a>
                                    @endif
                                    @if($vendor->youtube)
                                        <a href="{{ $vendor->youtube }}" target="_blank" class="btn btn-outline-danger btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-youtube"></i></a>
                                    @endif
                                    @if($vendor->tiktok)
                                        <a href="{{ $vendor->tiktok }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-tiktok"></i></a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <hr class="my-4 opacity-10">

                        <div class="text-center">
                            <p class="small text-muted mb-0">Member since {{ $vendor->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
