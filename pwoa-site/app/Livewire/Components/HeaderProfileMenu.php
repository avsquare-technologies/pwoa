<?php

namespace App\Livewire\Components;

use Livewire\Component;

class HeaderProfileMenu extends Component
{
    public function render()
    {
        return <<<'HTML'
        <div class="dropdown">
            <button class="btn btn-link text-decoration-none dropdown-toggle d-flex align-items-center gap-2 p-0" type="button" data-bs-toggle="dropdown">
                <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border: 2px solid white; box-shadow: var(--ag-shadow);">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="d-none d-md-block text-start">
                    <p class="mb-0 fw-bold small text-dark lh-1">{{ auth()->user()->name }}</p>
                    <p class="mb-0 text-muted" style="font-size: 0.7rem;">Verified Member</p>
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-3 rounded-4 p-2">
                <li><a class="dropdown-item rounded-3 py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Profile Settings</a></li>
                <li><a class="dropdown-item rounded-3 py-2" href="{{ route('membership.status') }}"><i class="bi bi-star me-2"></i> Subscription</a></li>
                <li><hr class="dropdown-divider opacity-50"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item rounded-3 py-2 text-danger"><i class="bi bi-box-arrow-right me-2"></i> Log Out</button>
                    </form>
                </li>
            </ul>
        </div>
        HTML;
    }
}
