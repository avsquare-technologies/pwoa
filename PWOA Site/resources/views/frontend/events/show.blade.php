@extends('layouts.front')

@section('title', $event->title)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($event->description), 160))

@section('content')

@php
    $loginUrl = \Illuminate\Support\Facades\Route::has('login')
        ? route('login', ['redirect' => route('events.show', $event->slug)])
        : url('/admin/login');
@endphp

{{-- Hero Banner --}}
<section class="position-relative" style="height: 350px;">
    @if($event->image_path)
        <img src="{{ str_starts_with($event->image_path, 'http') ? $event->image_path : Storage::url($event->image_path) }}"
            class="w-100 h-100 object-fit-cover position-absolute top-0 start-0" alt="{{ $event->title }}">
    @else
        <div class="w-100 h-100 bg-brand-gradient position-absolute top-0 start-0"></div>
    @endif
    <div class="position-absolute top-0 start-0 w-100 h-100"
        style="background: linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.7) 100%);"></div>
    <div class="container position-relative h-100 d-flex flex-column justify-content-end pb-4">
        <a href="{{ route('events.index') }}"
            class="text-white text-decoration-none mb-3 d-inline-flex align-items-center gap-1 small opacity-75">
            <i class="bi bi-arrow-left"></i> Back to events
        </a>
        <div class="d-flex flex-wrap gap-2 mb-3">
            <span class="badge bg-white text-dark rounded-pill px-3 py-1 shadow-sm">
                {{ $event->category?->name ?? 'General' }}
            </span>
            @if($event->price == 0)
                <span class="badge bg-success rounded-pill px-3 py-1">Free Event</span>
            @endif
            @if($event->is_free_for_members && $event->price > 0)
                <span class="badge bg-info rounded-pill px-3 py-1">Free for Members</span>
            @endif
        </div>
        <h1 class="display-5 fw-bold text-white mb-0">{{ $event->title }}</h1>
    </div>
</section>

@if($event->isEnded())
    <div class="bg-dark text-white py-2 text-center small fw-bold">
        <i class="bi bi-info-circle me-1"></i> This event has ended. You can no longer register for this event.
    </div>
@endif

