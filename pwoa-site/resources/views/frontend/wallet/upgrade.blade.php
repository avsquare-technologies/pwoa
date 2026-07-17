<x-app-layout>
    {{-- 
        Dashboard-Integrated Upgrade Page
        This view is designed to match the 'User Dashboard' layout and aesthetic.
        It uses the dashboard's grid system, glass-cards, and typography tokens.
    --}}

    <x-slot name="header">
        <div class="d-flex align-items-center">
            <h2 class="h4 mb-0 text-dark fw-bold">
                <i class="bi bi-lock-fill text-muted me-2"></i> Access Restricted
            </h2>
        </div>
    </x-slot>

    <div class="row g-4">
        <!-- Main Upgrade Hero Banner -->
        <div class="col-12">
            <div class="card border-0 glass-card mb-2 overflow-hidden shadow-sm"
                style="background: linear-gradient(105deg, #ffffff 0%, #f7faff 100%);">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center">
                        <div class="col-lg-7">
                            <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2 mb-3 fw-bold">
                                <i class="bi bi-shield-lock-fill me-1"></i> Premium Access Required
                            </span>
                            <h1 class="display-5 fw-bold mb-3 ls-tight">Unlock Your Potential</h1>
                            <p class="lead text-muted mb-4">You've reached a premium section of the PWOA ecosystem. To ensure community quality and engagement, access to this area requires a minimum $WASH token stake.</p>
                            
                            <div class="alert bg-white border border-warning border-opacity-25 rounded-4 p-4 mb-4 shadow-sm hvr-grow">
                                <div class="row align-items-center">
                                    <div class="col-md-7 border-end-md">
                                        <div class="d-flex align-items-center gap-4">
                                            <div class="bg-warning bg-opacity-10 p-3 rounded-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                                <i class="bi bi-coin fs-1 text-warning"></i>
                                            </div>
                                            <div>
                                                <h5 class="fw-bold mb-1">2,000 $WASH Tokens</h5>
                                                <p class="small text-muted mb-0">Minimum balance required for full ecosystem access.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5 mt-3 mt-md-0 ps-md-4">
                                        <div class="small text-uppercase fw-bold text-muted ls-wide mb-1" style="font-size: 0.65rem;">Your Current Balance</div>
                                        <div class="h3 fw-bold {{ ($currentBalance ?? 0) < 2000 ? 'text-danger' : 'text-success' }} mb-0">
                                            {{ number_format($currentBalance ?? 0, 0) }} $WASH
                                        </div>
                                        @if(($currentBalance ?? 0) < 2000)
                                            <div class="small text-muted mt-1 fw-medium">
                                                <i class="bi bi-info-circle me-1"></i> Need {{ number_format(2000 - ($currentBalance ?? 0), 0) }} more
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="d-flex flex-wrap gap-3 mt-4">
                                <a href="{{ route('token.purchase') }}" class="btn btn-primary btn-lg shadow-sm px-5 py-3 rounded-4 fw-bold d-flex align-items-center gap-3">
                                    <i class="bi bi-cart-plus-fill fs-4"></i> Purchase $WASH
                                </a>
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg px-4 py-3 rounded-4 border-2">
                                    Return to Dashboard
                                </a>
                            </div>
                        </div>

                        <!-- Hero Decorative Icon -->
                        <div class="col-lg-5 d-none d-lg-block text-end">
                            <div class="position-relative d-inline-block">
                                <div class="bg-warning rounded-4 opacity-10 position-absolute"
                                    style="top: -25px; right: -25px; width: 120%; height: 120%; transform: rotate(8deg);">
                                </div>
                                <i class="bi bi-award display-1 text-warning position-relative" style="font-size: 10rem; opacity: 0.9;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Premium Feature Highlights -->
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm glass-card h-100 p-4 transition-all">
                <div class="bg-primary bg-opacity-10 rounded-4 p-3 d-inline-block mb-4">
                    <i class="bi bi-mortarboard-fill text-primary fs-3"></i>
                </div>
                <h5 class="fw-bold mb-3">Elite Education</h5>
                <p class="text-muted small mb-0">Unlock all professional certification tracks, interactive courses, and industry-recognized credentials within our Learning Center.</p>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm glass-card h-100 p-4 transition-all">
                <div class="bg-success bg-opacity-10 rounded-4 p-3 d-inline-block mb-4">
                    <i class="bi bi-calendar-event-fill text-success fs-3"></i>
                </div>
                <h5 class="fw-bold mb-3">Community Events</h5>
                <p class="text-muted small mb-0">Join exclusive collabathons, live webinars, and local meetups reserved for our most committed ecosystem members.</p>
            </div>
        </div>

        <div class="col-xl-4 col-md-12">
            <div class="card border-0 shadow-sm glass-card h-100 p-4 transition-all">
                <div class="bg-info bg-opacity-10 rounded-4 p-3 d-inline-block mb-4">
                    <i class="bi bi-shield-check text-info fs-3"></i>
                </div>
                <h5 class="fw-bold mb-3">Verified Status</h5>
                <p class="text-muted small mb-0">Display your commitment with a verified badge on your business profile, increasing trust and visibility in the directory.</p>
            </div>
        </div>
    </div>

    <style>
        .transition-all {
            transition: all 0.3s ease;
        }
        .transition-all:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
        }
        .hvr-grow {
            transition: transform 0.2s ease;
        }
        .hvr-grow:hover {
            transform: scale(1.015);
        }
    </style>
</x-app-layout>
