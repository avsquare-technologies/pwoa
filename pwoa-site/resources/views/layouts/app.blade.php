<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
   

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

      <!-- Favicon -->
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/favicon_io/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon_io/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon_io/favicon-16x16.png') }}">
<link rel="manifest" href="{{ asset('assets/favicon_io/site.webmanifest') }}">
<link rel="shortcut icon" href="{{ asset('assets/favicon_io/favicon.ico') }}" type="image/x-icon">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pressure Washers Of America (PWOA)') }}</title>

    <!-- Fonts: Outfit for Headings, Inter for Body -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Raleway:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://js.stripe.com/v3/"></script>

    @livewireStyles

    <link href="{{ asset('css/common.css') }}?v={{ file_exists(public_path('css/common.css')) ? filemtime(public_path('css/common.css')) : time() }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}?v={{ file_exists(public_path('css/dashboard.css')) ? filemtime(public_path('css/dashboard.css')) : time() }}" rel="stylesheet">
</head>

<body class="antialiased">
    <div id="sidebar" class="d-flex flex-column p-4">
        @include('layouts.navigation_sidebar')
    </div>

    <div id="main-wrapper">
        <header id="top-header">
            <button class="btn btn-link d-lg-none p-0 me-3"
                onclick="document.getElementById('sidebar').classList.toggle('active')">
                <i class="bi bi-list fs-2 text-dark"></i>
            </button>
            @isset($header)
                <div class="flex-grow-1">
                    {{ $header }}
                </div>
            @endisset
            <div class="ms-auto d-flex align-items-center gap-3">
                <a href="{{ route('wallet.index') }}"
                    class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px;" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="My Wallet">
                    <i class="bi bi-wallet2"></i>
                </a>
                <a href="{{ route('home') }}"
                    class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px;" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="Visit Website">

                    <i class="bi bi-globe"></i>
                </a>
                @livewire('components.header-profile-menu')
            </div>
        </header>

        <main class="content-area">
            {{ $slot }}
        </main>

        <footer class="py-4 px-5 text-muted small border-top bg-white mt-auto">
            <div class="d-flex justify-content-between align-items-center">
                <span>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</span>
                <div class="d-flex gap-4">
                    <a href="#" class="text-muted text-decoration-none">Privacy Policy</a>
                    <a href="#" class="text-muted text-decoration-none">Terms of Service</a>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');

            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl, {
                    delay: { show: 200, hide: 100 }
                });
            });

        });
    </script>
    @livewireScripts
    @stack('scripts')
    @stack('modals')
</body>

</html>