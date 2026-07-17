@php
    $hasAccess = auth()->check() && auth()->user()->hasPremiumAccess();
@endphp

<div class="mb-5 d-flex align-items-center">
    <img src="{{ asset('assets/pwoa-logo.png') }}" alt="PWOA Logo" style="height: 50px;">
</div>

<div class="nav flex-column gap-2 mb-auto">
    <a href="{{ route('dashboard') }}" class="nav-link-sidebar {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <span class="sidebar-item-left">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </span>
    </a>

    {{-- <a href="{{ route('directory') }}"
        class="nav-link-sidebar {{ request()->routeIs('directory') ? 'active' : '' }}">
        <i class="bi bi-shop"></i>
        <span>Business Directory</span>
    </a> --}}

    <a href="{{ $hasAccess ? route('events') : route('wash.upgrade') }}"
        class="nav-link-sidebar {{ request()->routeIs('events') ? 'active' : '' }} {{ !$hasAccess ? 'is-locked' : '' }}">
        <span class="sidebar-item-left">
            <i class="bi bi-calendar-event"></i>
            <span>Community Events</span>
        </span>
        @if(!$hasAccess)
            <span class="upgrade-badge ms-auto">
                <i class="bi bi-lock-fill"></i>
                <span>Upgrade</span>
            </span>
        @endif
    </a>

    {{-- <a href="{{ $hasAccess ? route('courses') : route('wash.upgrade') }}"
        class="nav-link-sidebar {{ request()->routeIs('courses') ? 'active' : '' }} {{ !$hasAccess ? 'is-locked' : '' }}">
        <span class="sidebar-item-left">
            <i class="bi bi-mortarboard"></i>
            <span>Learning Center</span>
        </span>
        @if(!$hasAccess)
            <span class="upgrade-badge ms-auto">
                <i class="bi bi-lock-fill"></i>
                <span>Upgrade</span>
            </span>
        @endif
    </a> --}}

    <a href="{{ $hasAccess ? route('certificates.index') : route('wash.upgrade') }}"
        class="nav-link-sidebar {{ request()->routeIs('certificates.*') ? 'active' : '' }} {{ !$hasAccess ? 'is-locked' : '' }}">
        <span class="sidebar-item-left">
            <i class="bi bi-award"></i>
            <span>My Certificates</span>
        </span>
        @if(!$hasAccess)
            <span class="upgrade-badge ms-auto">
                <i class="bi bi-lock-fill"></i>
                <span>Upgrade</span>
            </span>
        @endif
    </a>

    <a href="{{ $hasAccess ? route('complaints.index') : route('wash.upgrade') }}"
        class="nav-link-sidebar {{ request()->routeIs('complaints.*') ? 'active' : '' }} {{ !$hasAccess ? 'is-locked' : '' }}">
        <span class="sidebar-item-left">
            <i class="bi bi-exclamation-octagon"></i>
            <span>My Complaints</span>
        </span>
        @if(!$hasAccess)
            <span class="upgrade-badge ms-auto">
                <i class="bi bi-lock-fill"></i>
                <span>Upgrade</span>
            </span>
        @endif
    </a>

    <a href="{{ route('wallet.index') }}"
        class="nav-link-sidebar {{ request()->routeIs('wallet.*') ? 'active' : '' }}">
        <span class="sidebar-item-left">
            <i class="bi bi-wallet2"></i>
            <span>Wallet</span>
        </span>
    </a>


    @if(auth()->user()->isActiveMember())
        <div class="sidebar-divider my-3"></div>
        <p class="small fw-bold text-muted text-uppercase mb-2 px-3">Manage</p>

        <a href="{{ route('business.manage') }}"
            class="nav-link-sidebar {{ request()->routeIs('business.manage') ? 'active' : '' }}">
            <span class="sidebar-item-left">
                <i class="bi bi-briefcase"></i>
                <span>My Business</span>
            </span>
        </a>

        <a href="{{ route('membership.status') }}"
            class="nav-link-sidebar {{ request()->routeIs('membership.status') ? 'active' : '' }}">
            <span class="sidebar-item-left">
                <i class="bi bi-person-badge"></i>
                <span>Membership Status</span>
            </span>
        </a>

        <a href="{{ route('payments.history') }}"
            class="nav-link-sidebar {{ request()->routeIs('payments.history') ? 'active' : '' }}">
            <span class="sidebar-item-left">
                <i class="bi bi-credit-card-2-front"></i>
                <span>Billing History</span>
            </span>
        </a>
    @endif
</div>

<div class="mt-5 pt-5 border-top border-light">
    <a href="{{ route('profile.edit') }}"
        class="nav-link-sidebar {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
        <span class="sidebar-item-left">
            <i class="bi bi-gear"></i>
            <span>Account Settings</span>
        </span>
    </a>
</div>

<style>
    .nav-link-sidebar {
        display: flex;
        align-items: center;
        text-decoration: none;
    }

    .sidebar-item-left {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .upgrade-badge {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        padding: 0.4rem 1rem;
        /* background: linear-gradient(180deg, #f59e0b 0%, #d97706 100%); */
        color: #0095d7;
        border: none;
        border-radius: 50rem;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(217, 119, 6, 0.4);
        z-index: 10;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .upgrade-badge i {
        font-size: 0.75rem;
    }

    .nav-link-sidebar {
        position: relative;
        /* Base for floating badge */
        display: flex;
        align-items: center;
        padding: 0.85rem 1.25rem;
        border-radius: 12px;
        margin-bottom: 6px;
        transition: all 0.3s ease;
        text-decoration: none;
        width: 100%;
        overflow: hidden;
    }

    .nav-link-sidebar:hover {
        background-color: #f8fafc;
        color: var(--ag-primary);
    }

    .nav-link-sidebar.is-locked {
        background-color: #fcfdfe;
        border: 1px solid rgba(0, 0, 0, 0.02);
    }

    .nav-link-sidebar.is-locked .sidebar-item-left {
        opacity: 0.25;
        /* Heavily fade the background content */
        filter: blur(0.5px);
        transition: all 0.3s ease;
    }

    .nav-link-sidebar.is-locked:hover {
        background-color: #fffaf0;
        border-color: #fde68a;
    }

    .nav-link-sidebar.is-locked:hover .upgrade-badge {
        background: linear-gradient(180deg, #fbbf24 0%, #f59e0b 100%);
        transform: translate(-50%, -50%) scale(1.1);
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.5);
    }

    .nav-link-sidebar.is-locked:hover .sidebar-item-left {
        opacity: 0.4;
        filter: blur(0px);
    }
</style>