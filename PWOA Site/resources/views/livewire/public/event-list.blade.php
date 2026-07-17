<x-slot name="header">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0 text-dark fw-bold">
            Community Events
        </h2>
        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 d-none d-md-inline-block">
            {{ $events->total() }} Upcoming Events
        </span>
    </div>
</x-slot>

<div class="row g-4">
    <!-- Filters Sidebar -->
    <div class="col-lg-3">
        <div class="card border-0 glass-card sticky-top" style="top: 100px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Event Filters</h5>
                    <div wire:loading wire:target="search, category_id, time_filter" class="spinner-border spinner-border-sm text-primary" role="status"></div>
                </div>
                
                <!-- Search -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">Search Events</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search py-1"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control bg-light border-0" placeholder="e.g. Conference, Meetup...">
                    </div>
                </div>

                <!-- Time Range -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">Time Period</label>
                    <div class="d-flex flex-column gap-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model.live="time_filter" value="upcoming" id="time_upcoming">
                            <label class="form-check-label small" for="time_upcoming">Upcoming Events</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model.live="time_filter" value="today" id="time_today">
                            <label class="form-check-label small" for="time_today">Today's Events</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model.live="time_filter" value="past" id="time_past">
                            <label class="form-check-label small" for="time_past">Past Events</label>
                        </div>
                    </div>
                </div>

                <!-- Categories -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">Event Category</label>
                    <div class="d-flex flex-column gap-2" style="max-height: 400px; overflow-y: auto;">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model.live="category_id" value="" id="cat_all">
                            <label class="form-check-label small" for="cat_all">All Categories</label>
                        </div>
                        @foreach($categories as $cat)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" wire:model.live="category_id" value="{{ $cat->id }}" id="cat_{{ $cat->id }}">
                                <label class="form-check-label small" for="cat_{{ $cat->id }}">{{ $cat->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-grid gap-2 pt-2 border-top">
                    <button wire:click="$set('search', ''); $set('category_id', ''); $set('time_filter', 'upcoming');" class="btn btn-outline-secondary btn-sm rounded-3">
                        Reset Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Results Area -->
    <div class="col-lg-9 position-relative">
        @if(!$isSubscribed)
            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center z-3" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(4px); min-height: 500px;">
                <div class="card border-0 shadow-lg text-center p-5 max-w-md mx-auto" style="max-width: 450px;">
                    <div class="mb-4">
                        <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-calendar-check-fill display-5"></i>
                        </div>
                        <h3 class="fw-bold">Exclusive Event Access</h3>
                        <p class="text-muted">Event registrations and detailed community schedules are exclusive to active PWOA members. Join us to access our full calendar of events.</p>
                    </div>
                    <p class="mb-0 text-muted">Your current plan has limited access. <a href="{{ route('membership.index') }}" class="text-primary fw-bold text-decoration-none border-bottom border-primary">Unlock the Premium Marketplace & Education Center today.</a></p>
                    <div class="d-grid gap-3 mt-4">
                        <a href="{{ route('membership.index') }}" class="btn btn-primary btn-lg rounded-pill fw-bold px-5 py-3 shadow-sm">
                            Unlock Events Now
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-link text-decoration-none">Already a member? Sign in</a>
                    </div>
                </div>
            </div>
        @endif

        <div class="row g-4 @if(!$isSubscribed) pe-none user-select-none @endif" 
            style="@if(!$isSubscribed) filter: blur(8px); opacity: 0.6; @endif"
            wire:loading.class="opacity-50 transition-all">
            @forelse($events as $event)
                <div class="col-xl-12">
                    <div class="card border-0 shadow-sm glass-card hover-scale h-100 overflow-hidden">
                        <div class="row g-0 h-100">
                            <div class="col-md-4 bg-light position-relative overflow-hidden">
                                @if($event->image_path)
                                    <img src="{{ \Illuminate\Support\Str::startsWith($event->image_path, ['http://', 'https://']) ? $event->image_path : Storage::url($event->image_path) }}" class="w-100 h-100 object-fit-cover">
                                @else
                                    <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 bg-primary-subtle">
                                        <span class="display-5 fw-bold text-primary mb-0">{{ $event->starts_at->format('d') }}</span>
                                        <span class="text-uppercase fw-bold text-muted small">{{ $event->starts_at->format('M Y') }}</span>
                                    </div>
                                @endif
                                <div class="position-absolute top-0 start-0 p-3">
                                    <span class="badge bg-white text-dark shadow-sm rounded-pill px-3 py-2 fw-bold">
                                        {{ $event->isFreeFor(auth()->user()) ? 'Free' : '$' . number_format($event->price, 2) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card-body p-4 d-flex flex-column h-100">
                                    <div class="mb-3">
                                        <div class="mb-2">
                                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 small fw-bold">
                                                {{ $event->category->name ?? 'Event' }}
                                            </span>
                                        </div>
                                        <h5 class="fw-bold mb-1 text-truncate-2">{{ $event->title }}</h5>
                                        <div class="d-flex align-items-center gap-4 text-muted small mt-2">
                                            <span class="d-flex align-items-center gap-1"><i class="bi bi-clock"></i> {{ $event->starts_at->format('h:i A') }}</span>
                                            <span class="d-flex align-items-center gap-1"><i class="bi bi-geo-alt"></i> {{ $event->location }}</span>
                                        </div>
                                    </div>
                                    
                                    <p class="text-muted small mb-4 line-clamp-2 mt-auto">
                                        {!! $event->description !!}
                                    </p>

                                    <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                        <div class="text-muted small fw-bold">
                                            <i class="bi bi-people-fill me-1 text-primary"></i> 
                                            {{ $event->attendees()->count() }} Interested
                                        </div>
                                        <a href="{{ route('events.show', $event->slug) }}" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold shadow-sm">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="opacity-25 mb-4">
                        <i class="bi bi-calendar-x" style="font-size: 5rem;"></i>
                    </div>
                    <h4 class="fw-bold">No events found</h4>
                    <p class="text-muted">Try adjusting your filters or search terms.</p>
                </div>
            @endforelse

            @if($isSubscribed)
            <div class="mt-5">
                {{ $events->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

