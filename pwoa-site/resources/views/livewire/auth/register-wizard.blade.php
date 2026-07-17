<div>
    <!-- Step Progress Indicator -->
    <div class="mb-4 text-center">
        <div class="d-flex justify-content-between align-items-center mb-2 position-relative" style="max-width: 600px; margin: 0 auto;">
            <!-- Line connector -->
            <div class="position-absolute top-50 start-0 end-0 translate-middle-y bg-secondary bg-opacity-20" style="height: 4px; z-index: 1;"></div>
            <div class="position-absolute top-50 start-0 bg-primary" style="height: 4px; width: {{ (($currentStep - 1) / ($totalSteps - 1)) * 100 }}%; transition: width 0.4s ease; z-index: 2;"></div>

            <!-- Steps -->
            @for ($i = 1; $i <= $totalSteps; $i++)
                <div class="position-relative" style="z-index: 3;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold {{ $currentStep >= $i ? 'bg-primary text-white' : 'bg-white text-muted border border-2' }}"
                         style="width: 38px; height: 38px; transition: all 0.3s ease; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                        @if ($currentStep > $i)
                            <i class="bi bi-check-lg"></i>
                        @else
                            {{ $i }}
                        @endif
                    </div>
                    <span class="position-absolute top-100 start-50 translate-middle-x mt-2 small fw-semibold text-nowrap d-none d-md-block {{ $currentStep == $i ? 'text-primary' : 'text-muted' }}">
                        {{ match($i) {
                            1 => 'Account',
                            2 => 'Company',
                            3 => 'Membership',
                            4 => 'Verification',
                            default => ''
                        } }}
                    </span>
                </div>
            @endfor
        </div>
        <div class="mt-4">
            <h4 class="fw-bold text-dark mt-5 my-3">{{ match($currentStep) {
                1 => 'Create Account Profile',
                2 => 'Enter Company Information',
                3 => 'Select Membership Tier',
                4 => 'Verify Your Email',
                default => ''
            } }}</h4>
            <p class="text-muted small">Step {{ $currentStep }} of {{ $totalSteps }} - {{ match($currentStep) {
                1 => 'Enter your personal account contact details',
                2 => 'Specify your directory listing company profile',
                3 => 'Choose standard or gold placement tier',
                4 => 'Submit the 6-digit verification pin sent to ' . $email,
                default => ''
            } }}</p>
        </div>
    </div>

    <!-- Wizard Steps Form -->
    <div class="card border-0 glass-card shadow-sm mt-4">
        <div class="card-body p-4 p-md-5">
            <!-- Step 1: Account Information -->
            @if ($currentStep === 1)
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase">First Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-person text-muted"></i></span>
                            <input type="text" wire:model="first_name" class="form-control bg-light border-0 py-2.5 rounded-end shadow-none @error('first_name') is-invalid @enderror" placeholder="John">
                            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Last Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-person text-muted"></i></span>
                            <input type="text" wire:model="last_name" class="form-control bg-light border-0 py-2.5 rounded-end shadow-none @error('last_name') is-invalid @enderror" placeholder="Doe">
                            @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" wire:model="email" class="form-control bg-light border-0 py-2.5 rounded-end shadow-none @error('email') is-invalid @enderror" placeholder="john.doe@example.com">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-telephone text-muted"></i></span>
                            <input type="text" wire:model="phone" class="form-control bg-light border-0 py-2.5 rounded-end shadow-none @error('phone') is-invalid @enderror" placeholder="(555) 000-0000">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" id="password-input" wire:model="password" class="form-control bg-light border-0 py-2.5 shadow-none @error('password') is-invalid @enderror" placeholder="">
                            <button class="btn bg-light border-0 py-2.5" type="button" onclick="togglePassword('password-input', this)" style="border-top-right-radius: 0.75rem; border-bottom-right-radius: 0.75rem;">
                                <i class="bi bi-eye text-muted"></i>
                            </button>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-shield-lock text-muted"></i></span>
                            <input type="password" id="password-confirm-input" wire:model="password_confirmation" class="form-control bg-light border-0 py-2.5 shadow-none" placeholder="">
                            <button class="btn bg-light border-0 py-2.5" type="button" onclick="togglePassword('password-confirm-input', this)" style="border-top-right-radius: 0.75rem; border-bottom-right-radius: 0.75rem;">
                                <i class="bi bi-eye text-muted"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 2: Company details -->
            @if ($currentStep === 2)
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Company Name</label>
                        <input type="text" wire:model="company_name" class="form-control bg-light border-0 py-2.5 rounded shadow-none @error('company_name') is-invalid @enderror" placeholder="e.g. Wash Patrol">
                        @error('company_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Directory Type</label>
                        <select wire:model.live="directory_type" class="form-select bg-light border-0 py-2.5 rounded shadow-none">
                            <option value="contractor">Contractor Directory (Performs services)</option>
                            <option value="vendor">Vendor Directory (Sells products/software/coaching)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Website Link</label>
                        <input type="url" wire:model="website" class="form-control bg-light border-0 py-2.5 rounded shadow-none @error('website') is-invalid @enderror" placeholder="https://website.com">
                        @error('website') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Years in Business</label>
                        <input type="number" wire:model="years_in_business" class="form-control bg-light border-0 py-2.5 rounded shadow-none @error('years_in_business') is-invalid @enderror" placeholder="e.g. 5">
                        @error('years_in_business') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Company Phone</label>
                        <input type="text" wire:model="company_phone" class="form-control bg-light border-0 py-2.5 rounded shadow-none @error('company_phone') is-invalid @enderror" placeholder="(555) 000-0000">
                        @error('company_phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Company Email</label>
                        <input type="email" wire:model="company_email" class="form-control bg-light border-0 py-2.5 rounded shadow-none @error('company_email') is-invalid @enderror" placeholder="office@company.com">
                        @error('company_email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    @if ($directory_type === 'contractor')
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary text-uppercase">License Number (Optional)</label>
                            <input type="text" wire:model="license_number" class="form-control bg-light border-0 py-2.5 rounded shadow-none @error('license_number') is-invalid @enderror" placeholder="e.g. LIC-9988">
                            @error('license_number') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 d-flex align-items-center pt-md-4">
                            <div class="form-check form-switch py-2">
                                <input class="form-check-input" type="checkbox" wire:model="is_insured" id="is_insured">
                                <label class="form-check-label fw-bold text-secondary ms-2" for="is_insured">Fully Insured</label>
                            </div>
                        </div>
                    @endif

                    <div class="col-12 mt-4 border-top pt-4">
                        <h6 class="fw-bold mb-3">Headquarters Address</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small text-muted text-uppercase fw-bold mb-1">Street Address</label>
                                <input type="text" wire:model="address" class="form-control bg-light border-0 py-2 rounded @error('address') is-invalid @enderror" placeholder="123 Corporate Blvd">
                                @error('address') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-muted text-uppercase fw-bold mb-1">Country</label>
                                <select wire:model.live="country_id" class="form-select bg-light border-0 py-2 rounded">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                                @error('country_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-muted text-uppercase fw-bold mb-1">Zip / Postal Code</label>
                                <input type="text" wire:model="zip" class="form-control bg-light border-0 py-2 rounded @error('zip') is-invalid @enderror" placeholder="e.g. 10001">
                                @error('zip') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-muted text-uppercase fw-bold mb-1">State / Province</label>
                                <select wire:model.live="state_id" class="form-select bg-light border-0 py-2 rounded" {{ empty($states) ? 'disabled' : '' }}>
                                    <option value="">Select State</option>
                                    @foreach($states as $s)
                                        <option value="{{ $s['id'] }}">{{ $s['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('state_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-muted text-uppercase fw-bold mb-1">City</label>
                                <select wire:model.live="city_id" class="form-select bg-light border-0 py-2 rounded" {{ empty($cities) ? 'disabled' : '' }}>
                                    <option value="">Select City</option>
                                    @foreach($cities as $ct)
                                        <option value="{{ $ct['id'] }}">{{ $ct['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('city_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 3: Membership Tier -->
            @if ($currentStep === 3)
                <div class="row g-4 justify-content-center">
                    <div class="col-lg-6 col-md-8">
                        <div wire:click="$set('membership_tier', 'standard')" 
                             class="card card-soft h-100 cursor-pointer border-2 {{ $membership_tier === 'standard' ? 'border-primary shadow-md' : 'border-light shadow-sm' }}"
                             style="transition: all 0.3s ease; position: relative; background-color: {{ $membership_tier === 'standard' ? 'rgba(0, 149, 215, 0.05) !important' : '#fff' }};">
                            <!-- Radio indicator -->
                            <div class="position-absolute top-0 end-0 m-4">
                                @if ($membership_tier === 'standard')
                                    <i class="bi bi-record-circle-fill text-primary fs-3"></i>
                                @else
                                    <i class="bi bi-circle text-muted fs-3"></i>
                                @endif
                            </div>
                            <div class="card-body p-4 p-lg-5">
                                <h2 class="h3 mb-2">Standard Membership</h2>
                                <p class="display-5 fw-bold mb-3">$99 <span class="fs-6 text-secondary">/ year</span></p>
                                <ul class="text-secondary mb-4">
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i>Member directory listing</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i>Compliance resource access</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i>Core education access</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i>Member event discounts</li>
                                    <li><i class="bi bi-check-circle-fill text-primary me-2"></i>PWOA token participation rewards</li>
                                </ul>
                                <button type="button" class="btn btn-brand btn-lg w-100 {{ $membership_tier === 'standard' ? '' : 'btn-outline-primary bg-white text-primary' }}">
                                    {{ $membership_tier === 'standard' ? 'Selected' : 'Select Standard' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    @if(false)
                    <div class="col-lg-5">
                        <div wire:click="$set('membership_tier', 'gold')" 
                             class="card card-soft h-100 cursor-pointer border-2 {{ $membership_tier === 'gold' ? 'border-warning shadow-md' : 'border-light shadow-sm' }}"
                             style="transition: all 0.3s ease; position: relative; background-color: {{ $membership_tier === 'gold' ? 'rgba(245, 158, 11, 0.05) !important' : '#fff' }};">
                            <!-- Radio indicator -->
                            <div class="position-absolute top-0 end-0 m-4">
                                @if ($membership_tier === 'gold')
                                    <i class="bi bi-record-circle-fill text-warning fs-3"></i>
                                @else
                                    <i class="bi bi-circle text-muted fs-3"></i>
                                @endif
                            </div>
                            <div class="card-body p-4 p-lg-5">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h2 class="h3 mb-0">Gold Membership</h2>
                                    <span class="badge text-bg-warning">Best Value</span>
                                </div>
                                <p class="display-5 fw-bold mb-3">$300 <span class="fs-6 text-secondary">/ year</span></p>
                                <ul class="text-secondary mb-4">
                                    <li class="mb-2"><i class="bi bi-star-fill text-warning me-2"></i>Everything in Standard</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i>Priority directory placement</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i>Unlimited course access</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i>Higher event discounts</li>
                                    <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Priority support and stronger rewards</li>
                                </ul>
                                <button type="button" class="btn btn-accent btn-lg w-100 {{ $membership_tier === 'gold' ? '' : 'btn-outline-warning bg-white text-dark' }}">
                                    {{ $membership_tier === 'gold' ? 'Selected' : 'Select Gold' }}
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            @endif

            <!-- Step 4: OTP Verification -->
            @if ($currentStep === 4)
                <div class="text-center py-4" style="max-width: 500px; margin: 0 auto;">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                        <i class="bi bi-envelope-open fs-1"></i>
                    </div>
                    
                    <h5 class="fw-bold mb-2">Check Your Email</h5>
                    <p class="text-secondary small mb-4">We sent a 6-digit verification PIN to <strong>{{ $email }}</strong>. Please check your spam folder if it doesn't arrive within a few seconds.</p>

                    @if(session('otp_success'))
                        <div class="alert alert-success border-0 small py-2 mb-3">{{ session('otp_success') }}</div>
                    @endif

                    <div class="mb-4">
                        <input type="text" wire:model="otp" class="form-control form-control-lg text-center fw-bold fs-3 bg-light border-0 py-3 rounded-3 shadow-none @error('otp') is-invalid @enderror" placeholder="000000" maxlength="6" style="letter-spacing: 0.5rem;">
                        @error('otp') <div class="invalid-feedback text-center mt-2 d-block">{{ $message }}</div> @enderror
                    </div>

                    <button type="button" wire:click="verifyOtp" wire:loading.attr="disabled" class="btn btn-primary btn-lg w-100 py-3 rounded-pill fw-bold shadow-sm mb-3 d-inline-flex align-items-center justify-content-center">
                        <span wire:loading wire:target="verifyOtp" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        <span>Verify & Create Account</span>
                        <i class="bi bi-arrow-right ms-1" wire:loading.remove wire:target="verifyOtp"></i>
                    </button>

                    <p class="text-muted small">
                        Didn't receive the PIN? 
                        <a href="javascript:void(0)" wire:click="resendOtp" wire:loading.class="opacity-50 pointer-events-none" class="text-primary fw-bold text-decoration-none ms-1">
                            <span wire:loading wire:target="resendOtp" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            Resend PIN
                        </a>
                    </p>
                </div>
            @endif

            <!-- Stepper Footer Action Buttons -->
            @if ($currentStep < 4)
                <div class="pt-5 border-top d-flex justify-content-between align-items-center mt-4">
                    @if ($currentStep > 1)
                        <button type="button" wire:click="prevStep" wire:loading.attr="disabled" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </button>
                    @else
                        <div></div>
                    @endif

                    <button type="button" wire:click="nextStep" wire:loading.attr="disabled" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm d-inline-flex align-items-center justify-content-center">
                        <span wire:loading wire:target="nextStep" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        <span>Continue</span>
                        <i class="bi bi-arrow-right ms-1" wire:loading.remove wire:target="nextStep"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Toggle Password Visibility Script -->
    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input && icon) {
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        }
    </script>
</div>
