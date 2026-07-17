<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Antigravity') }}</title>

        <!-- Fonts: Outfit for Headings, Inter for Body -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Raleway:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Bootstrap 5 & Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    </head>
    <body>
        <nav class="navbar navbar-expand-lg ag-navbar sticky-top">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2 mb-0" href="/">
                    <img src="{{ asset('assets/pwoa-logo.png') }}" alt="PWOA Logo" style="height: 40px;">
                    {{-- <span class="fs-4 fw-bold">Network</span> --}}
                </a>
<div class="collapse navbar-collapse" id="mainNav">
              <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                {{-- <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li> --}}
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('membership.*') ? 'active' : '' }}" href="{{ route('membership.index') }}">Membership</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('contractors.*') ? 'active' : '' }}" href="{{ route('contractors.index') }}">Contractors</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('vendors.*') ? 'active' : '' }}" href="{{ route('vendors.index') }}">Vendors</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}" href="{{ route('events.index') }}">Events</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('education.*') ? 'active' : '' }}" href="{{ route('education.index') }}">Education</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('compliance.*') ? 'active' : '' }}" href="{{ route('compliance.index') }}">Compliance</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('tokenomics') ? 'active' : '' }}" href="{{ route('tokenomics') }}">Tokenomics</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact*') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a></li>

            </ul>
        </div>

                <div class="ms-auto d-flex align-items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-primary d-flex align-items-center gap-2">
                                Dashboard <i class="bi bi-arrow-right"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-muted text-decoration-none fw-bold small">LOG IN</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-primary px-4">JOIN NOW</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </nav>

        <header class="hero-section text-center overflow-hidden">
            <div class="container position-relative">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <span class="badge bg-primary-subtle text-primary mb-4 p-2 px-3">
                            <i class="bi bi-stars me-1"></i> THE PROFESSIONAL ECOSYSTEM IS HERE
                        </span>
                        <h1 class="display-2 fw-bold mb-4 text-gradient">Build Your Legacy.<br>Connect with Excellence.</h1>
                        <p class="lead mb-5 text-muted mx-auto fw-medium" style="max-width: 650px; font-size: 1.25rem;">
                            The official network for industry-leading contractors and vendors. Secure your verified profile and scale your business with professional tools.
                        </p>
                        <div class="d-flex justify-content-center gap-3 flex-column flex-sm-row mt-2">
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5 py-3 fs-5">Get Your Membership</a>
                            <a href="#features" class="btn btn-link text-muted text-decoration-none d-flex align-items-center justify-content-center gap-2">
                                Explore Benefits <i class="bi bi-arrow-down-short fs-4"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main id="features" class="py-5 bg-white">
            <div class="container py-5">
                <div class="row mb-5 text-center">
                    <div class="col-12">
                        <h2 class="display-5 fw-bold mb-3">Engineered for Professionals</h2>
                        <p class="text-muted fs-5 mx-auto" style="max-width: 600px;">Every feature is designed to maximize your efficiency and expand your professional reach.</p>
                    </div>
                </div>
                <div class="row g-4 pt-4">
                    <div class="col-md-4">
                        <div class="feature-card h-100 shadow-sm">
                            <div class="feature-icon">
                                <i class="bi bi-patch-check"></i>
                            </div>
                            <h3 class="h4 fw-bold mb-3">Verified Marketplace</h3>
                            <p class="text-muted">A premium directory of vetted businesses. Transparency and trust built into every connection.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card h-100 shadow-sm border-primary" style="background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%);">
                            <div class="feature-icon bg-primary text-white">
                                <i class="bi bi-mortarboard"></i>
                            </div>
                            <h3 class="h4 fw-bold mb-3">LMS & Education</h3>
                            <p class="text-muted">Exclusive access to industry courses, certifications, and advanced business training modules.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card h-100 shadow-sm">
                            <div class="feature-icon">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <h3 class="h4 fw-bold mb-3">Industry Events</h3>
                            <p class="text-muted">Participate in exclusive networking events, webinars, and meetups with professional peers.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <section class="py-5">
            <div class="container py-5 text-center">
                <div class="card bg-dark text-white p-5 border-0 rounded-5 shadow-lg overflow-hidden position-relative">
                    <div class="position-absolute top-0 end-0 p-5 opacity-10">
                        <i class="bi bi-rocket-takeoff" style="font-size: 15rem;"></i>
                    </div>
                    <div class="position-relative z-1 py-4">
                        <h2 class="display-5 fw-bold mb-4">Ready to elevate your business?</h2>
                        <p class="lead mb-5 text-white-50 mx-auto" style="max-width: 600px;">Join 5,000+ verified professionals scaling their expertise with PWOA Network tools.</p>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5 py-3 shadow">Join the Network Today</a>
                    </div>
                </div>
            </div>
        </section>

    <footer class="bg-dark text-white mt-5 py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h5 class="mb-3">PWOA</h5>
                <p class="text-white-50 mb-3">The Pressure Washers of America supporting growth, standards, education, and community in the pressure washing industry.</p>
                <div class="d-flex gap-3 fs-5">
                    <a href="#" class="footer-link"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="footer-link"><i class="bi bi-linkedin"></i></a>
                    <a href="#" class="footer-link"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="text-uppercase text-white-50 small mb-3">Association</h6>
                <ul class="list-unstyled small d-grid gap-2">
                    <li><a class="footer-link">About</a></li>
                    <li><a class="footer-link" >Membership</a></li>
                    <li><a class="footer-link" >Events</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="text-uppercase text-white-50 small mb-3">Directories</h6>
                <ul class="list-unstyled small d-grid gap-2">
                    <li><a class="footer-link" >Contractors</a></li>
                    <li><a class="footer-link" >Vendors</a></li>
                    <li><a class="footer-link" >Education</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6 class="text-uppercase text-white-50 small mb-3">Contact</h6>
                <p class="small text-white-50 mb-1">info@pwoa.org</p>
                <p class="small text-white-50 mb-1">(800) 123-PWOA</p>
                <p class="small text-white-50 mb-0">Support for contractors, vendors, members, and event partners.</p>
            </div>
        </div>
    </div>
</footer>

        <!-- Bootstrap 5 JS Bundle -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
