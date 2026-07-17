@extends('layouts.front')

@section('title', 'Order Summary - ' . $event->title)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            {{-- Order Status Header --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-4 p-md-5 text-center">
                    @if($order->status->value === 'completed')
                        <div class="bg-success-subtle text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-check-lg display-4"></i>
                        </div>
                        <h1 class="h3 fw-bold">Order Completed!</h1>
                        <p class="text-secondary">Successfully secured {{ $order->quantity }} tickets for {{ $event->title }}.</p>
                    @elseif($order->status->value === 'partial')
                        <div class="bg-warning-subtle text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-exclamation-triangle display-4"></i>
                        </div>
                        <h1 class="h3 fw-bold">Partial Success</h1>
                        <p class="text-secondary">Some tickets were secured, but some transfers failed. Check details below.</p>
                    @else
                        <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem; border-width: 0.25em;"></div>
                        </div>
                        <h1 class="h3 fw-bold">Order Processing</h1>
                        <p class="text-secondary">We are currently transferring your NFT tickets on the blockchain.</p>
                    @endif
                    
                    <div class="mt-4 pt-4 border-top">
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <label class="small text-muted d-block mb-1">Order ID</label>
                                <span class="fw-bold text-uppercase small">{{ substr($order->id, 0, 8) }}</span>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="small text-muted d-block mb-1">Quantity</label>
                                <span class="fw-bold">{{ $order->quantity }} Tickets</span>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="small text-muted d-block mb-1">Total Paid</label>
                                <span class="fw-bold text-primary">{{ number_format($order->total_amount, 2) }} {{ $order->currency }}</span>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="small text-muted d-block mb-1">Date</label>
                                <span class="fw-bold">{{ $order->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tickets List --}}
            <h4 class="fw-bold mb-4 px-2 text-center">Your Entry Passes</h4>
            <div class="d-flex flex-column gap-5 mb-5 align-items-center">
                @foreach($order->attendees as $attendee)
                    {{-- Full Sized Ticket (Exact Existing Design) --}}
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden ticket-card w-100" style="max-width: 450px;">
                        
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
                                    {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->margin(1)->generate(json_encode([
                                        'ticket_id' => $attendee->ticket_id,
                                        'token' => $attendee->token
                                    ])) !!}
                                </div>
                                <div class="mt-2">
                                    <span class="badge bg-success rounded-pill px-3 py-2 text-uppercase tracking-wider">
                                        Valid Ticket
                                    </span>
                                </div>
                                
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
                                    <h3 class="fw-black text-dark mb-0">{{ strtoupper(auth()->user()->name) }}</h3>
                                </div>

                                {{-- Event Info --}}
                                <div class="bg-light rounded-4 p-4">
                                    <h5 class="fw-bold text-primary mb-3 text-center">{{ $event->title }}</h5>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="text-muted d-block small fw-bold text-uppercase tracking-wider">Date</label>
                                            <span class="fw-bold d-block small">{{ $event->starts_at->format('M d, Y') }}</span>
                                        </div>
                                        <div class="col-6 text-end">
                                            <label class="text-muted d-block small fw-bold text-uppercase tracking-wider">Time</label>
                                            <span class="fw-bold d-block small">{{ $event->starts_at->format('h:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Action --}}
                        <div class="card-footer bg-light p-3 border-0">
                            <a href="{{ route('events.ticket', [$event->slug, $attendee->ticket_id]) }}" class="btn btn-primary w-100 rounded-pill fw-bold">
                                <i class="bi bi-fullscreen me-1"></i> Fullscreen View
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

                {{-- Failed Transfers --}}
                @foreach($order->transfers()->where('status', '!=', 'success')->get() as $transfer)
                    <div class="col-md-6">
                        <div class="card border-0 bg-white rounded-4 h-100 border-dashed position-relative overflow-hidden">
                            <div class="position-absolute top-0 start-0 w-100 h-100 bg-danger opacity-5"></div>
                            <div class="card-body p-4 text-center d-flex flex-column justify-content-center position-relative">
                                <div class="text-danger mb-3">
                                    <i class="bi bi-exclamation-octagon fs-1"></i>
                                </div>
                                <h6 class="fw-bold text-danger mb-2">Transfer Pending / Failed</h6>
                                <p class="small text-muted mb-4 px-3">We encountered an issue transferring this NFT to your wallet. Our team is working on it.</p>
                                <button class="btn btn-sm btn-outline-danger rounded-pill px-4 align-self-center fw-bold">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Retry
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Support Footer --}}
            <div class="text-center">
                <p class="text-muted small">Need help with your order? <a href="{{ route('contact') }}" class="text-primary">Contact PWOA Support</a></p>
                <a href="{{ route('events.index') }}" class="btn btn-link text-decoration-none text-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to All Events
                </a>
            </div>

        </div>
    </div>
</div>

<style>
    .border-dashed {
        border: 2px dashed #dee2e6 !important;
    }
    .ticket-card {
        border-radius: 1.5rem !important;
        transition: transform 0.2s ease;
    }
    .ticket-card:hover {
        transform: translateY(-5px);
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
        margin: 0 1rem;
    }
    .circle {
        position: absolute;
        top: 50%;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        transform: translateY(-50%);
        border: 1px solid #dee2e6;
        z-index: 2;
    }
    .circle-left {
        left: -24px;
    }
    .circle-right {
        right: -24px;
    }
</style>
@endsection
