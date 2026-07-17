@extends('layouts.front')

@section('title', 'Events')

@section('content')
<section class="section-hero bg-accent-gradient">
    <div class="container text-center">
        <span class="badge rounded-pill badge-soft-primary px-3 py-2 mb-3">Events</span>
        <h1 class="display-5 fw-bold mb-3">Conferences, workshops, networking, and member events.</h1>
        <p class="lead text-white-50 mx-auto" style="max-width: 760px;">PWOA events help members learn, connect, and grow with stronger systems and better industry relationships.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 fw-bold mb-1">Upcoming Events</h2>
                <p class="text-muted mb-0">Don't miss these opportunities to learn and network</p>
            </div>
            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fs-6">
                {{ $upcomingEvents->count() }} upcoming
            </span>
        </div>
        <div class="row g-4 mb-5">
            @forelse($upcomingEvents as $event)
            <div class="col-lg-4">
                <div class="card card-soft h-100 hover-scale overflow-hidden border-0 shadow-sm">
                    {{-- Event Image --}}
                    <div class="position-relative" style="height: 200px;">
                        @if($event->image_path)
                            <img src="{{ str_starts_with($event->image_path, 'http') ? $event->image_path : Storage::url($event->image_path) }}"
                                 class="w-100 h-100 object-fit-cover" alt="{{ $event->title }}">
                        @else
                            <div class="w-100 h-100 bg-brand-gradient d-flex align-items-center justify-content-center">
                                <i class="bi bi-calendar-event text-white" style="font-size: 3rem; opacity: 0.3;"></i>
                            </div>
                        @endif
                        
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, transparent 50%, rgba(0,0,0,0.7) 100%);"></div>

                        {{-- Date Badge --}}
                        <div class="position-absolute top-0 start-0 p-3">
                            <div class="bg-white rounded-3 text-center shadow-sm px-3 py-2">
                                <div class="fw-bold text-primary lh-1" style="font-size: 1.4rem;">{{ optional($event->starts_at)->format('d') }}</div>
                                <div class="text-muted small text-uppercase fw-bold">{{ optional($event->starts_at)->format('M') }}</div>
                            </div>
                        </div>

                        {{-- Category Badge --}}
                        <div class="position-absolute top-0 end-0 p-3">
                            <span class="badge bg-white text-dark shadow-sm rounded-pill small px-2 py-1">
                                {{ $event->category?->name ?? 'General' }}
                            </span>
                        </div>

                        {{-- Price Badge --}}
                        <div class="position-absolute bottom-0 end-0 p-3">
                            @if($event->price == 0)
                                <span class="badge bg-success rounded-pill px-3 py-2 fw-bold">Free</span>
                            @else
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fw-bold">${{ number_format($event->price, 0) }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body p-4 d-flex flex-column">
                        <h3 class="h5 fw-bold mb-2">{{ $event->title }}</h3>

                        <div class="d-flex flex-column gap-2 small text-muted mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-clock"></i>
                                <span>{{ optional($event->starts_at)->format('M d, Y • g:i A') }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-geo-alt"></i>
                                <span>{{ $event->location ?? 'TBA' }}</span>
                            </div>
                            @if($event->capacity)
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-people"></i>
                                <span>{{ $event->capacity }} seats available</span>
                            </div>
                            @endif
                        </div>

                        @if($event->is_free_for_members && $event->price > 0)
                            <div class="mb-3">
                                <span class="badge bg-success-subtle text-success border-0 rounded-pill px-3 py-1">
                                    <i class="bi bi-star-fill me-1"></i> Free for Members
                                </span>
                            </div>
                        @endif

                        <a href="{{ route('events.show', $event->slug) }}" class="btn btn-brand w-100 rounded-pill fw-bold mt-auto">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="text-muted opacity-25 mb-3"><i class="bi bi-calendar-x" style="font-size: 4rem;"></i></div>
                    <h4 class="fw-bold">No upcoming events</h4>
                    <p class="text-muted">Check back soon for new events and workshops.</p>
                </div>
            </div>
            @endforelse
        </div>

        {{-- Past Events --}}
        @if($pastEvents->count())
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 fw-bold mb-1">Past Events</h2>
                <p class="text-muted mb-0">Catch up on what you might have missed</p>
            </div>
        </div>
        <div class="row g-4">
            @foreach($pastEvents as $event)
            <div class="col-md-6 col-xl-3">
                <a href="{{ route('events.show', $event->slug) }}" class="text-decoration-none">
                    <div class="card card-soft h-100 overflow-hidden border-0 shadow-sm" style="opacity: 0.85;">
                        <div class="position-relative" style="height: 120px;">
                            @if($event->image_path)
                                <img src="{{ str_starts_with($event->image_path, 'http') ? $event->image_path : Storage::url($event->image_path) }}"
                                     class="w-100 h-100 object-fit-cover" style="filter: grayscale(40%);" alt="{{ $event->title }}">
                            @else
                                <div class="w-100 h-100 bg-secondary"></div>
                            @endif
                            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.3);"></div>
                            <div class="position-absolute top-0 start-0 p-2">
                                <span class="badge bg-dark bg-opacity-75 rounded-pill small">{{ optional($event->starts_at)->format('M Y') }}</span>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <h3 class="h6 mb-1 fw-bold text-dark">{{ $event->title }}</h3>
                            <p class="text-muted small mb-0">{{ $event->category?->name ?? 'General' }}</p>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endsection
