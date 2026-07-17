@php
    $hasPremiumAccess = auth()->check() && auth()->user()->hasPremiumAccess();
    $isActiveMember = auth()->check() && auth()->user()->isActiveMember();
@endphp
<div class="row g-4">
    <!-- Welcome Header -->
    <div class="col-12">
        <div class="card border-0 glass-card mb-2 overflow-hidden"
            style="background: linear-gradient(105deg, #ffffff 0%, #f7faff 100%);">
            <div class="card-body p-4 p-lg-5">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-3 fw-bold">Member
                            Dashboard</span>
                        <h1 class="display-5 fw-bold mb-3 ls-tight">Hello, {{ auth()->user()->name }}!</h1>
                        <p class="lead text-muted mb-4">Welcome to your professional command center. Explore the
                            marketplace, join upcoming events, and expand your expertise.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ url('/') }}" target="_blank" class="btn btn-dark shadow-sm px-4">
                                <i class="bi bi-globe me-2"></i> Visit Website
                            </a>
                            <a href="{{ route('directory') }}" class="btn btn-primary shadow-sm px-4">
                                <i class="bi bi-search me-2"></i> Explore Directory
                            </a>
                            <a href="{{ route('profile.edit') }}"
                                class="btn btn-outline-secondary px-4 d-flex align-items-center">
                                <i class="bi bi-person me-2"></i> Account Settings
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-5 d-none d-lg-block text-end">
                        <div class="position-relative d-inline-block">
                            <div class="bg-primary rounded-4 opacity-10 position-absolute"
                                style="top: -20px; right: -20px; width: 100%; height: 100%; transform: rotate(5deg);">
                            </div>
                            <i class="bi bi-rocket-takeoff display-1 text-primary position-relative"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!auth()->user()->isActiveMember())
        <div class="col-12">
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center p-4 rounded-4 bg-white"
                role="alert">
                <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center me-4"
                    style="width: 60px; height: 60px; min-width: 60px;">
                    <i class="bi bi-shield-lock-fill fs-3"></i>
                </div>
                <div>
                    <h5 class="mb-1 fw-bold">Upgrade Your Experience</h5>
                    <p class="mb-0 text-muted">Your current plan has limited access. <a
                            href="{{ route('membership.subscribe_form') }}"
                            class="text-primary fw-bold text-decoration-none border-bottom border-primary">Unlock the
                            Premium Marketplace & Education Center today.</a></p>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Stats Grid -->
    <div class="col-xl-4 col-md-6">
        <div class="card border-0 shadow-sm glass-card h-100 p-2 hover-scale"
            onclick="window.location='{{ $isActiveMember ? route('business.manage') : route('membership.subscribe_form') }}'" style="cursor: pointer;">
            <div class="card-body d-flex align-items-center gap-4">
                <div class="bg-primary bg-gradient rounded-4 text-white d-flex align-items-center justify-content-center"
                    style="width: 70px; height: 70px; min-width: 70px;">
                    <i class="bi bi-building fs-2"></i>
                </div>
                <div>
                    <p class="text-muted small text-uppercase fw-bold mb-1 ls-wide">Business Status</p>
                    <h4 class="fw-bold mb-0 text-capitalize">{{ $stats['business_status'] }}</h4>
                </div>
                <div class="ms-auto">
                    @if(!$isActiveMember)
                        <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1 small fw-bold">
                            <i class="bi bi-lock-fill"></i> Upgrade
                        </span>
                    @else
                        <i class="bi bi-chevron-right text-muted"></i>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{--
    <div class="col-xl-4 col-md-6">
        <div class="card border-0 shadow-sm glass-card h-100 p-2 hover-scale"
            onclick="window.location='{{ $hasPremiumAccess ? route('courses') : route('wash.upgrade') }}'" style="cursor: pointer;">
            <div class="card-body d-flex align-items-center gap-4">
                <div class="bg-success bg-gradient rounded-4 text-white d-flex align-items-center justify-content-center"
                    style="width: 70px; height: 70px; min-width: 70px;">
                    <i class="bi bi-mortarboard fs-2"></i>
                </div>
                <div>
                    <p class="text-muted small text-uppercase fw-bold mb-1 ls-wide">Learn</p>
                    <h4 class="fw-bold mb-0">{{ $stats['total_courses'] }} Courses</h4>
                </div>
                <div class="ms-auto">
                    @if(!$hasPremiumAccess)
                        <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1 small fw-bold">
                            <i class="bi bi-lock-fill"></i> Upgrade
                        </span>
                    @else
                        <i class="bi bi-chevron-right text-muted"></i>
                    @endif
                </div>
            </div>
        </div>
    </div>
    --}}

    <div class="col-xl-4 col-md-6">
        <div class="card border-0 shadow-sm glass-card h-100 p-2 hover-scale"
            onclick="window.location='{{ $hasPremiumAccess ? route('events') : route('wash.upgrade') }}'" style="cursor: pointer;">
            <div class="card-body d-flex align-items-center gap-4">
                <div class="bg-info bg-gradient rounded-4 text-white d-flex align-items-center justify-content-center"
                    style="width: 70px; height: 70px; min-width: 70px;">
                    <i class="bi bi-calendar3 fs-2"></i>
                </div>
                <div>
                    <p class="text-muted small text-uppercase fw-bold mb-1 ls-wide">Events</p>
                    <h4 class="fw-bold mb-0">{{ $stats['upcoming_events'] }} Upcoming</h4>
                </div>
                <div class="ms-auto">
                    @if(!$hasPremiumAccess)
                        <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1 small fw-bold">
                            <i class="bi bi-lock-fill"></i> Upgrade
                        </span>
                    @else
                        <i class="bi bi-chevron-right text-muted"></i>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="col-xl-4 col-md-6">
        <div class="card border-0 shadow-sm glass-card h-100 p-2 hover-scale"
            onclick="window.location='{{ $hasPremiumAccess ? route('courses') : route('wash.upgrade') }}'" style="cursor: pointer;">
            <div class="card-body d-flex align-items-center gap-4">
                <div class="bg-warning bg-gradient rounded-4 text-white d-flex align-items-center justify-content-center"
                    style="width: 70px; height: 70px; min-width: 70px;">
                    <i class="bi bi-journal-check fs-2"></i>
                </div>
                <div>
                    <p class="text-muted small text-uppercase fw-bold mb-1 ls-wide">My Courses</p>
                    <h4 class="fw-bold mb-0">{{ $stats['enrolled_courses'] }} Joined</h4>
                </div>
                <div class="ms-auto">
                    @if(!$hasPremiumAccess)
                        <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1 small fw-bold">
                            <i class="bi bi-lock-fill"></i> Upgrade
                        </span>
                    @else
                        <i class="bi bi-chevron-right text-muted"></i>
                    @endif
                </div>
            </div>
        </div>
    </div> --}}

    <div class="col-xl-4 col-md-6">
        <div class="card border-0 shadow-sm glass-card h-100 p-2 hover-scale"
             onclick="window.location='{{ $hasPremiumAccess ? route('certificates.index') : route('wash.upgrade') }}'" style="cursor: pointer;">
            <div class="card-body d-flex align-items-center gap-4">
                <div class="bg-danger bg-gradient rounded-4 text-white d-flex align-items-center justify-content-center"
                    style="width: 70px; height: 70px; min-width: 70px;">
                    <i class="bi bi-award fs-2"></i>
                </div>
                <div>
                    <p class="text-muted small text-uppercase fw-bold mb-1 ls-wide">Certificates</p>
                    <h4 class="fw-bold mb-0">{{ $stats['certificates_earned'] }} Earned</h4>
                </div>
                <div class="ms-auto">
                    @if(!$hasPremiumAccess)
                        <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1 small fw-bold">
                            <i class="bi bi-lock-fill"></i> Upgrade
                        </span>
                    @else
                        <i class="bi bi-chevron-right text-muted"></i>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card border-0 shadow-sm glass-card h-100 p-2 hover-scale"
             onclick="window.location='{{ $hasPremiumAccess ? route('complaints.index') : route('wash.upgrade') }}'" style="cursor: pointer;">
            <div class="card-body d-flex align-items-center gap-4">
                <div class="bg-gradient rounded-4 text-white d-flex align-items-center justify-content-center"
                    style="background: linear-gradient(135deg, #f35588 0%, #ff8fa3 100%) !important; width: 70px; height: 70px; min-width: 70px;">
                    <i class="bi bi-exclamation-octagon fs-2"></i>
                </div>
                <div>
                    <p class="text-muted small text-uppercase fw-bold mb-1 ls-wide">Complaints</p>
                    <h4 class="fw-bold mb-0">{{ $stats['complaints_filed'] }} Filed</h4>
                </div>
                <div class="ms-auto">
                    @if(!$hasPremiumAccess)
                        <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1 small fw-bold">
                            <i class="bi bi-lock-fill"></i> Upgrade
                        </span>
                    @else
                        <i class="bi bi-chevron-right text-muted"></i>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card border-0 shadow-sm glass-card h-100 p-2 hover-scale"
             onclick="window.location='{{ $hasPremiumAccess ? '#nft-collection' : route('wash.upgrade') }}'" style="cursor: pointer;">
            <div class="card-body d-flex align-items-center gap-4">
                <div class="bg-primary bg-gradient rounded-4 text-white d-flex align-items-center justify-content-center"
                    style="width: 70px; height: 70px; min-width: 70px;">
                    <i class="bi bi-ticket-perforated fs-2"></i>
                </div>
                <div>
                    <p class="text-muted small text-uppercase fw-bold mb-1 ls-wide">NFT Collection</p>
                    <h4 class="fw-bold mb-0">{{ $stats['tickets_owned'] }} Tickets</h4>
                </div>
                <div class="ms-auto">
                    @if(!$hasPremiumAccess)
                        <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1 small fw-bold">
                            <i class="bi bi-lock-fill"></i> Upgrade
                        </span>
                    @else
                        <i class="bi bi-chevron-down text-muted"></i>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Wallet Status Card -->
    <div class="col-xl-4 col-md-6">
        <div class="card border-0 shadow-sm glass-card h-100 p-2 hover-scale"
            onclick="window.location='{{ route('wallet.index') }}'" style="cursor: pointer;">
            <div class="card-body d-flex align-items-center gap-4">
                <div class="bg-dark bg-gradient rounded-4 text-white d-flex align-items-center justify-content-center"
                    style="width: 70px; height: 70px; min-width: 70px;">
                    <i class="bi bi-wallet2 fs-2"></i>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <p class="text-muted small text-uppercase fw-bold mb-1 ls-wide">XRPL Wallet</p>
                    @if($wallet)
                        <h4 class="fw-bold mb-0 text-truncate text-primary" style="font-size: 0.9rem;"
                            title="{{ $wallet->address }}">
                            {{ $wallet->address }}
                        </h4>
                    @else
                        <button wire:click.stop="createWallet" wire:loading.attr="disabled"
                            class="btn btn-sm btn-primary mt-1">
                            <span wire:loading.remove wire:target="createWallet">Generate Wallet</span>
                            <span wire:loading wire:target="createWallet">Creating...</span>
                        </button>
                    @endif
                </div>
                @if($wallet)
                    <div class="ms-auto">
                        <i class="bi bi-chevron-right text-muted"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Token Transactions -->
    <div class="col-12 mt-4">
        <div class="card border-0 shadow-sm glass-card">
            <div class="card-header bg-transparent border-0 p-4 pb-0">
                <h5 class="fw-bold mb-0">Recent $WASH Token Transactions</h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light border-0">
                            <tr>
                                <th class="border-0 rounded-start">Date</th>
                                <th class="border-0">Amount</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Transaction Hash</th>
                                <th class="border-0 rounded-end">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tokenTransactions as $tx)
                                <tr>
                                    <td>{{ $tx->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <span class="fw-bold text-success">+{{ number_format($tx->amount, 2) }}
                                            {{ $tx->currency }}</span>
                                    </td>
                                    <td>
                                        @if($tx->status === 'success')
                                            <span class="badge bg-success-subtle text-success rounded-pill px-3">Success</span>
                                        @elseif($tx->status === 'pending')
                                            <span class="badge bg-warning-subtle text-warning rounded-pill px-3">Pending</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-3">Failed</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tx->tx_hash)
                                            <code class="small text-muted">{{ Str::limit($tx->tx_hash, 15) }}</code>
                                            <a href="{{ rtrim(config('services.xrpl.explorer_url', 'https://testnet.xrpl.org/transactions'), '/') }}/{{ $tx->tx_hash }}" target="_blank"
                                                class="ms-1 text-primary">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        @else
                                            <span class="text-muted small">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tx->error_message)
                                            <small class="text-danger"
                                                title="{{ $tx->error_message }}">{{ Str::limit($tx->error_message, 20) }}</small>
                                        @else
                                            <span class="text-muted small">Confirmed</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No recent transactions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- My NFT Ticket Collection -->
    <div class="col-12 mt-4" id="nft-collection">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="fw-bold mb-0">My NFT Ticket Collection</h5>
            <span class="badge bg-primary rounded-pill">{{ count($tickets) }} Assets</span>
        </div>

        <div class="row g-4">
            @forelse($tickets as $ticket)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm glass-card h-100 overflow-hidden hover-scale">
                        <div class="position-relative">
                            @if($ticket->event?->image_path)
                                <img src="{{ str_starts_with($ticket->event->image_path, 'http') ? $ticket->event->image_path : asset('storage/' . $ticket->event->image_path) }}"
                                    class="card-img-top" style="height: 180px; object-fit: cover;"
                                    alt="{{ $ticket->event->title }}">
                            @else
                                <div class="bg-secondary-subtle d-flex align-items-center justify-content-center card-img-top"
                                    style="height: 180px;">
                                    <i class="bi bi-image text-secondary fs-1"></i>
                                </div>
                            @endif
                            <div class="position-absolute top-0 end-0 m-2">
                                <span
                                    class="badge bg-dark rounded-pill shadow-sm">#{{ str_pad($ticket->ticket_number, 3, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <h6 class="fw-bold text-truncate mb-1">{{ $ticket->event?->title }}</h6>
                            <p class="text-muted small mb-1">
                                <i class="bi bi-calendar-event me-1"></i> {{ $ticket->event?->starts_at->format('M d, Y') }}
                            </p>
                            <p class="text-muted small mb-3">
                                <i class="bi bi-clock me-1"></i> Purchased On: {{ $ticket->created_at->format('M d, Y h:i A') }}
                            </p>

                            <div class="d-grid gap-2">
                                @php
                                    $attendee = \App\Models\EventAttendee::where('event_id', $ticket->event_id)
                                        ->where('user_id', auth()->id())
                                        ->first();
                                @endphp
                                @if($attendee)
                                    <a href="{{ route('events.ticket', [$ticket->event->slug, $attendee->ticket_id]) }}"
                                        class="btn btn-primary btn-sm rounded-3">
                                        <i class="bi bi-qr-code me-1"></i> View Pass
                                    </a>
                                    <a href="{{ route('events.ticket.pdf', [$ticket->event->slug, $attendee->ticket_id]) }}"
                                        class="btn btn-outline-primary btn-sm rounded-3">
                                        <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
                                    </a>
                                @endif
                                <a href="{{ str_replace('/account', '', config('services.xrpl.explorer_url')) }}/nft/{{ $ticket->nft_token_id }}"
                                    target="_blank" class="btn btn-outline-dark btn-sm rounded-3">
                                    <i class="bi bi-blockchain me-1"></i> Verify NFT
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm glass-card p-5 text-center">
                        <div class="mb-3 text-muted opacity-50">
                            <i class="bi bi-ticket-perforated display-1"></i>
                        </div>
                        <h5 class="fw-bold">No NFT Tickets Yet</h5>
                        <p class="text-muted">Explore our upcoming events to start your collection.</p>
                        <div class="mt-3">
                            @if($hasPremiumAccess)
                                <a href="{{ route('events') }}" class="btn btn-primary px-4">Browse Events</a>
                            @else
                                <a href="{{ route('wash.upgrade') }}" class="btn btn-warning px-4 fw-bold shadow-sm"><i class="bi bi-unlock-fill me-1"></i> Unlock Events &amp; Tickets</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>


</div>