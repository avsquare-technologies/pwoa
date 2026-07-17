<x-slot name="header">
    <h2 class="h4 mb-0 text-dark">
        <i class="bi bi-rocket-takeoff me-2 text-primary"></i> {{ __('Join Annual Membership') }}
    </h2>
</x-slot>

<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="row g-5">
            <!-- Left Side: Benefits -->
            <div class="col-md-5 order-2 order-md-1">
                <div class="p-4 rounded-4 bg-primary text-white shadow-lg h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h3 class="fw-bold mb-4">{{ $plan === 'gold' ? 'Gold Plan' : 'Standard Plan' }}</h3>
                        <div class="d-flex align-items-baseline gap-2 mb-4">
                            <span class="display-4 fw-bold">${{ $plan === 'gold' ? '300' : '99' }}</span>
                            <span class="fs-5 opacity-75">/ Year</span>
                        </div>
                        
                        <ul class="list-unstyled mb-0">
                            @if ($plan === 'gold')
                                <li class="mb-3 d-flex align-items-center gap-3">
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <span>Priority Directory Placement</span>
                                </li>
                                <li class="mb-3 d-flex align-items-center gap-3">
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <span>Gold Member Badge</span>
                                </li>
                                <li class="mb-3 d-flex align-items-center gap-3">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Featured Listing Opportunities</span>
                                </li>
                                <li class="mb-3 d-flex align-items-center gap-3">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Additional Platform Benefits</span>
                                </li>
                            @else
                                <li class="mb-3 d-flex align-items-center gap-3">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Standard Directory Placement</span>
                                </li>
                                <li class="mb-3 d-flex align-items-center gap-3">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Standard Member Badge</span>
                                </li>
                                <li class="mb-3 d-flex align-items-center gap-3">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Full profile customizations</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                    
                    <div class="mt-5 opacity-75 small">
                        <p class="mb-0"><i class="bi bi-shield-lock me-1"></i> Secure checkout powered by Stripe.</p>
                    </div>
                </div>
            </div>

            <!-- Right Side: Stripe Checkout -->
            <div class="col-md-7 order-1 order-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 p-md-5">
                        @if (session('error'))
                            <div class="alert alert-danger border-0 glass-card d-flex align-items-center p-4 mb-4" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-3 fs-3"></i>
                                <div class="fw-bold">{{ session('error') }}</div>
                            </div>
                        @endif

                        @if (session('status'))
                            <div class="alert alert-success border-0 glass-card d-flex align-items-center p-4 mb-4" role="alert">
                                <i class="bi bi-check-circle-fill me-3 fs-3"></i>
                                <div class="fw-bold">{{ session('status') }}</div>
                            </div>
                        @endif

                        <!-- Plan Selection Toggle -->
                        @if(false)
                        <div class="btn-group w-100 mb-4 rounded-pill overflow-hidden p-1 bg-light border" role="group">
                            <button type="button" wire:click="$set('plan', 'standard')" class="btn rounded-pill py-2 fw-bold {{ $plan === 'standard' ? 'btn-primary shadow-sm' : 'btn-light text-secondary border-0' }}">Standard ($99/yr)</button>
                            <button type="button" wire:click="$set('plan', 'gold')" class="btn rounded-pill py-2 fw-bold {{ $plan === 'gold' ? 'btn-primary shadow-sm' : 'btn-light text-secondary border-0' }}">Gold ($300/yr)</button>
                        </div>
                        @endif

                        <form id="payment-form" wire:submit.prevent="subscribe">
                            <div class="mb-4">
                                <label for="card-holder-name" class="form-label text-muted small fw-bold text-uppercase">Cardholder Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-person text-muted"></i></span>
                                    <input id="card-holder-name" type="text" class="form-control bg-light border-0 p-3" placeholder="Full name on card" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">Card Information</label>
                                <div id="card-element" wire:ignore class="form-control bg-light border-0 p-3" style="min-height: 52px;">
                                    <!-- Stripe Elements placeholder -->
                                </div>
                                <div id="card-errors" wire:ignore class="text-danger small mt-2" role="alert"></div>
                            </div>

                            <button id="card-button" class="btn btn-primary btn-lg w-100 py-3 shadow-sm d-flex align-items-center justify-content-center gap-2" data-secret="{{ $intent }}">
                                <i class="bi bi-lock-fill"></i> Subscribe Now
                            </button>
                            
                            <p class="text-center text-muted small mt-4">
                                By subscribing, you agree to our Terms of Service and Privacy Policy. You can cancel at any time.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        let stripe = null;
        let elements = null;
        let cardElement = null;

        function initStripe() {
            const stripeDiv = document.getElementById('card-element');
            if (!stripeDiv) return;

            // 1. Initialize Stripe and mount Card Element if not already mounted
            if (!stripeDiv.querySelector('iframe')) {
                try {
                    if (typeof Stripe === 'undefined') {
                        console.error('Stripe.js is not loaded yet.');
                        return;
                    }

                    stripe = Stripe('{{ config('cashier.key') }}');
                    elements = stripe.elements();
                    cardElement = elements.create('card', {
                        style: {
                            base: {
                                fontSize: '16px',
                                color: '#32325d',
                                fontFamily: 'Inter, sans-serif',
                                '::placeholder': { color: '#aab7c4' },
                            },
                            invalid: { color: '#fa755a', iconColor: '#fa755a' },
                        }
                    });

                    cardElement.mount('#card-element');
                } catch (err) {
                    console.error('Failed to initialize Stripe Elements:', err);
                    return;
                }
            }

            // 2. Bind the click handler to the current button in the DOM
            const cardButton = document.getElementById('card-button');
            const cardHolderName = document.getElementById('card-holder-name');
            const cardErrors = document.getElementById('card-errors');

            if (cardButton && !cardButton.dataset.listenerBound) {
                cardButton.dataset.listenerBound = 'true';
                cardButton.addEventListener('click', async (e) => {
                    e.preventDefault();
                    
                    const clientSecret = cardButton.getAttribute('data-secret');
                    
                    // Disable button
                    cardButton.disabled = true;
                    cardButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';

                    const { setupIntent, error } = await stripe.confirmCardSetup(
                        clientSecret, {
                            payment_method: {
                                card: cardElement,
                                billing_details: { name: cardHolderName ? cardHolderName.value : '' }
                            }
                        }
                    );

                    if (error) {
                        if (cardErrors) cardErrors.textContent = error.message;
                        cardButton.disabled = false;
                        cardButton.innerHTML = '<i class="bi bi-lock-fill"></i> Subscribe Now';
                        delete cardButton.dataset.listenerBound; // allow re-binding if needed
                    } else {
                        @this.call('subscribe', setupIntent.payment_method);
                    }
                });
            }
        }

        // Initialize on Livewire events
        document.addEventListener('livewire:initialized', initStripe);
        document.addEventListener('livewire:navigated', initStripe);

        // Run immediately if DOM is already parsed
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(initStripe, 50);
        }
    })();
</script>
