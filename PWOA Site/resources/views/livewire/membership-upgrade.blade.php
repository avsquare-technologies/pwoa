<div>
    <x-slot name="header">
        <h4 class="fw-bold mb-0 text-dark">Change Membership Tier</h4>
    </x-slot>

    <div class="row justify-content-center py-4">
        <div class="col-xl-9">
            @if(session('error'))
                <div class="alert alert-danger border-0 glass-card d-flex align-items-center p-4 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-3 fs-3 text-danger"></i>
                    <div class="fw-bold">{{ session('error') }}</div>
                </div>
            @endif

            <div class="text-center mb-5">
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-3 fw-bold">SUBSCRIPTION ADJUSTMENT</span>
                <h2 class="fw-bold text-dark">Switch Your Membership Plan</h2>
                <p class="text-muted fs-5 mx-auto" style="max-width: 600px;">Adjust your directory listing features and exposure level by choosing the plan that best fits your business goals.</p>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- Standard Plan Card -->
                <div class="col-md-6">
                    <div class="card h-100 border-0 glass-card overflow-hidden transition-all shadow-sm {{ $currentTier === 'standard' ? 'border border-primary' : '' }}">
                        @if($currentTier === 'standard')
                            <div class="bg-primary text-white text-center py-1.5 fw-bold uppercase small" style="font-size: 0.75rem;">Current Plan</div>
                        @endif
                        <div class="card-body p-4 p-lg-5 d-flex flex-column h-100">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h4 class="fw-bold mb-0 text-dark">Standard Tier</h4>
                                    <p class="text-muted small mb-0">Professional business listing</p>
                                </div>
                                <div class="bg-light p-3 rounded-4 text-primary">
                                    <i class="bi bi-shield-check fs-2"></i>
                                </div>
                            </div>

                            <div class="mb-4">
                                <span class="display-4 fw-extrabold text-dark">$99</span>
                                <span class="text-secondary">/ year</span>
                            </div>

                            <ul class="list-unstyled mb-5 flex-grow-1">
                                <li class="d-flex align-items-start gap-2 mb-3">
                                    <i class="bi bi-check-circle-fill text-success mt-0.5"></i>
                                    <span class="text-secondary small">Standard Contractor or Vendor Directory profile</span>
                                </li>
                                <li class="d-flex align-items-start gap-2 mb-3">
                                    <i class="bi bi-check-circle-fill text-success mt-0.5"></i>
                                    <span class="text-secondary small">Address, contact options, and map integrations</span>
                                </li>
                                <li class="d-flex align-items-start gap-2 mb-3">
                                    <i class="bi bi-check-circle-fill text-success mt-0.5"></i>
                                    <span class="text-secondary small">Category tagging and equipment fleet listings</span>
                                </li>
                                <li class="d-flex align-items-start gap-2 mb-3">
                                    <i class="bi bi-check-circle-fill text-success mt-0.5"></i>
                                    <span class="text-secondary small">Upload company logos and custom taglines</span>
                                </li>
                            </ul>

                            @if($currentTier === 'gold')
                                <button wire:click="downgrade" class="btn btn-outline-primary py-3 rounded-pill fw-bold hvr-grow w-100" 
                                        onclick="return confirm('Downgrading will remove premium priority placement and custom badges. Standard pricing will take effect starting next cycle. Continue?')">
                                    Downgrade to Standard
                                </button>
                            @else
                                <button class="btn btn-secondary py-3 rounded-pill fw-bold w-100" disabled>
                                    Active Plan
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Gold Plan Card -->
                <div class="col-md-6">
                    <div class="card h-100 border-0 glass-card overflow-hidden transition-all shadow-sm {{ $currentTier === 'gold' ? 'border border-warning' : 'border border-warning-subtle' }}" style="background: linear-gradient(180deg, rgba(255, 255, 255, 0.9) 0%, rgba(254, 243, 199, 0.3) 100%);">
                        @if($currentTier === 'gold')
                            <div class="bg-warning text-dark text-center py-1.5 fw-bold uppercase small" style="font-size: 0.75rem;">Current Plan</div>
                        @else
                            <div class="bg-warning text-dark text-center py-1.5 fw-bold uppercase small" style="font-size: 0.75rem;">Recommend Tier Upgrade</div>
                        @endif
                        <div class="card-body p-4 p-lg-5 d-flex flex-column h-100">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h4 class="fw-bold mb-0 text-dark">Gold Membership</h4>
                                    <p class="text-muted small mb-0">Maximum exposure & credibility</p>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-3 rounded-4 text-warning">
                                    <i class="bi bi-award-fill fs-2"></i>
                                </div>
                            </div>

                            <div class="mb-4">
                                <span class="display-4 fw-extrabold text-dark">$300</span>
                                <span class="text-secondary">/ year</span>
                            </div>

                            <ul class="list-unstyled mb-5 flex-grow-1">
                                <li class="d-flex align-items-start gap-2 mb-3">
                                    <i class="bi bi-check-circle-fill text-warning mt-0.5"></i>
                                    <span class="text-secondary small fw-semibold">Gold verification badge on public profile</span>
                                </li>
                                <li class="d-flex align-items-start gap-2 mb-3">
                                    <i class="bi bi-check-circle-fill text-warning mt-0.5"></i>
                                    <span class="text-secondary small fw-semibold">Priority listing placement (sorted first in queries)</span>
                                </li>
                                <li class="d-flex align-items-start gap-2 mb-3">
                                    <i class="bi bi-check-circle-fill text-warning mt-0.5"></i>
                                    <span class="text-secondary small">Upload high-resolution cover photo/banners</span>
                                </li>
                                <li class="d-flex align-items-start gap-2 mb-3">
                                    <i class="bi bi-check-circle-fill text-warning mt-0.5"></i>
                                    <span class="text-secondary small">Premium dynamic badge support & verification</span>
                                </li>
                            </ul>

                            @if($currentTier === 'standard')
                                <button wire:click="upgrade" class="btn btn-warning text-dark py-3 rounded-pill fw-bold hvr-grow w-100"
                                        onclick="return confirm('You will be charged a prorated fee of the difference for the remainder of your billing year. Continue with upgrade?')">
                                    Upgrade to Gold
                                </button>
                            @else
                                <button class="btn btn-secondary py-3 rounded-pill fw-bold w-100" disabled>
                                    Active Plan
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('membership.status') }}" class="btn btn-link text-decoration-none fw-bold text-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Membership Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
