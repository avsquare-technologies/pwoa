<div>
    <x-slot name="header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('events') }}" class="text-decoration-none">Events</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $event->title }}</li>
            </ol>
        </nav>
    </x-slot>

    @if(session('status'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 p-4 d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-3 fs-3"></i>
            <div class="fw-bold">{{ session('status') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-4 d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-3 fs-3"></i>
            <div class="fw-bold">{{ session('error') }}</div>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 glass-card overflow-hidden mb-4">
                <div class="bg-dark p-0 position-relative" style="height: 350px;">
                    @if($event->image_path)
                        <img src="{{ \Illuminate\Support\Str::startsWith($event->image_path, ['http://', 'https://']) ? $event->image_path : Storage::url($event->image_path) }}" class="w-100 h-100 object-fit-cover opacity-75">
                    @else
                        <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-primary bg-gradient">
                            <i class="bi bi-calendar-event text-white" style="font-size: 8rem; opacity: 0.1;"></i>
                        </div>
                    @endif
                    <div class="position-absolute bottom-0 start-0 w-100 p-5 bg-gradient-dark">
                        <h1 class="text-white fw-bold display-5 mb-0">{{ $event->title }}</h1>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary-subtle text-primary rounded-4 p-3 fs-4">
                                    <i class="bi bi-calendar-date"></i>
                                </div>
                                <div>
                                    <p class="text-muted small text-uppercase fw-bold mb-0">Date & Time</p>
                                    <p class="mb-0 fw-bold fs-5">{{ $event->starts_at->format('M d, Y') }} at {{ $event->starts_at->format('h:i A') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-info-subtle text-info rounded-4 p-3 fs-4">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div>
                                    <p class="text-muted small text-uppercase fw-bold mb-0">Location</p>
                                    <p class="mb-0 fw-bold fs-5">{{ $event->location }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-4">About this Event</h5>
                    <div class="text-muted fs-5 lh-base mb-0">
                        {!! $event->description !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sticky-top" style="top: 100px;">
                <div class="card border-0 glass-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Registration</h5>
                        <span class="badge bg-{{ $event->isFreeFor(auth()->user()) ? 'success' : 'primary' }}-subtle text-{{ $event->isFreeFor(auth()->user()) ? 'success' : 'primary' }} rounded-pill px-3 py-2 fw-bold">
                            {{ $event->isFreeFor(auth()->user()) ? 'FREE ACCESS' : '$' . number_format($event->price, 2) }}
                        </span>
                    </div>

                    @auth
                        @if($event->attendees()->where('user_id', auth()->id())->exists())
                            <div class="text-center py-4 bg-success-subtle rounded-4 mb-4">
                                <i class="bi bi-check-circle-fill text-success fs-1 mb-2"></i>
                                <h6 class="fw-bold mb-0">You're Registered!</h6>
                            </div>
                        @else
                            <button wire:click="register" wire:loading.attr="disabled" class="btn btn-primary w-100 py-3 rounded-4 shadow fw-bold fs-5">
                                <span wire:loading wire:target="register" class="loading-spinner"></span>
                                Confirm Presence
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-dark w-100 py-3 rounded-4 fw-bold">Login to Register</a>
                    @endauth

                    <div class="mt-4 pt-4 border-top">
                        <div class="d-flex justify-content-between text-muted small mb-2">
                            <span>Capacity</span>
                            <span class="fw-bold text-dark">{{ $event->attendees()->count() }} / {{ $event->capacity ?? 'Unlimited' }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            @if($event->capacity)
                                <div class="progress-bar bg-primary" style="width: {{ ($event->attendees()->count() / $event->capacity) * 100 }}%"></div>
                            @else
                                <div class="progress-bar bg-primary" style="width: 10%"></div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm p-4 bg-light rounded-4">
                    <h6 class="fw-bold mb-3">Event Guidelines</h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-2"><i class="bi bi-info-circle me-2 text-primary"></i> Please arrive 15 minutes before state time.</li>
                        <li class="mb-2"><i class="bi bi-info-circle me-2 text-primary"></i> Cancellation allowed up to 24h prior.</li>
                        <li><i class="bi bi-info-circle me-2 text-primary"></i> Network with verified professionals.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

