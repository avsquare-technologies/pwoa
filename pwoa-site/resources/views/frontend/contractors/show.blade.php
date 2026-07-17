@extends('layouts.front')

@section('title', $contractor->name . ' - PWOA Contractor')

@section('content')
<section class="py-4 py-lg-5 bg-light overflow-hidden">
    <div class="container">
        {{-- Profile Header Card --}}
        <div class="card border-0 glass-card overflow-hidden transition-all mb-4 shadow-sm w-100">
            <div class="business-hero">
                <div class="hero-pattern"></div>
                <div class="hero-gradient"></div>
                @if($contractor->cover_photo_path)
                    <img src="{{ str_starts_with($contractor->cover_photo_path, 'http') ? $contractor->cover_photo_path : Storage::url($contractor->cover_photo_path) }}" class="w-100 h-100 object-fit-cover position-absolute top-0 start-0">
                @else
                    <div class="w-100 h-100 bg-dark position-absolute top-0 start-0 opacity-25"></div>
                @endif
                <div class="position-absolute bottom-0 start-0 p-3 p-md-4 d-flex align-items-end gap-3 gap-md-4 profile-header-content">
                    <div class="business-logo-container bg-white rounded-4 p-2 shadow-lg">
                        @if($contractor->logo_path)
                            <img src="{{ str_starts_with($contractor->logo_path, 'http') ? $contractor->logo_path : Storage::url($contractor->logo_path) }}" class="w-100 h-100 object-fit-contain rounded-3">
                        @else
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light text-primary rounded-3">
                                <i class="bi bi-person-workspace fs-1"></i>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-body p-3 p-md-4 pt-5 mt-2 mt-md-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                    <div class="w-100">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                            <h1 class="fw-bold mb-0 display-5 text-break">{{ $contractor->name }}</h1>
                            @if($contractor->is_verified)
                                <i class="bi bi-patch-check-fill text-primary fs-3" title="Verified Contractor"></i>
                            @endif
                        </div>
                        
                        <div class="d-flex align-items-center gap-1 text-muted small mb-2">
                            <i class="bi bi-eye"></i>
                            <span>{{ number_format($contractor->views_count) }} profile views</span>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                            @if(($contractor->membership_tier ?? 'standard') === 'gold')
                                <span class="badge rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1 shadow-sm" style="background: linear-gradient(135deg, #FBBF24 0%, #D97706 100%); color: #fff;"><i class="bi bi-award-fill"></i> Gold Member</span>
                            @else
                                <span class="badge rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1 border" style="background: #F3F4F6; color: #4B5563;"><i class="bi bi-shield"></i> Standard Member</span>
                            @endif

                            @if($contractor->is_verified)
                                <span class="badge rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1 shadow-sm" style="background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%); color: #fff;"><i class="bi bi-patch-check-fill"></i> Verified Contractor</span>
                            @endif

                            @if($contractor->directoryCertifications->contains('slug', 'pwoa-certified'))
                                <span class="badge rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1 shadow-sm" style="background: linear-gradient(135deg, #10B981 0%, #047857 100%); color: #fff;"><i class="bi bi-shield-check"></i> PWOA Certified</span>
                            @endif

                            @if($contractor->directoryCertifications->contains('slug', 'eco-certified'))
                                <span class="badge rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1 shadow-sm" style="background: linear-gradient(135deg, #06B6D4 0%, #0891B2 100%); color: #fff;"><i class="bi bi-tree-fill"></i> ECO Certified</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
                        @auth
                            @if(auth()->id() === $contractor->user_id)
                                <a href="{{ route('contractors.edit') }}" class="btn btn-warning rounded-pill px-4 fw-bold flex-grow-1 flex-md-grow-0">
                                    <i class="bi bi-pencil-square me-1"></i> Edit Profile
                                </a>
                            @endif
                        @endauth
                        @if($contractor->website)
                            <a href="{{ $contractor->website }}" target="_blank" class="btn btn-primary rounded-pill px-4 fw-bold flex-grow-1 flex-md-grow-0">Official Website</a>
                        @endif
                        @if($contractor->email)
                            <a href="mailto:{{ $contractor->email }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold flex-grow-1 flex-md-grow-0">Send Email</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 align-items-start">
            <div class="col-12 col-lg-8">
                <div class="card card-soft mb-4 w-100 border-0 shadow-sm">
                    <div class="card-body p-3 p-md-4 p-lg-5">
                        @if($contractor->tagline)
                            <h4 class="fw-bold mb-4 italic text-primary">"{{ $contractor->tagline }}"</h4>
                        @endif
                        <h5 class="fw-bold mb-3 border-start border-4 border-primary ps-3">Business Profile</h5>
                        <div class="text-secondary fs-5 lh-base mb-4 text-break">
                            {!! $contractor->description !!}
                        </div>

                        <h6 class="fw-bold text-muted small text-uppercase mb-3">Service Categories</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @forelse($contractor->categories as $category)
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-semibold">{{ $category->name }}</span>
                            @empty
                                <span class="text-muted small">No categories assigned.</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Certifications Badges Section --}}
                @if($contractor->directoryCertifications && count($contractor->directoryCertifications) > 0)
                    <div class="card card-soft mb-4 w-100 border-0 shadow-sm">
                        <div class="card-body p-3 p-md-4">
                            <h5 class="fw-bold mb-4 border-start border-4 border-primary ps-3">Awarded Certifications</h5>
                            <div class="row g-2 g-md-3">
                                @foreach($contractor->directoryCertifications as $cert)
                                    <div class="col-12 col-md-6">
                                        <div class="p-3 bg-white rounded-4 shadow-xs border d-flex align-items-center gap-3">
                                            @if($cert->badge_icon_path)
                                                <img src="{{ Storage::url($cert->badge_icon_path) }}" alt="{{ $cert->name }}" style="height: 36px; width: 36px;" class="flex-shrink-0">
                                            @else
                                                <div class="bg-primary-subtle text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                    <i class="bi bi-award-fill"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <span class="fw-bold text-break d-block">{{ $cert->name }}</span>
                                                @if($cert->pivot->certificate_number)
                                                    <small class="text-muted d-block">Lic #: {{ $cert->pivot->certificate_number }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Dynamic Badges Section --}}
                @if($contractor->badges && count($contractor->badges) > 0)
                    <div class="card card-soft mb-4 w-100 border-0 shadow-sm">
                        <div class="card-body p-3 p-md-4">
                            <h5 class="fw-bold mb-4 border-start border-4 border-primary ps-3">Special Badges</h5>
                            <div class="row g-2 g-md-3">
                                @foreach($contractor->badges as $badge)
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

                {{-- Equipment Fleet Section --}}
                @if($contractor->directoryEquipments && count($contractor->directoryEquipments) > 0)
                    <div class="card card-soft mb-4 w-100 border-0 shadow-sm">
                        <div class="card-body p-3 p-md-4">
                            <h5 class="fw-bold mb-4 border-start border-4 border-primary ps-3">Equipment fleet</h5>
                            <div class="row g-2 g-md-3">
                                @foreach($contractor->directoryEquipments as $equip)
                                    <div class="col-12 col-md-6">
                                        <div class="p-3 bg-light rounded-4 d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-3">
                                                @if($equip->icon_path)
                                                    <img src="{{ Storage::url($equip->icon_path) }}" alt="{{ $equip->name }}" style="height: 24px; width: 24px;" class="flex-shrink-0">
                                                @else
                                                    <i class="bi bi-tools text-primary fs-4 flex-shrink-0"></i>
                                                @endif
                                                <div>
                                                    <span class="fw-semibold text-break d-block">{{ $equip->name }}</span>
                                                    @if($equip->pivot->specifications)
                                                        <small class="text-muted text-break d-block">{{ $equip->pivot->specifications }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-secondary rounded-pill">Qty: {{ $equip->pivot->quantity }}</span>
                                                @if($equip->pivot->is_verified)
                                                    <span class="badge bg-success rounded-pill"><i class="bi bi-patch-check-fill"></i> Verified</span>
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

            <div class="col-12 col-lg-4">
                <div class="card card-soft sticky-lg-top w-100 border-0 shadow-sm mb-4" style="top: 90px;">
                    <div class="card-body p-3 p-md-4">
                        <h5 class="fw-bold mb-4">Contractor Info</h5>
                        <div class="d-flex flex-column gap-3 gap-md-4">
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-light rounded-circle p-3 text-primary flex-shrink-0">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </div>
                                <div class="text-break">
                                    <p class="mb-0 small text-muted text-uppercase fw-bold ls-wide">Headquarters</p>
                                    <p class="mb-0 fw-bold">{{ $contractor->city->name ?? 'N/A' }}, {{ $contractor->state->name ?? 'N/A' }}</p>
                                    @if($contractor->address)
                                        <small class="text-secondary">{{ $contractor->address }} {{ $contractor->zip }}</small>
                                    @endif
                                </div>
                            </div>

                            @if($contractor->phone)
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-light rounded-circle p-3 text-primary flex-shrink-0">
                                        <i class="bi bi-telephone-fill"></i>
                                    </div>
                                    <div class="text-break">
                                        <p class="mb-0 small text-muted text-uppercase fw-bold ls-wide">Direct Line</p>
                                        <p class="mb-0 fw-bold">{{ $contractor->phone }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($contractor->email)
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-light rounded-circle p-3 text-primary flex-shrink-0">
                                        <i class="bi bi-envelope-fill"></i>
                                    </div>
                                    <div class="text-break">
                                        <p class="mb-0 small text-muted text-uppercase fw-bold ls-wide">Public Email</p>
                                        <p class="mb-0 fw-bold text-break">{{ $contractor->email }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-light rounded-circle p-3 text-primary flex-shrink-0">
                                    <i class="bi bi-star-fill"></i>
                                </div>
                                <div class="text-break">
                                    <p class="mb-0 small text-muted text-uppercase fw-bold ls-wide">Customer Rating</p>
                                    <p class="mb-0 fw-bold">{{ number_format($contractor->avg_rating ?? 5.0, 1) }} / 5.0</p>
                                </div>
                            </div>

                            @if($contractor->contractorDetail)
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-light rounded-circle p-3 text-primary flex-shrink-0">
                                        <i class="bi bi-calendar-event-fill"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 small text-muted text-uppercase fw-bold ls-wide">Years in Business</p>
                                        <p class="mb-0 fw-bold">{{ $contractor->contractorDetail->years_in_business ?? 'N/A' }} years</p>
                                    </div>
                                </div>

                                @if($contractor->contractorDetail->license_number)
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="bg-light rounded-circle p-3 text-primary flex-shrink-0">
                                            <i class="bi bi-card-text"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 small text-muted text-uppercase fw-bold ls-wide">License Number</p>
                                            <p class="mb-0 fw-bold">{{ $contractor->contractorDetail->license_number }}</p>
                                        </div>
                                    </div>
                                @endif

                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-light rounded-circle p-3 text-primary flex-shrink-0">
                                        <i class="bi bi-shield-fill-check"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 small text-muted text-uppercase fw-bold ls-wide">Fully Insured</p>
                                        <p class="mb-0 fw-bold">{{ $contractor->contractorDetail->is_insured ? 'Yes' : 'No' }}</p>
                                    </div>
                                </div>

                                @if($contractor->contractorDetail->serviceRadius)
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="bg-light rounded-circle p-3 text-primary flex-shrink-0">
                                            <i class="bi bi-geo-fill"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 small text-muted text-uppercase fw-bold ls-wide">Service Radius</p>
                                            <p class="mb-0 fw-bold">{{ $contractor->contractorDetail->serviceRadius->name }}</p>
                                        </div>
                                    </div>
                                @endif

                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-light rounded-circle p-3 text-primary flex-shrink-0">
                                        <i class="bi bi-lightning-charge-fill"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 small text-muted text-uppercase fw-bold ls-wide">Emergency 24/7 Service</p>
                                        <p class="mb-0 fw-bold">{{ $contractor->contractorDetail->is_emergency_service ? 'Yes' : 'No' }}</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-light rounded-circle p-3 text-primary flex-shrink-0">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 small text-muted text-uppercase fw-bold ls-wide">Available for Subcontracting</p>
                                        <p class="mb-0 fw-bold">{{ $contractor->contractorDetail->is_subcontracting ? 'Yes' : 'No' }}</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-light rounded-circle p-3 text-primary flex-shrink-0">
                                        <i class="bi bi-globe"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 small text-muted text-uppercase fw-bold ls-wide">National Accounts</p>
                                        <p class="mb-0 fw-bold">{{ $contractor->contractorDetail->is_national_accounts ? 'Yes' : 'No' }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Social Media Links --}}
                        @if($contractor->facebook || $contractor->instagram || $contractor->linkedin || $contractor->youtube || $contractor->tiktok)
                            <hr class="my-4 opacity-10">
                            <div>
                                <h6 class="fw-bold text-muted small text-uppercase mb-3">Connect on Social Media</h6>
                                <div class="d-flex gap-2">
                                    @if($contractor->facebook)
                                        <a href="{{ $contractor->facebook }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-facebook"></i></a>
                                    @endif
                                    @if($contractor->instagram)
                                        <a href="{{ $contractor->instagram }}" target="_blank" class="btn btn-outline-danger btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-instagram"></i></a>
                                    @endif
                                    @if($contractor->linkedin)
                                        <a href="{{ $contractor->linkedin }}" target="_blank" class="btn btn-outline-info btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-linkedin"></i></a>
                                    @endif
                                    @if($contractor->youtube)
                                        <a href="{{ $contractor->youtube }}" target="_blank" class="btn btn-outline-danger btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-youtube"></i></a>
                                    @endif
                                    @if($contractor->tiktok)
                                        <a href="{{ $contractor->tiktok }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-tiktok"></i></a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <hr class="my-4 opacity-10">

                        <div class="text-center">
                            <p class="small text-muted mb-0">Member since {{ $contractor->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
