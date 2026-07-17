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
    <title>@yield('title', 'PWOA') - Pressure Washers of America</title>
    <meta name="description"
        content="@yield('meta_description', 'The Pressure Washers of America for pressure washing professionals.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <link href="{{ asset('css/common.css') }}?v={{ file_exists(public_path('css/common.css')) ? filemtime(public_path('css/common.css')) : time() }}" rel="stylesheet">
    <link href="{{ asset('css/front.css') }}?v={{ file_exists(public_path('css/front.css')) ? filemtime(public_path('css/front.css')) : time() }}" rel="stylesheet">
    @stack('styles')

</head>

<body>
    @include('layouts.partials.header')

    <main>@yield('content')</main>

    @include('layouts.partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {1
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: "{!! session('success') !!}"
            });
        @endif

        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: "{!! session('error') !!}"
            });
        @endif
        
        @if(session('info'))
            Toast.fire({
                icon: 'info',
                title: "{!! session('info') !!}"
            });
        @endif
        
        @if(session('warning'))
            Toast.fire({
                icon: 'warning',
                title: "{!! session('warning') !!}"
            });
        @endif

        // Global Button Loading Handler
        document.addEventListener('DOMContentLoaded', function() {
            // Handle form submissions
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (this.classList.contains('is-submitting')) {
                        e.preventDefault();
                        return;
                    }

                    // Get loading message from form or use default
                    let loadingTitle = this.getAttribute('data-loading-title') || 'Processing Request';
                    let loadingHtml = this.getAttribute('data-loading-text') || 'Please wait while we complete your request...';

                    // Use XRPL specific message only if marked
                    if (this.classList.contains('xrpl-form')) {
                        loadingTitle = 'Processing Transaction';
                        loadingHtml = 'Please wait while we interact with the XRPL ledger...';
                    }

                    // Show SweetAlert Loading
                    Swal.fire({
                        title: loadingTitle,
                        html: loadingHtml,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    const submitBtn = this.querySelector('button[type="submit"], input[type="submit"]');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
                        setTimeout(() => {
                            submitBtn.disabled = true;
                        }, 1);
                        submitBtn.style.opacity = '0.7';
                        submitBtn.style.cursor = 'not-allowed';
                    }
                    this.classList.add('is-submitting');
                });
            });

            // Handle direct button/link clicks for actions
            document.querySelectorAll('.btn-buy, .btn-mint, .btn-load, [data-loading], button:not([type="submit"])').forEach(el => {
                el.addEventListener('click', function(e) {
                    // Only trigger if it's not a simple navigation or if it has a specific action class
                    if (this.classList.contains('is-processing')) {
                        e.preventDefault();
                        return;
                    }

                    // For buttons that are NOT in forms but trigger actions
                    if (this.classList.contains('btn-buy') || this.classList.contains('btn-mint') || this.hasAttribute('data-loading')) {
                        Swal.fire({
                            title: 'Processing Request',
                            html: 'Initializing secure blockchain connection...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        this.classList.add('is-processing');
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>

</html>