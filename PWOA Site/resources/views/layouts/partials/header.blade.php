{{-- @php($loginUrl = \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/admin/login')) --}}
</style>

<nav class="navbar navbar-expand-lg navbar-light bg-brand shadow-sm sticky-top">
        <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('home') }}">
                        <img src="{{ asset('assets/pwoa-logo.png') }}" alt="PWOA Logo" style="height:40px;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                        aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNav">
                        <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                                <li class="nav-item"><a
                                                class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                                                href="{{ route('home') }}">Home</a></li>
                                <li class="nav-item"><a
                                                class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}"
                                                href="{{ route('about') }}">About</a></li>
                                <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle {{ (request()->routeIs('contractors.*') || request()->routeIs('vendors.*')) ? 'active' : '' }}"
                                                href="#" id="directoryDropdown" role="button" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                Directory
                                        </a>
                                        <ul class="dropdown-menu border-0 shadow-sm"
                                                aria-labelledby="directoryDropdown">
                                                <li><a class="dropdown-item"
                                                                href="{{ route('contractors.index') }}">Contractor</a>
                                                </li>
                                                <li><a class="dropdown-item"
                                                                href="{{ route('vendors.index') }}">Vendor</a></li>
                                        </ul>
                                </li>
                                <li class="nav-item"><a
                                                class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}"
                                                href="{{ route('events.index') }}">Events</a></li>
                                <li class="nav-item"><a
                                                class="nav-link {{ request()->routeIs('education.*') ? 'active' : '' }}"
                                                href="{{ route('education.index') }}">Education</a></li>
                                <li class="nav-item"><a
                                                class="nav-link {{ request()->routeIs('compliance.*') ? 'active' : '' }}"
                                                href="{{ route('compliance.index') }}">Compliance</a></li>
                                <li class="nav-item"><a
                                                class="nav-link {{ request()->routeIs('tokenomics') ? 'active' : '' }}"
                                                href="{{ route('tokenomics') }}">Tokenomics</a></li>
                                <li class="nav-item"><a
                                                class="nav-link {{ request()->routeIs('contact*') ? 'active' : '' }}"
                                                href="{{ route('contact') }}">Contact</a></li>
                                @guest
                                        <li class="nav-item ms-lg-2"><a class="btn  btn-sm px-3"
                                                        href="{{ route('login') }}">Login</a>
                                        </li>
                                        <li class="nav-item"><a class="btn btn-accent btn-sm px-3"
                                                        href="{{ route('register') }}">Become a Member</a></li>
                                @else
                                        <li class="nav-item ms-lg-2"><a class="btn btn-light btn-sm px-3"
                                                        href="{{ route('dashboard') }}">Dashboard</a></li>
                                        @if (\Illuminate\Support\Facades\Route::has('logout'))
                                                <li class="nav-item">
                                                        <form method="POST" action="{{ route('logout') }}">@csrf<button
                                                                        class="btn btn-outline-light btn-sm px-3"
                                                                        type="submit">Logout</button></form>
                                                </li>
                                        @endif
                                @endguest
                        </ul>
                </div>
        </div>
</nav>
