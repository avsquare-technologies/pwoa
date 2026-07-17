<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Error Alerts -->
    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #f87171; padding: 14px;">
            <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
            <div class="small fw-semibold">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-warning border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); color: #fbbf24; padding: 14px;">
            <i class="bi bi-exclamation-circle-fill fs-5 text-warning"></i>
            <div class="small fw-semibold">{{ session('error') }}</div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                <input id="email" class="form-control border-start-0 ps-0" type="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" required autofocus autocomplete="username" />
            </div>
            @error('email')
                <div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label small fw-bold text-muted text-uppercase">
                Password
            </label>

            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-lock text-muted"></i>
                </span>

                <input
                    id="password"
                    class="form-control border-start-0 border-end-0 ps-0"
                    type="password"
                    name="password"
                    
                    required
                    autocomplete="current-password"
                />

                <button
                    class="input-group-text bg-light border-start-0"
                    type="button"
                    id="togglePassword"
                >
                    <i class="bi bi-eye text-muted" id="togglePasswordIcon"></i>
                </button>
            </div>

            @error('password')
                <div class="text-danger small mt-2">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input id="remember_me" type="checkbox" class="form-check-input mt-1" name="remember">
                <label for="remember_me" class="form-check-label text-muted small">{{ __('Remember me') }}</label>
            </div>
            @if (Route::has('password.request'))
                <a class="text-decoration-none small text-primary fw-medium" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <button type="submit" id="submitBtn" class="btn btn-primary w-100 py-2 fw-bold shadow-sm mb-4 d-flex align-items-center justify-content-center gap-2">
            <span id="btnText">{{ __('Sign In') }}</span>
            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>

        <div class="text-center">
            <p class="text-muted small mb-0">Don't have an account? <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">Create one</a></p>
        </div>
    </form>
    <script>
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');

    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password'
            ? 'text'
            : 'password';

        passwordInput.setAttribute('type', type);

        togglePasswordIcon.classList.toggle('bi-eye');
        togglePasswordIcon.classList.toggle('bi-eye-slash');
    });

    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');

    loginForm.addEventListener('submit', function () {
        setTimeout(() => {
            submitBtn.disabled = true;
        }, 1);
        btnText.textContent = 'Signing In...';
        btnSpinner.classList.remove('d-none');
    });
</script>
</x-guest-layout>
