@extends('layouts.front')

@section('title', $track->name)

@section('content')
<section class="section-hero bg-brand-gradient position-relative overflow-hidden" style="padding-top: 100px; padding-bottom: 100px;">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-pattern opacity-25"></div>
    <div class="container text-center position-relative z-1">
        <a href="{{ route('education.index') }}" class="text-white text-decoration-none mb-4 d-inline-flex align-items-center gap-1 small opacity-75 hover-opacity-100 transition">
            <i class="bi bi-arrow-left"></i> Back to Education
        </a>
        <br>
        <span class="badge rounded-pill bg-white text-primary px-3 py-2 mb-3 shadow-sm text-uppercase fw-bold tracking-wider">Certification Track</span>
        <h1 class="display-4 fw-bold mb-4 text-white">{{ $track->name }}</h1>
        <p class="lead text-white-50 mx-auto mb-5" style="max-width: 800px;">{!! strip_tags($track->description) !!}</p>
        
        <div class="d-flex justify-content-center gap-4 text-white">
            <div class="d-flex flex-column align-items-center">
                <span class="fs-2 fw-bold">{{ $courses->count() }}</span>
                <span class="small text-white-50 text-uppercase tracking-wider">Courses</span>
            </div>
            <div class="border-end border-white opacity-25"></div>
            <div class="d-flex flex-column align-items-center">
                <span class="fs-2 fw-bold">{{ $track->renewal_years ?? 2 }}</span>
                <span class="small text-white-50 text-uppercase tracking-wider">Year Renewal</span>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light min-vh-100">
    <div class="container">
        
        <div class="row g-4 justify-content-center">
            <div class="col-lg-10">
                
                <div class="card border-0 shadow-sm rounded-4 mb-5 bg-white overflow-hidden">
                    <div class="row g-0">
                        <div class="col-md-4 bg-primary-subtle d-flex align-items-center justify-content-center p-5">
                            <i class="bi bi-award-fill text-primary" style="font-size: 6rem;"></i>
                        </div>
                        <div class="col-md-8 p-4 p-lg-5">
                            <h2 class="h4 fw-bold mb-3">About this Certification</h2>
                            <div class="text-secondary mb-4 lh-lg">
                                {!! $track->description !!}
                            </div>
                            <div class="d-flex align-items-center gap-2 text-dark fw-medium bg-light p-3 rounded-3 d-inline-flex">
                                <i class="bi bi-info-circle-fill text-primary"></i> 
                                Complete all {{ $courses->count() }} courses to earn your official PWOA certification.
                            </div>
                        </div>
                    </div>
                </div>

                <h3 class="h4 fw-bold mb-4">Required Courses</h3>

                <div class="d-flex flex-column gap-4">
                    @forelse($courses as $index => $course)
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden hover-scale transition">
                        <div class="row g-0">
                            <div class="col-md-3 position-relative" style="min-height: 200px;">
                                @if($course->thumbnail_path)
                                    <img src="{{ str_starts_with($course->thumbnail_path, 'http') ? $course->thumbnail_path : Storage::url($course->thumbnail_path) }}" class="w-100 h-100 object-fit-cover position-absolute top-0 start-0" alt="{{ $course->title }}">
                                @else
                                    <div class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center text-white-50 position-absolute top-0 start-0">
                                        <i class="bi bi-image" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                                <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-25"></div>
                                <div class="position-absolute top-0 start-0 p-3">
                                    <div class="bg-white text-dark fw-bold rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 40px; height: 40px;">
                                        {{ $index + 1 }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9 p-4 p-lg-5 d-flex flex-column bg-white">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h4 class="h5 fw-bold mb-0">{{ $course->title }}</h4>
                                    @if($course->is_free)
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1"><i class="bi bi-unlock-fill me-1"></i> Free</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1"><i class="bi bi-lock-fill me-1"></i> Premium</span>
                                    @endif
                                </div>
                                
                                <p class="text-secondary small mb-4 flex-grow-1 lh-lg">
                                    {!! Str::limit(strip_tags($course->description), 150) !!}
                                </p>
                                
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-auto pt-3 border-top">
                                    <div class="d-flex gap-4 text-muted small fw-medium">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-clock text-primary"></i> {{ $course->duration_hours }}h {{ $course->duration_minutes ?? 0 }}m
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-journal-text text-primary"></i> {{ $course->lessons_count }} Lessons
                                        </div>
                                    </div>
                                    <a href="{{ route('education.course', $course->slug) }}" class="btn btn-outline-primary rounded-pill px-4">
                                        View Course
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="alert alert-light border text-center p-5 rounded-4 shadow-sm">
                        <i class="bi bi-journal-x text-muted mb-3" style="font-size: 3rem;"></i>
                        <h4 class="fw-bold">No courses found</h4>
                        <p class="text-muted mb-0">Courses for this track will be added soon.</p>
                    </div>
                    @endforelse
                </div>

            </div>
        </div>

    </div>
</section>
@endsection
