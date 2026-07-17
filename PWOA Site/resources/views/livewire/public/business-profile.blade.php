<div>
    <x-slot name="header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('directory') }}" class="text-decoration-none">Directory</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $business->name }}</li>
            </ol>
        </nav>
    </x-slot>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Hero Section -->
            <div class="card border-0 glass-card overflow-hidden transition-all mb-4">
                <div class="business-hero position-relative">
                    <div class="hero-pattern"></div>
                    <div class="hero-gradient"></div>
                    @if($business->cover_photo_path)
                        <img src="{{ str_starts_with($business->cover_photo_path, 'http') ? $business->cover_photo_path : Storage::url($business->cover_photo_path) }}" class="w-100 h-100 object-fit-cover position-absolute top-0 start-0">
                    @endif
                    <div class="position-absolute bottom-0 start-0 p-4 d-flex align-items-end gap-4 profile-header-content">
                        <div class="business-logo-container bg-white rounded-4 p-2 shadow-lg">
                            @if($business->logo_path)
                                <img src="{{ str_starts_with($business->logo_path, 'http') ? $business->logo_path : Storage::url($business->logo_path) }}" class="w-100 h-100 object-fit-contain rounded-3">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light text-primary rounded-3">
                                    <i class="bi bi-building-fill fs-1"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4 pt-5 mt-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h1 class="fw-bold mb-0 display-5">{{ $business->name }}</h1>
                                @if($business->verified_at)
                                    <i class="bi bi-patch-check-fill text-primary fs-3" title="Verified Business"></i>
                                @endif
                            </div>
                            <p class="text-muted fs-5 mb-0 d-flex align-items-center gap-2">
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-semibold">
                                    {{ $business->categories->first()->name ?? 'Premium Member' }}
                                </span>
                                <span class="opacity-50">|</span>
                                <span>{{ ucfirst($business->type) }} Entity</span>
                            </p>
                        </div>
                    </div>

                    <div class="row g-3 mb-5">
                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 rounded-4 bg-light d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-geo-alt-fill fs-5"></i>
                                </div>
                                <div>
                                    <p class="mb-0 small text-muted">Location</p>
                                    <p class="mb-0 fw-bold">{{ $business->city->name ?? 'Global' }}, {{ $business->state->name ?? 'Remote' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 rounded-4 bg-light d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success rounded-circle p-2" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-calendar-check-fill fs-5"></i>
                                </div>
                                <div>
                                    <p class="mb-0 small text-muted">Member Since</p>
                                    <p class="mb-0 fw-bold">{{ $business->created_at->format('M Y') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <div class="p-3 rounded-4 bg-light d-flex align-items-center gap-3">
                                <div class="bg-info bg-opacity-10 text-info rounded-circle p-2" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-shield-check fs-5"></i>
                                </div>
                                <div>
                                    <p class="mb-0 small text-muted">Status</p>
                                    <p class="mb-0 fw-bold">Active Profile</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3 border-start border-4 border-primary ps-3">Company Overview</h5>
                        <div class="text-muted fs-5 lh-base mb-0">
                            {!! $business->description !!}
                        </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 100px;">
                <!-- Contact Card -->
                <div class="card border-0 glass-card mb-4 overflow-hidden">
                    <div class="card-header bg-dark p-4 text-white">
                        <h5 class="fw-bold mb-0">Contact Details</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-grid gap-3 mb-4">
                            @if($business->email)
                                <a href="mailto:{{ $business->email }}" class="btn btn-primary py-3 rounded-4 shadow-sm fw-bold hvr-grow">
                                    <i class="bi bi-envelope-at-fill me-2"></i> Send Message
                                </a>
                            @endif
                            
                            @if($business->website)
                                <a href="{{ $business->website }}" target="_blank" class="btn btn-outline-dark py-3 rounded-4 fw-bold hvr-grow">
                                    <i class="bi bi-globe2 me-2"></i> Official Website
                                </a>
                            @endif
                        </div>

                        <div class="contact-info-list">
                            <div class="d-flex align-items-start gap-3 mb-4">
                                <div class="contact-icon bg-light rounded-circle text-primary shadow-sm">
                                    <i class="bi bi-telephone-fill"></i>
                                </div>
                                <div>
                                    <p class="mb-0 small text-muted">Phone Number</p>
                                    <p class="mb-0 fw-bold">{{ $business->phone ?? 'Not provided' }}</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3">
                                <div class="contact-icon bg-light rounded-circle text-primary shadow-sm">
                                    <i class="bi bi-pin-map-fill"></i>
                                </div>
                                <div>
                                    <p class="mb-0 small text-muted">Headquarters</p>
                                    <p class="mb-0 fw-bold">
                                        {{ $business->address }}<br>
                                        {{ $business->city->name ?? '' }}, {{ $business->state->name ?? '' }} {{ $business->zip }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Verification Banner -->
                <div class="card border-0 bg-primary text-white p-4 rounded-4 shadow-lg overflow-hidden position-relative">
                    <div class="verification-bg-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <div class="position-relative z-1">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-patch-check-fill"></i>
                            <h6 class="fw-bold mb-0">Verified Business</h6>
                        </div>
                        <p class="small mb-0 opacity-75">This entity has undergone a thorough background check by our compliance team.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
