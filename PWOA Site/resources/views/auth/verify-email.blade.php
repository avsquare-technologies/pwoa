<x-guest-layout>
    <div class="mb-4 text-muted small">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success border-0 d-flex align-items-center p-3 mb-4 rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>
            <div class="small fw-bold">{{ __('A new verification link has been sent to the email address you provided during registration.') }}</div>
        </div>
    @endif

    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 mt-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary py-2 px-4 fw-bold shadow-sm">
                {{ __('Resend Verification Email') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-muted text-decoration-none small fw-medium">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
