@extends('layouts.front')

@section('title', 'Education & Certification')

@section('content')
<section class="section-hero bg-brand-gradient position-relative overflow-hidden" style="padding-top: 100px; padding-bottom: 100px;">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-pattern opacity-25"></div>
    <div class="container text-center position-relative z-1">
        <span class="badge rounded-pill bg-white text-primary px-3 py-2 mb-3 shadow-sm text-uppercase fw-bold tracking-wider">Education Center</span>
        <h1 class="display-4 fw-bold mb-4 text-white">Level Up Your Washing Business</h1>
        <p class="lead text-white-50 mx-auto mb-5" style="max-width: 800px;">Explore comprehensive certification tracks and practical training built for field teams, owners, and growth-minded contractors.</p>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        
        <div class="d-flex justify-content-between align-items-center mb-5 mt-4">
            <div>
                <h2 class="h2 fw-bold text-dark mb-1">Certification Tracks</h2>
                <p class="text-muted">Structured learning paths to master your craft.</p>
            </div>
            <a href="#all-courses" class="btn btn-outline-primary rounded-pill px-4">View All Courses</a>
        </div>

        <div class="row g-4 mb-5 pb-4 border-bottom">
            @foreach($certificationTracks ?? [] as $track)
            <div class="col-lg-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm hover-scale overflow-hidden rounded-4">
                    <div class="card-body p-4 p-xl-5 d-flex flex-column bg-white">
                        <div class="icon-box bg-primary-subtle text-primary rounded-circle mb-4 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.5rem;">
                           <i class="bi bi-award-fill"></i>
                        </div>
                        <h3 class="h4 fw-bold mb-3">{{ $track->name }}</h3>
                        <p class="text-secondary mb-4 flex-grow-1">{!! Str::limit(strip_tags($track->description), 100) !!}</p>
                        <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-auto">
                            <span class="text-muted small fw-bold"><i class="bi bi-journal-bookmark me-1"></i> {{ $track->courses_count }} Courses</span>
                            <a href="{{ route('education.track', $track->slug) }}" class="text-primary fw-bold text-decoration-none">Explore <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div id="all-courses" class="d-flex justify-content-between align-items-center mb-5 mt-5">
            <div>
                <h2 class="h2 fw-bold text-dark mb-1">Available Courses</h2>
                <p class="text-muted">Pick a specific skill and start learning today.</p>
            </div>
        </div>

        <div class="row g-4 mb-5">
            @forelse($courses ?? [] as $course)
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100 hover-scale overflow-hidden rounded-4">
                    <div class="position-relative" style="height: 220px;">
                        @if($course->thumbnail_path)
                            <img src="{{ str_starts_with($course->thumbnail_path, 'http') ? $course->thumbnail_path : Storage::url($course->thumbnail_path) }}" class="w-100 h-100 object-fit-cover" alt="{{ $course->title }}">
                        @else
                            <div class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center text-white-50">
                                <i class="bi bi-image" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.6) 100%);"></div>
                        <div class="position-absolute bottom-0 start-0 p-3 w-100 d-flex justify-content-between align-items-end">
                            <span class="badge bg-primary text-white rounded-pill px-3 py-2 shadow-sm">{{ $course->category?->name ?? 'General' }}</span>
                            @if($course->is_free)
                                <span class="badge bg-success text-white rounded-pill px-3 py-2 shadow-sm"><i class="bi bi-unlock-fill me-1"></i> Free</span>
                            @else
                                <span class="badge bg-dark text-white rounded-pill px-3 py-2 shadow-sm"><i class="bi bi-lock-fill me-1"></i> Premium</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-body p-4 d-flex flex-column bg-white">
                        <h3 class="h5 fw-bold mb-3 text-dark lh-base">{{ $course->title }}</h3>
                        <p class="text-secondary flex-grow-1 small lh-lg">{!! Str::limit(strip_tags($course->description), 120) !!}</p>
                        
                        <div class="d-flex align-items-center gap-3 mb-4 text-muted small bg-light p-3 rounded-3 mt-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-clock text-primary"></i>
                                <span class="fw-medium">{{ $course->duration_hours }}h {{ $course->duration_minutes ?? 0 }}m</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-book text-primary"></i>
                                <span class="fw-medium">{{ $course->lessons_count }} Lessons</span>
                            </div>
                        </div>
                        
                        <a href="{{ route('education.course', $course->slug) }}" class="btn btn-brand w-100 py-3 rounded-pill fw-bold mt-auto shadow-sm">
                            View Course Details
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                    <div class="text-muted opacity-25 mb-3"><i class="bi bi-journal-x" style="font-size: 4rem;"></i></div>
                    <h4 class="fw-bold text-dark">No courses available yet</h4>
                    <p class="text-muted">We are working hard to bring you top-tier content. Check back soon!</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection

