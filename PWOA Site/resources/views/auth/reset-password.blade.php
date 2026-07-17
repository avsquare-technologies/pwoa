<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                <input id="email" class="form-control border-start-0 ps-0" type="email" name="email" value="{{ old('email', $request->email) }}" placeholder="name@example.com" required autofocus autocomplete="username" />
            </div>
            @error('email')
                <div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="form-label small fw-bold text-muted text-uppercase">New Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                <input id="password" class="form-control border-start-0 ps-0" type="password" name="password" required autocomplete="new-password" />
            </div>
            @error('password')
                <div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label small fw-bold text-muted text-uppercase">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-lock text-muted"></i></span>
                <input id="password_confirmation" class="form-control border-start-0 ps-0" type="password" name="password_confirmation"  required autocomplete="new-password" />
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
            {{ __('Reset Password') }}
        </button>
    </form>
</x-guest-layout>
