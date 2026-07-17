@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp

@extends('layouts.front')

@section('title', 'Your Entry Pass - ' . $event->title)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            {{-- Ticket Card --}}
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden ticket-card mx-auto" style="max-width: 450px;">
                
                {{-- Header --}}
                <div class="card-header bg-dark text-white text-center py-4 border-0 position-relative">
                    <img src="{{ asset('assets/pwoa-logo.png') }}" alt="PWOA Logo" class="mb-2" style="height: 40px; filter: brightness(0) invert(1);">
                    <h5 class="mb-0 fw-bold tracking-widest text-uppercase small opacity-75">Your Entry Pass</h5>
                    <p class="small mb-0 opacity-50">Show this at event entry</p>
                </div>

                <div class="card-body p-0">
                    {{-- QR Code Section --}}
                    <div class="text-center py-5 bg-white">
                        <div class="d-inline-block p-3 border rounded-4 shadow-sm bg-light">
                            {!! QrCode::size(200)->margin(1)->generate(json_encode([
                                'ticket_id' => $attendee->ticket_id,
                                'token' => $attendee->token
                            ])) !!}
                        </div>
                        <div class="mt-2">
                            <span id="status-badge" class="badge {{ $attendee->status === 'valid' ? 'bg-success' : 'bg-danger' }} rounded-pill px-3 py-2 text-uppercase tracking-wider">
                                {{ $attendee->status === 'valid' ? 'Valid Ticket' : 'Used / Invalid' }}
                            </span>
                        </div>
                        
                        {{-- Countdown Timer --}}
                        @if($attendee->status === 'valid')
                            <div class="mt-3" id="expiry-timer-container">
                                <div class="small fw-bold text-uppercase text-muted mb-1" style="font-size: 0.65rem;">Ticket Expires In</div>
                                <div id="expiry-timer" class="h5 fw-black text-warning mb-0 font-monospace">
                                    --:--:--
                                </div>
                            </div>
                        @endif

                        <div class="mt-2 font-monospace text-muted small">
                            ID: {{ $attendee->ticket_id }}
                        </div>
                    </div>

                    {{-- Dashed Divider --}}
                    <div class="ticket-divider">
                        <div class="circle circle-left"></div>
                        <div class="circle circle-right"></div>
                    </div>

                    {{-- Details Section --}}
                    <div class="p-4 p-md-5 bg-white">
                        
                        {{-- Attendee --}}
                        <div class="text-center mb-4">
                            <label class="text-muted small fw-bold text-uppercase tracking-widest mb-1 d-block">Pass Holder</label>
                            <h3 class="fw-black text-dark mb-0">{{ strtoupper(Auth::user()->name) }}</h3>
                        </div>

                        {{-- Event Info --}}
                        <div class="bg-light rounded-4 p-4">
                            <h5 class="fw-bold text-primary mb-3 text-center">{{ $event->title }}</h5>
                            
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="text-muted d-block small fw-bold text-uppercase tracking-wider">Date</label>
                                    <span class="fw-bold d-block">{{ $event->starts_at->format('M d, Y') }}</span>
                                </div>
                                <div class="col-6 text-end">
                                    <label class="text-muted d-block small fw-bold text-uppercase tracking-wider">Time</label>
                                    <span class="fw-bold d-block">{{ $event->starts_at->format('h:i A') }}</span>
                                </div>
                                <div class="col-12 mt-3 pt-3 border-top">
                                    <label class="text-muted d-block small fw-bold text-uppercase tracking-wider">Location</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-geo-alt-fill text-danger"></i>
                                        <span class="fw-bold text-break">{{ $event->location ?? 'Location not available' }}</span>
                                    </div>
                                    <div class="mt-2">
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($event->location ?? '') }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">
                                            <i class="bi bi-map me-1"></i> View on Map
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="mt-4 pt-4 border-top text-center">
                            <p class="text-muted small mb-0 tracking-widest text-uppercase" style="font-size: 0.7rem;">Powered by PWOA</p>
                        </div>
                    </div>
                </div>

                {{-- Card Footer Actions --}}
                <div class="card-footer bg-light p-3 border-0 d-flex flex-column flex-sm-row gap-2">
                    <a href="{{ route('events.ticket.pdf', [$event->slug, $attendee->ticket_id]) }}" class="btn btn-outline-primary flex-grow-1 rounded-pill fw-bold">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-dark flex-grow-1 rounded-pill fw-bold">
                        <i class="bi bi-printer me-1"></i> Print Ticket
                    </button>
                    <a href="{{ route('events.show', $event->slug) }}" class="btn btn-primary flex-grow-1 rounded-pill fw-bold">
                        <i class="bi bi-arrow-left me-1"></i> Back to Event
                    </a>
                </div>
            </div>

            @if($attendee->status === 'used')
                <div class="alert alert-danger mt-4 rounded-4 shadow-sm border-0 small text-center">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Checked in on {{ $attendee->checked_in_at->format('M d, Y \a\t h:i A') }}
                </div>
            @endif

        </div>
    </div>
</div>

<style>
    .ticket-card {
        border-radius: 2rem !important;
    }
    
    .tracking-widest {
        letter-spacing: 0.2em;
    }

    .fw-black {
        font-weight: 900;
    }

    .ticket-divider {
        position: relative;
        height: 1px;
        border-top: 2px dashed #dee2e6;
        margin: 0 1.5rem;
    }

    .circle {
        position: absolute;
        top: 50%;
        width: 30px;
        height: 30px;
        background-color: #f8f9fa;
        border-radius: 50%;
        transform: translateY(-50%);
        border: 1px solid #dee2e6;
        z-index: 2;
    }

    .circle-left {
        left: -46px;
    }

    .circle-right {
        right: -46px;
    }

    @media (max-width: 576px) {
        .circle-left { left: -31px; }
        .circle-right { right: -31px; }
        .ticket-divider { margin: 0 1rem; }
    }

    @media print {
        body * {
            visibility: hidden;
        }
        .container, .container * {
            visibility: visible;
        }
        .container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .btn, .card-footer, .alert, .navbar, footer {
            display: none !important;
        }
        .ticket-card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            margin-top: 0 !important;
        }
        .circle {
            background-color: white !important;
        }
    }
</style>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const expiresAt = {{ $attendee->expires_at->timestamp * 1000 }};
        const timerElement = document.getElementById('expiry-timer');
        const badgeElement = document.getElementById('status-badge');
        
        if (!timerElement) return;

        function updateTimer() {
            const now = new Date().getTime();
            const distance = expiresAt - now;

            if (distance < 0) {
                clearInterval(timerInterval);
                timerElement.innerHTML = "EXPIRED";
                timerElement.classList.remove('text-warning');
                timerElement.classList.add('text-danger');
                
                if (badgeElement) {
                    badgeElement.innerHTML = "Expired";
                    badgeElement.classList.remove('bg-success');
                    badgeElement.classList.add('bg-danger');
                }
                return;
            }

            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            const formattedTime = 
                String(hours).padStart(2, '0') + ":" + 
                String(minutes).padStart(2, '0') + ":" + 
                String(seconds).padStart(2, '0');

            timerElement.innerHTML = formattedTime;
        }

        const timerInterval = setInterval(updateTimer, 1000);
        updateTimer();
    });
</script>
@endpush
