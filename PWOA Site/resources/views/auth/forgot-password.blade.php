<x-guest-layout>
    <div class="mb-4 text-muted small">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                <input id="email" class="form-control border-start-0 ps-0" type="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" required autofocus />
            </div>
            @error('email')
                <div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
            {{ __('Email Password Reset Link') }}
        </button>

        <div class="text-center mt-4">
            <p class="text-muted small mb-0">Remember your password? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Sign in here</a></p>
        </div>
    </form>
</x-guest-layout>
