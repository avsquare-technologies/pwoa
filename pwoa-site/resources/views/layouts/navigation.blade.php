<nav class="navbar navbar-expand-lg ag-navbar mb-4 shadow-sm">
    <div class="container py-1">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            <img src="{{ asset('assets/pwoa-logo.png') }}" alt="PWOA Logo" style="height: 40px;">
        </a>
        
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto ps-lg-4 gap-lg-2">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-bold' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-house-door me-1"></i> {{ __('Dashboard') }}
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ (request()->routeIs('directory') || request()->routeIs('contractors.*') || request()->routeIs('vendors.*')) ? 'active fw-bold' : '' }}" href="#" id="directoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-search me-1"></i> {{ __('Directory') }}
                    </a>
                    <div class="dropdown-menu border-0 shadow-lg mt-lg-3 p-2" style="border-radius: 12px; min-width: 180px;" aria-labelledby="directoryDropdown">
                        <a class="dropdown-item rounded-2 py-2 {{ request()->routeIs('contractors.*') ? 'active bg-light' : '' }}" href="{{ route('contractors.index') }}">
                            <i class="bi bi-person-gear me-2 text-muted"></i> {{ __('Contractor') }}
                        </a>
                        <a class="dropdown-item rounded-2 py-2 {{ request()->routeIs('vendors.*') ? 'active bg-light' : '' }}" href="{{ route('vendors.index') }}">
                            <i class="bi bi-shop me-2 text-muted"></i> {{ __('Vendor') }}
                        </a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('events') ? 'active fw-bold' : '' }}" href="{{ route('events') }}">
                        <i class="bi bi-calendar-event me-1"></i> {{ __('Events') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('courses') ? 'active fw-bold' : '' }}" href="{{ route('courses') }}">
                        <i class="bi bi-mortarboard me-1"></i> {{ __('Education') }}
                    </a>
                </li>
                @if(auth()->user()->isActiveMember())

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('business.manage') ? 'active fw-bold' : '' }}" href="{{ route('business.manage') }}">
                            <i class="bi bi-briefcase me-1"></i> {{ __('My Business') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('payments.*') ? 'active fw-bold' : '' }}" href="{{ route('payments.history') }}">
                            <i class="bi bi-credit-card me-1"></i> {{ __('Payments') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('wallet.*') ? 'active fw-bold' : '' }}" href="{{ route('wallet.index') }}">
                            <i class="bi bi-wallet2 me-1"></i> {{ __('My Wallet') }}
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('complaints.*') ? 'active fw-bold' : '' }}" href="{{ route('complaints.index') }}">
                        <i class="bi bi-exclamation-octagon me-1"></i> {{ __('Complaints') }}
                    </a>
                </li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto align-items-center gap-lg-3">
                @auth
                    @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin'))
                        <li class="nav-item">
                            <a class="nav-link text-primary fw-bold" href="{{ url('/admin') }}">
                                <i class="bi bi-shield-lock me-1"></i> {{ __('Admin') }}
                            </a>
                        </li>
                    @endif

                    <li class="nav-item dropdown me-2">
                        <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bell fs-5"></i>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-3 p-0" style="border-radius: 12px; min-width: 300px;">
                            <div class="p-3 border-b bg-light rounded-top-2">
                                <h6 class="mb-0">Notifications</h6>
                            </div>
                            <div class="max-h-300 overflow-y-auto" style="max-height: 350px; overflow-y: auto;">
                                @forelse(auth()->user()->unreadNotifications as $notification)
                                    <a href="{{ $notification->data['action_url'] ?? '#' }}" class="dropdown-item py-3 border-bottom whitespace-normal">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                                                <i class="bi bi-chat-dots text-primary"></i>
                                            </div>
                                            <div>
                                                <p class="mb-1 text-wrap" style="font-size: 0.9rem;">{{ $notification->data['message'] }}</p>
                                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-4 text-center text-muted italic">
                                        No new notifications
                                    </div>
                                @endforelse
                            </div>
                            <div class="p-2 border-top text-center">
                                <a href="#" class="text-xs text-primary text-decoration-none">View All</a>
                            </div>
                        </div>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center gap-2 bg-light rounded-pill px-3 py-2" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.8rem;">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="text-dark">{{ Auth::user()->name }}</span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-3 p-2" style="border-radius: 12px; min-width: 200px;" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item rounded-2 py-2" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person me-2 text-muted"></i> {{ __('My Profile') }}
                            </a>
                            <a class="dropdown-item rounded-2 py-2" href="{{ route('wallet.index') }}">
                                <i class="bi bi-wallet2 me-2 text-muted"></i> {{ __('My Wallet') }}
                            </a>
                            <a class="dropdown-item rounded-2 py-2" href="{{ route('profile.password') }}">
                                <i class="bi bi-key me-2 text-muted"></i> {{ __('Security') }}
                            </a>

                            <div class="dropdown-divider opacity-50"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item rounded-2 py-2 text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> {{ __('Log Out') }}
                                </button>
                            </form>
                        </div>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link px-3" href="{{ route('login') }}">{{ __('Log in') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm ms-lg-2 rounded-pill px-4" href="{{ route('register') }}">{{ __('Become a Member') }}</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
