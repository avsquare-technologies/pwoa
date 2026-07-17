<x-slot name="header">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0 text-dark fw-bold">
            Learning Center
        </h2>
        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 d-none d-md-inline-block">
            {{ $courses->total() }} Professional Courses
        </span>
    </div>
</x-slot>

<div class="row g-4">
    <!-- Filters Sidebar -->
    <div class="col-lg-3">
        <div class="card border-0 glass-card sticky-top" style="top: 100px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Learning Filters</h5>
                    <div wire:loading wire:target="search, category_id" class="spinner-border spinner-border-sm text-primary" role="status"></div>
                </div>
                
                <!-- Search -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">Search Courses</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search py-1"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control bg-light border-0" placeholder="e.g. OSHA, Safety...">
                    </div>
                </div>

                <!-- Categories -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">Course Category</label>
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
                    <button wire:click="$set('search', ''); $set('category_id', '');" class="btn btn-outline-secondary btn-sm rounded-3">
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
                            <i class="bi bi-mortarboard-fill display-5"></i>
                        </div>
                        <h3 class="fw-bold">Premium Learning Center</h3>
                        <p class="text-muted">Professional courses and certifications are exclusive to active PWOA members. Upgrade your account to start learning today.</p>
                    </div>
                    <div class="d-grid gap-3">
                        <a href="{{ route('membership.index') }}" class="btn btn-primary btn-lg rounded-pill fw-bold px-5 py-3 shadow-sm">
                            Access Courses Now
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-link text-decoration-none">Already a member? Sign in</a>
                    </div>
                </div>
            </div>
        @endif

        <div class="row g-4 @if(!$isSubscribed) pe-none user-select-none @endif" 
            style="@if(!$isSubscribed) filter: blur(8px); opacity: 0.6; @endif"
            wire:loading.class="opacity-50 transition-all">
            @forelse($courses as $course)
                <div class="col-md-6 col-xl-6">
                    <div class="card border-0 h-100 glass-card hover-scale overflow-hidden shadow-sm">
                        <div class="course-thumb position-relative" style="height: 200px; background: #f8f9fa;">
                            @if($course->thumbnail_path)
                                <img src="{{ str_starts_with($course->thumbnail_path, 'http') ? $course->thumbnail_path : Storage::url($course->thumbnail_path) }}" class="w-100 h-100 object-fit-cover transition-all">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-primary bg-gradient text-white opacity-75">
                                    <i class="bi bi-mortarboard fs-1"></i>
                                </div>
                            @endif
                            <div class="position-absolute bottom-0 start-0 p-3 w-100 bg-gradient-dark">
                                <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold shadow-sm">
                                    {{ $course->lessons_count }} Modular Lessons
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex gap-2 mb-2">
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 small fw-bold">
                                    {{ $course->category->name ?? 'Course' }}
                                </span>
                            </div>
                            <h5 class="fw-bold mb-3 ls-tight">{{ $course->title }}</h5>
                            <p class="text-muted small mb-4 line-clamp-3">
                                {!! $course->description !!}
                            </p>

                            <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                <div class="d-flex flex-column gap-1">
                                    <span class="text-muted small fw-bold"><i class="bi bi-person-check me-1"></i> {{ $course->users_count }} Joined</span>
                                    <span class="text-muted small fw-bold"><i class="bi bi-clock me-1"></i> Flexible Pace</span>
                                </div>
                                @if(in_array($course->id, $joinedCourseIds))
                                    <a href="{{ route('courses.player', $course->slug) }}" class="btn btn-outline-primary rounded-pill px-4 btn-sm fw-bold shadow-sm">
                                        Continue Lesson
                                    </a>
                                @else
                                    <a href="{{ route('courses.player', $course->slug) }}" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold shadow-sm">
                                        Join Course
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="opacity-25 mb-4">
                        <i class="bi bi-journal-x" style="font-size: 5rem;"></i>
                    </div>
                    <h4 class="fw-bold">No courses found</h4>
                    <p class="text-muted">Try adjusting your filters or search terms.</p>
                </div>
            @endforelse

            @if($isSubscribed)
            <div class="mt-5">
                {{ $courses->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

