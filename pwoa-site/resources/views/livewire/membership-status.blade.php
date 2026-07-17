@inject('balanceService', 'App\Services\WashBalanceService')
@php
    $user = auth()->user();
    $balance = $user ? ($balanceService->getBalance($user) ?? 0) : 0;
    $hasGoldCard = $balance >= 2000;
@endphp

<x-slot name="header">
    <div class="d-flex align-items-center justify-content-between">
        <h2 class="h4 mb-0 text-dark fw-bold">
            Membership & Billing
        </h2>
        @if($membership->is_active)
            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 fw-bold">
                <i class="bi bi-shield-check me-1"></i> Active Member
            </span>
        @endif
    </div>
</x-slot>

<div class="row g-4">
    <div class="col-lg-8">
        @if($hasGoldCard)
            <!-- $WASH Token Gold Card Royal Edition -->
            <div class="gold-card-container mb-4 overflow-hidden">
                <div class="gold-card-bg-glow"></div>
                <div class="card-body p-4 p-md-5 gold-card-wrapper">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-5">
                            <div class="gold-card-mockup">
                                <div class="gold-card-mockup-watermark">
                                    <i class="bi bi-shield-shaded"></i>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="gold-card-chip"></div>
                                    <div class="gold-card-logo-text">GOLD ELITE</div>
                                </div>
                                <div>
                                    <div class="gold-card-number">•••• •••• •••• 2000</div>
                                    <div class="d-flex justify-content-between align-items-end">
                                        <div>
                                            <div class="gold-card-label">Card Holder</div>
                                            <div class="gold-card-holder-name text-truncate" style="max-width: 140px;">{{ $user->name }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="gold-card-label">WASH Balance</div>
                                            <div class="gold-card-balance-val">{{ number_format($balance) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 ps-md-4">
                            <div class="mb-4">
                                <span class="badge badge-gold-royal text-uppercase tracking-wider mb-3">
                                    <i class="bi bi-award-fill me-1"></i> Membership Tier
                                </span>
                                <h3 class="text-white fw-bold mb-2">
                                    PWOA <span class="text-gold-gradient">Gold Member</span>
                                </h3>
                                <p class="text-white small mb-0">Premium Web3 privileges unlocked via your active $WASH token holding status.</p>
                            </div>
                            
                            <div class="gold-benefits-compact">
                                <div class="gold-benefit-item-compact">
                                    <div class="gold-benefit-checkmark-wrapper">
                                        <i class="bi bi-check-lg gold-benefit-checkmark"></i>
                                    </div>
                                    <span class="gold-benefit-text-compact">Full Education Access</span>
                                </div>
                                <div class="gold-benefit-item-compact">
                                    <div class="gold-benefit-checkmark-wrapper">
                                        <i class="bi bi-check-lg gold-benefit-checkmark"></i>
                                    </div>
                                    <span class="gold-benefit-text-compact">Exclusive Events</span>
                                </div>
                                <div class="gold-benefit-item-compact">
                                    <span class="gold-benefit-checkmark-wrapper">
                                        <i class="bi bi-check-lg gold-benefit-checkmark"></i>
                                    </span>
                                    <span class="gold-benefit-text-compact">Verified Gold Badge</span>
                                </div>
                                <div class="gold-benefit-item-compact">
                                    <span class="gold-benefit-checkmark-wrapper">
                                        <i class="bi bi-check-lg gold-benefit-checkmark"></i>
                                    </span>
                                    <span class="gold-benefit-text-compact">Priority Support</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Status Card -->
        <div class="card border-0 glass-card mb-4 overflow-hidden">
            <div class="card-body p-4 p-md-5">
                <div class="section-title mb-5">
                    <h5 class="fw-bold mb-1 text-dark">Subscription Overview</h5>
                    <p class="text-muted small">Manage your billing and plan details.</p>
                    <hr class="mt-2 opacity-10">
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-4 bg-light rounded-4 border">
                            <p class="text-muted small text-uppercase fw-bold mb-2 ls-wide">Current Plan</p>
                            <h3 class="fw-bold mb-0 text-dark">
                                @if($membership->is_active)
                                    {{ config('membership.plans.' . $membership->plan . '.name', 'Standard Membership') }}
                                @else
                                    No Active Membership
                                @endif
                            </h3>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-4 bg-light rounded-4 border">
                            <p class="text-muted small text-uppercase fw-bold mb-2 ls-wide">Billing Amount</p>
                            <h3 class="fw-bold mb-0 text-dark">
                                @if($membership->is_active)
                                    ${{ number_format(config('membership.plans.' . $membership->plan . '.price', 99), 2) }} <span class="fs-6 text-muted fw-normal">/ year</span>
                                @else
                                    $0.00 <span class="fs-6 text-muted fw-normal">/ year</span>
                                @endif
                            </h3>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-4 p-4 rounded-4 bg-primary-subtle border border-primary-subtle">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px; min-width: 50px;">
                                <i class="bi bi-calendar-check fs-4"></i>
                            </div>
                            <div>
                                <p class="mb-0 text-muted small fw-bold text-uppercase">Next Billing Date</p>
                                <p class="mb-0 fw-bold fs-5 text-dark">
                                    @if($membership->is_active)
                                        {{ $membership->expires_at?->format('M d, Y') ?? 'N/A' }}
                                    @else
                                        Subscription Inactive
                                    @endif
                                </p>
                            </div>
                            @if(!$membership->cancelled_at)
                                <div class="ms-auto">
                                    <span class="badge bg-white text-primary rounded-pill px-3 py-2 border shadow-sm">Auto-renew ON</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-top d-flex flex-wrap gap-3">
                    @if($membership->is_active)
                        <a href="{{ route('billing.portal') }}" class="btn btn-primary px-4 py-3 rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-credit-card-2-front me-2"></i> Manage Billing & Cards
                        </a>

                        {{--
                        <a href="{{ route('membership.upgrade') }}" class="btn btn-outline-primary px-4 py-3 rounded-pill fw-bold">
                            <i class="bi bi-arrow-down-up me-2"></i> Switch Plan/Tier
                        </a>
                        --}}

                        @if($membership->cancelled_at)
                            <button wire:click="resume" wire:loading.attr="disabled" class="btn btn-success px-4 py-3 rounded-pill fw-bold shadow-sm">
                                <span wire:loading wire:target="resume" class="loading-spinner"></span>
                                <span wire:loading.remove wire:target="resume">Resume Subscription</span>
                            </button>
                        @else
                            <button wire:click="cancel" wire:loading.attr="disabled" class="btn btn-outline-danger px-4 py-3 rounded-pill fw-bold border-2">
                                <span wire:loading wire:target="cancel" class="loading-spinner" style="border-top-color: var(--bs-danger);"></span>
                                <span wire:loading.remove wire:target="cancel">Cancel Auto-Renew</span>
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        @if($membership->cancelled_at)
            <div class="alert alert-warning border-0 glass-card d-flex align-items-center p-4 rounded-4" role="alert">
                <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center me-4" style="width: 60px; height: 60px; min-width: 60px;">
                    <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                </div>
                <div>
                    <h5 class="mb-1 fw-bold">Subscription Set to Expire</h5>
                    <p class="mb-0 text-muted">You will maintain full access to all premium features until <strong>{{ $membership->expires_at?->format('F d, Y') ?? 'N/A' }}</strong>. After this date, your profile will be limited.</p>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card border-0 bg-dark text-white p-5 rounded-4 shadow-lg overflow-hidden position-relative h-100">
            <div style="position: absolute; top: -20px; right: -20px; font-size: 10rem; opacity: 0.05; transform: rotate(-15deg);">
                <i class="bi bi-rocket-takeoff-fill"></i>
            </div>
            <h4 class="fw-bold mb-5 ls-tight">Premium Perks</h4>
            <ul class="list-unstyled mb-0 d-flex flex-column gap-4">
                <li class="d-flex align-items-start gap-3">
                    <div class="bg-primary rounded-circle p-2 text-white" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; min-width: 32px;">
                        <i class="bi bi-check-lg fw-bold"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold">Verified Profile</h6>
                        <p class="small text-white-50 mb-0">Build trust with the verification badge on your profile.</p>
                    </div>
                </li>
                <li class="d-flex align-items-start gap-3">
                    <div class="bg-primary rounded-circle p-2 text-white" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; min-width: 32px;">
                        <i class="bi bi-check-lg fw-bold"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold">Full Education Center</h6>
                        <p class="small text-white-50 mb-0">Access all current and future courses/certifications.</p>
                    </div>
                </li>
                <li class="d-flex align-items-start gap-3">
                    <div class="bg-primary rounded-circle p-2 text-white" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; min-width: 32px;">
                        <i class="bi bi-check-lg fw-bold"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold">Member Events</h6>
                        <p class="small text-white-50 mb-0">Join exclusive community networking and webinars for free.</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
