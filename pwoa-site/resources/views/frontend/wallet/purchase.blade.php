<x-app-layout>
    {{-- 
        Token Purchase Page
        This page handles the Stripe-integrated purchase flow for $WASH tokens.
        Built with Bootstrap 5 for a premium, responsive experience.
    --}}

    <x-slot name="header">
        <h2 class="h4 mb-0 text-dark fw-bold">
            {{ __('Purchase $WASH Tokens') }}
        </h2>
    </x-slot>

    <div class="purchase-page container py-5">
        <div class="row justify-content-center g-5">
            <!-- Left Side: Value Proposition & Trust (Hidden on mobile) -->
            <div class="col-lg-5 d-none d-lg-block">
                <div class="sticky-top" style="top: 120px;">
                    <h1 class="display-5 fw-bold mb-4" style="font-family: 'Raleway', sans-serif;">Empower Your Experience</h1>
                    <p class="lead text-muted mb-5">
                        $WASH tokens are the core utility of our ecosystem. Purchase them securely via Stripe to unlock premium features, participate in events, and earn rewards.
                    </p>
                    
                    <div class="d-flex flex-column gap-4">
                        <!-- Benefit Items -->
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-4">
                                <i class="bi bi-shield-lock-fill fs-4 text-primary"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Secure Transactions</h6>
                                <p class="small text-muted mb-0">Processed by Stripe with industrial-grade encryption for your peace of mind.</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-success bg-opacity-10 p-3 rounded-4">
                                <i class="bi bi-lightning-fill fs-4 text-success"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Instant Delivery</h6>
                                <p class="small text-muted mb-0">Tokens are minted and delivered to your wallet immediately after payment confirmation.</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-warning bg-opacity-10 p-3 rounded-4">
                                <i class="bi bi-graph-up-arrow fs-4 text-warning"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Ecosystem Utility</h6>
                                <p class="small text-muted mb-0">Use your tokens for course enrollments, event tickets, and platform certifications.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Interactive Purchase Form -->
            <div class="col-lg-6 col-xl-5">
                <div class="card purchase-card border-0 shadow-lg rounded-5 overflow-hidden">
                    <!-- Progress/Indicator Line -->
                    <div style="height: 5px; background: linear-gradient(90deg, var(--ag-primary) 0%, var(--ag-secondary) 100%);"></div>
                    
                    <div class="card-body p-4 p-md-5">
                        <!-- Form Header -->
                        <div class="text-center mb-5">
                            <div class="icon-circle mx-auto mb-3 shadow-sm" style="width: 76px; height: 76px; background: rgba(0, 149, 215, 0.08); display: flex; align-items: center; justify-content: center; border-radius: 22px;">
                                <i class="bi bi-cart-plus-fill fs-1 text-primary"></i>
                            </div>
                            <h3 class="fw-bold text-dark">Token Checkout</h3>
                            <div class="badge bg-light text-primary rounded-pill px-3 py-2 mt-2 border border-primary border-opacity-10">
                                <i class="bi bi-tag-fill me-1"></i> 1 USD = 1.00 $WASH
                            </div>
                        </div>

                        <!-- Purchase Form -->
                        <form action="{{ route('token.checkout') }}" method="POST" id="purchaseForm">
                            @csrf
                            <div class="mb-4">
                                <label for="usd_amount" class="form-label fw-bold text-muted small text-uppercase ls-wide mb-3">Amount to Purchase (USD)</label>
                                <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border border-2 transition-all" id="inputWrapper">
                                    <span class="input-group-text bg-white border-0 ps-4 text-muted fw-bold">$</span>
                                    <input type="number" name="usd_amount" id="usd_amount" step="1" min="1" required
                                        class="form-control border-0 ps-2 fw-bold text-dark"
                                        placeholder="0.00"
                                        oninput="updatePreview(this.value)"
                                        onfocus="document.getElementById('inputWrapper').style.borderColor = 'var(--ag-primary)'"
                                        onblur="document.getElementById('inputWrapper').style.borderColor = '#e2e8f0'"
                                        style="font-size: 1.75rem; letter-spacing: -0.5px;">
                                </div>
                            </div>

                            <!-- Modern Receipt UI -->
                            <div class="preview-box p-4 mb-5 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted small fw-medium">Token Quantity:</span>
                                    <span class="fw-bold text-dark"><span id="token_preview">0</span> $WASH</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted small fw-medium">Network Fee:</span>
                                    <span class="text-success small fw-bold text-uppercase">Sponsored</span>
                                </div>
                                <div class="my-3 border-top border-dashed" style="border-style: dashed !important; opacity: 0.5;"></div>
                                <div class="d-flex justify-content-between align-items-center pt-2">
                                    <span class="fw-bold text-dark fs-5">Grand Total:</span>
                                    <div class="text-end">
                                        <span class="h3 fw-bold text-primary mb-0">$<span id="total_preview">0.00</span></span>
                                        <div class="small text-muted" style="font-size: 0.65rem;">Includes all taxes</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-purchase w-100 py-3 rounded-4 shadow-sm d-flex align-items-center justify-content-center gap-3 transition-all hvr-grow" id="submitBtn" style="background: var(--ag-primary); border: none; color: white;">
                                <span class="fw-bold fs-5">Proceed to Payment</span>
                                <i class="bi bi-stripe fs-3"></i>
                            </button>
                        </form>

                        <!-- Trust Markers -->
                        <div class="mt-5 pt-4 border-top text-center">
                            <div class="d-flex justify-content-center gap-4 mb-4 opacity-50 grayscale">
                                <i class="bi bi-credit-card-2-front fs-4" title="Visa/Mastercard"></i>
                                <i class="bi bi-apple fs-4" title="Apple Pay"></i>
                                <i class="bi bi-google fs-4" title="Google Pay"></i>
                            </div>
                            <p class="text-muted mb-0" style="font-size: 0.75rem; line-height: 1.6;">
                                <i class="bi bi-shield-fill-check text-success me-1"></i>
                                Verified Secure Checkout. Instant token issuance on successful payment. 
                                See our <a href="#" class="text-primary text-decoration-none fw-bold">Refund Policy</a>.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Secondary Actions -->
                <div class="text-center mt-5">
                    <a href="{{ route('wallet.index') }}" class="btn btn-link text-muted text-decoration-none small hvr-grow">
                        <i class="bi bi-arrow-left-circle-fill me-2 fs-5 align-middle"></i> Back to Wallet Overview
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        /**
         * Real-time preview calculation
         * 1 USD = 1 $WASH exchange rate
         */
        function updatePreview(val) {
            const amount = parseFloat(val) || 0;
            // Format with thousands separator
            document.getElementById('token_preview').innerText = amount.toLocaleString(undefined, {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
            // Format currency
            document.getElementById('total_preview').innerText = amount.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        /**
         * Handle button loading state
         */
        document.getElementById('purchaseForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.classList.add('opacity-75');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Initializing Stripe...';
        });
    </script>
    @endpush

    <style>
        .ls-wide { letter-spacing: 1.5px; }
        .hvr-grow { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .hvr-grow:hover { transform: translateY(-3px); opacity: 0.95; }
        .transition-all { transition: all 0.2s ease; }
        .grayscale { filter: grayscale(100%); }
        .grayscale:hover { filter: grayscale(0%); opacity: 1 !important; transition: all 0.4s ease; }
        
        /* Custom card styling for purchase page */
        .purchase-card {
            background: #ffffff;
            border: 1px solid rgba(0,0,0,0.05) !important;
        }

        #usd_amount:focus {
            outline: none;
            box-shadow: none;
        }
    </style>
</x-app-layout>