<section class="py-4">
    <div class="container">
        <div class="row g-4 align-items-start">

            {{-- LEFT SIDE --}}
            <div class="col-lg-8">

                {{-- Quick Info Bar --}}
                <div class="card card-soft mb-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-sm-6 col-md-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 48px; height: 48px;">
                                        <i class="bi bi-calendar-event fs-5"></i>
                                    </div>
                                    <div class="text-truncate">
                                        <div class="small text-muted">Date</div>
                                        <div class="fw-bold">{{ optional($event->starts_at)->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-success-subtle text-success rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 48px; height: 48px;">
                                        <i class="bi bi-clock fs-5"></i>
                                    </div>
                                    <div class="text-truncate">
                                        <div class="small text-muted">Time</div>
                                        <div class="fw-bold">{{ optional($event->starts_at)->format('g:i A') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-warning-subtle text-warning rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 48px; height: 48px;">
                                        <i class="bi bi-geo-alt fs-5"></i>
                                    </div>
                                    <div class="text-truncate">
                                        <div class="small text-muted">Location</div>
                                        <div class="fw-bold">{{ $event->location ?? 'TBA' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                <div class="card card-soft mb-4 border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <h2 class="h4 fw-bold mb-3">About This Event</h2>
                        <div class="text-secondary lh-lg">
                            {!! $event->description !!}
                        </div>
                    </div>
                </div>

                {{-- Event Details --}}
                <div class="card card-soft border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 fw-bold mb-3">Event Details</h2>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="bi bi-calendar-range text-primary fs-5 mt-1"></i>
                                    <div>
                                        <div class="small text-muted">Start</div>
                                        <div class="fw-bold">{{ optional($event->starts_at)->format('M d, Y g:i A') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="bi bi-calendar-check text-primary fs-5 mt-1"></i>
                                    <div>
                                        <div class="small text-muted">End</div>
                                        <div class="fw-bold">{{ optional($event->ends_at)->format('M d, Y g:i A') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="bi bi-building text-primary fs-5 mt-1"></i>
                                    <div>
                                        <div class="small text-muted">Organizer</div>
                                        <div class="fw-bold">PWOA</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="bi bi-people text-primary fs-5 mt-1"></i>
                                    <div>
                                        <div class="small text-muted">Capacity</div>
                                        <div class="fw-bold">{{ $event->capacity ?? 'Open' }} seats</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT SIDE --}}
            <div class="col-lg-4">
                {{-- Register Card --}}
                <div class="card card-soft border-0 shadow-lg mb-4">
                    <div class="card-body p-4 text-center">
                        <h2 class="h5 fw-bold mb-3">Register Now</h2>
                        <p class="text-secondary small mb-4">
                            {{ $event->price == 0 ? 'This event is free. Reserve your spot today!' : 'Secure your ticket for this event.' }}
                        </p>
                        <div class="bg-light rounded-3 p-3 mb-4">
                            <div class="small text-muted mb-1">Price</div>
                            <div class="display-6 fw-bold text-primary">
                                {{ $event->price == 0 ? 'Free' : '$' . number_format($event->price, 2) }}
                            </div>
                            @if($event->is_free_for_members && $event->price > 0)
                                <div class="small text-success mt-1 fw-bold">
                                    <i class="bi bi-star-fill me-1"></i> Free for PWOA Members
                                </div>
                            @endif
                        </div>

                        @php
                            $remainingSeats = $event->remainingSeats();
                            $ticketsCount = $event->tickets()->count();
                            $percent = ($event->capacity > 0) ? ($remainingSeats / $event->capacity * 100) : 100;
                            $colorClass = $percent <= 20 ? 'text-danger' : ($percent <= 50 ? 'text-warning' : 'text-success');
                        @endphp

                        <div class="mb-4 text-center">
                            @if($event->isEnded())
                                <div class="badge bg-secondary rounded-pill px-3 py-2 w-100 mb-2">
                                    <i class="bi bi-clock-history me-1"></i> Event Closed
                                </div>
                                <div class="small text-muted">This event has already ended.</div>
                            @elseif($event->isSoldOut())
                                <div class="badge bg-danger rounded-pill px-3 py-2 w-100">
                                    <i class="bi bi-exclamation-octagon me-1"></i> Sold Out
                                </div>
                            @else
                                <div class="d-flex align-items-center justify-content-center gap-2 fw-bold small {{ $colorClass }}">
                                    <i class="bi bi-people-fill"></i>
                                    <span>
                                        @if($percent <= 20)
                                            Only {{ $remainingSeats }} seats left!
                                        @else
                                            Seats Remaining: {{ $remainingSeats }}
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>

                        @auth
                            <form method="POST" action="{{ route('events.purchase', $event->slug) }}">
                                @csrf
                                <input type="hidden" name="event_id" value="{{ $event->id }}">
                                
                                {{-- Quantity Selector --}}
                                <div class="mb-4">
                                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block">
                                        Number of Tickets
                                    </label>
                                    <div class="input-group input-group-lg justify-content-center" style="max-width: 200px; margin: 0 auto;">
                                        <button type="button" class="btn btn-outline-secondary border-end-0" onclick="changeQty(-1)">
                                            <i class="bi bi-dash-lg"></i>
                                        </button>
                                        <input type="number" name="quantity" id="ticket-qty" value="1" 
                                               min="1" max="{{ min(10, $remainingSeats) }}"
                                               class="form-control text-center fw-bold fs-4 border-secondary bg-white"
                                               readonly>
                                        <button type="button" class="btn btn-outline-secondary border-start-0" onclick="changeQty(1)">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </div>
                                    <div class="small text-muted mt-2">
                                        Max 10 tickets per order
                                    </div>
                                </div>

                                {{-- Order Summary --}}
                                <div class="bg-light rounded-3 p-3 mb-4">
                                    <div class="d-flex justify-content-between align-items-center small mb-1">
                                        <span class="text-muted">Unit Price:</span>
                                        <span class="fw-bold text-dark">${{ number_format($event->price, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Total:</span>
                                        <span class="h5 mb-0 fw-bold text-primary" id="total-display">${{ number_format($event->price, 2) }}</span>
                                    </div>
                                </div>

                                <button class="btn btn-brand w-100 rounded-pill fw-bold py-3 shadow-sm" type="submit" id="submit-btn" {{ ($event->isSoldOut() || $event->isEnded()) ? 'disabled' : '' }}>
                                    <i class="bi bi-ticket-perforated me-2"></i>
                                    <span id="btn-text">
                                        @if($event->isEnded())
                                            Event Closed
                                        @elseif($event->isSoldOut())
                                            Sold Out
                                        @else
                                            Get 1 Ticket
                                        @endif
                                    </span>
                                </button>
                            </form>
                        @else
                            <a href="{{ $loginUrl }}" class="btn btn-brand w-100 rounded-pill fw-bold py-3 {{ ($event->isSoldOut() || $event->isEnded()) ? 'disabled' : '' }}">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                @if($event->isEnded())
                                    Event Closed
                                @elseif($event->isSoldOut())
                                    Sold Out
                                @else
                                    Login to Register
                                @endif
                            </a>
                        @endauth

                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-shield-check me-1"></i> Secure registration
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Map Integration --}}
                <div class="card card-soft border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h3 class="h5 fw-bold mb-3">Event Location</h3>
                        @if($event->latitude && $event->longitude)
                            <div class="rounded-3 overflow-hidden mb-3" style="height: 250px;">
                                <iframe width="100%" height="100%" frameborder="0" style="border:0"
                                    src="https://www.google.com/maps?q={{ $event->latitude }},{{ $event->longitude }}&output=embed" 
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $event->latitude }},{{ $event->longitude }}" 
                               target="_blank" class="btn btn-outline-brand w-100 rounded-pill d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-geo-alt"></i> Get Directions
                            </a>
                        @elseif($event->location)
                            <div class="rounded-3 overflow-hidden mb-3" style="height: 250px;">
                                <iframe width="100%" height="100%" frameborder="0" style="border:0"
                                    src="https://www.google.com/maps?q={{ urlencode($event->location) }}&output=embed" 
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($event->location) }}" 
                               target="_blank" class="btn btn-outline-brand w-100 rounded-pill d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-geo-alt"></i> Get Directions
                            </a>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-geo-alt fs-2 mb-2 d-block"></i>
                                <p class="small mb-0">Location details not available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
<script>
    function changeQty(delta) {
        const input = document.getElementById('ticket-qty');
        const btnText = document.getElementById('btn-text');
        const totalDisplay = document.getElementById('total-display');
        const price = {{ $event->price ?? 0 }};
        const max = {{ min(10, $remainingSeats ?? 0) }};
        
        let newValue = parseInt(input.value) + delta;
        
        if (newValue >= 1 && newValue <= max) {
            input.value = newValue;
            
            // Update button text
            if (btnText && !btnText.innerText.includes('Closed') && !btnText.innerText.includes('Sold')) {
                btnText.innerText = newValue > 1 ? `Get ${newValue} Tickets` : `Get 1 Ticket`;
            }
            
            // Update total price
            if (totalDisplay) {
                totalDisplay.innerText = '$' + (newValue * price).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
        }
    }
</script>
@endpush