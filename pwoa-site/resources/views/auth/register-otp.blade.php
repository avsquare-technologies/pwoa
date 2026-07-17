<x-guest-layout>
    <div class="mb-4 text-center">
        <h3 class="h4 fw-bold text-dark mb-2">Verify Your Email</h3>
        <p class="text-secondary small px-3">
            We have sent a 6-digit verification code to the email address you registered with. Please enter the code below to complete your registration.
        </p>
    </div>

    <!-- Session Status Alerts -->
    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 small text-center" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('register.otp.verify') }}" id="otpForm">
        @csrf

        <!-- OTP Code Input -->
        <div class="mb-4">
            <label for="otp" class="form-label small fw-bold text-muted text-uppercase d-block text-center">
                6-Digit Verification Code
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-check text-muted fs-5"></i></span>
                <input id="otp" 
                       class="form-control border-start-0 ps-0 text-center fw-bold ls-wide" 
                       type="text" 
                       name="otp" 
                       placeholder="123456" 
                       maxlength="6" 
                       pattern="[0-9]{6}" 
                       inputmode="numeric" 
                       required 
                       autofocus 
                       autocomplete="off" 
                       style="font-size: 1.4rem; letter-spacing: 0.3em; height: 52px; border-radius: 0.85rem;" />
            </div>
            @error('otp')
                <div class="text-danger small mt-2 text-center">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm mb-4" style="height: 48px; border-radius: 0.85rem;">
            {{ __('Verify & Register') }}
        </button>
    </form>

    <div class="text-center mt-3">
        <form method="POST" action="{{ route('register.otp.resend') }}">
            @csrf
            <p class="text-muted small mb-0">
                Didn't receive the code?
                <button type="submit" class="btn btn-link text-primary fw-bold text-decoration-none p-0 align-baseline small">
                    Resend Code
                </button>
            </p>
        </form>
    </div>
</x-guest-layout>
