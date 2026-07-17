<x-guest-layout>
    <div class="mb-4 text-muted small">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="form-label small fw-bold text-muted text-uppercase">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                <input id="password" class="form-control border-start-0 ps-0" type="password" name="password"required autocomplete="current-password" />
            </div>
            @error('password')
                <div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
            {{ __('Confirm Password') }}
        </button>
    </form>
</x-guest-layout>
