<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Antigravity') }}</title>

    <!-- Fonts: Outfit for Headings, Inter for Body -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Raleway:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Stripe JS -->
    <script src="https://js.stripe.com/v3/"></script>

    <link href="{{ asset('css/common.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>

<body>
    @include('layouts.partials.header')
    <div class="auth-card {{ request()->routeIs('register*') ? 'auth-card-lg' : '' }}">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <a href="/" class="text-decoration-none">
                    <div class="mb-4">
                        <img src="{{ asset('assets/pwoa-logo.png') }}" alt="PWOA Logo" style="height: 80px;">
                    </div>
                    <h2 class="text-dark mb-1">{{ config('app.name') }}</h2>
                    <p class="text-muted small">Advanced Professional Platform</p>
                </a>
            </div>

            {{ $slot }}
        </div>
    </div>
    @include('layouts.partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function () {
                const btn = form.querySelector('button[type="submit"]');
                if (!btn) return;
                btn.disabled = true;
                btn.dataset.originalText = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Please wait…';
            });
        });
        // Re-enable if user navigates back (bfcache)
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) {
                document.querySelectorAll('button[type="submit"][disabled]').forEach(btn => {
                    btn.disabled = false;
                    if (btn.dataset.originalText) btn.innerHTML = btn.dataset.originalText;
                });
            }
        });
    </script>
</body>

</html>