@extends('layouts.front')

@section('title', $lesson->title . ' - ' . $course->title)

@section('content')

    @php
        $isEnrolled = auth()->check() ? auth()->user()->enrolledCourses->contains($course->id) : false;
        $completedLessonIds = auth()->check() ? auth()->user()->completedLessons->pluck('id')->toArray() : [];
        $isCurrentCompleted = in_array($lesson->id, $completedLessonIds);
    @endphp

    <section class="bg-dark text-white pt-4 pb-3">
        <div class="container">
            <div class="d-flex align-items-center gap-2 mb-3 small opacity-75">
                <a href="{{ route('education.course', $course->slug) }}"
                    class="text-white text-decoration-none hover-opacity-100 transition">
                    <i class="bi bi-arrow-left me-1"></i> Back to Course
                </a>
                <span class="mx-2 text-muted">|</span>
                <span class="text-white-50">{{ $course->title }}</span>
            </div>
            <h1 class="h3 fw-bold mb-0">
                @if($isCurrentCompleted)
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                @endif
                {{ $lesson->title }}
            </h1>
        </div>
    </section>

    <section class="py-5 bg-light min-vh-100">
        <div class="container">
            <div class="row g-5">

                {{-- MAIN CONTENT: VIDEO & DESCRIPTION --}}
                <div class="col-lg-8">

                    {{-- Video Player Container --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-dark">
                        <div class="ratio ratio-16x9">
                            @if($lesson->video_url)
                                <iframe src="{{ $lesson->video_url }}" title="{{ $lesson->title }}" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                            @else
                                <div
                                    class="d-flex flex-column align-items-center justify-content-center text-white-50 p-5 text-center">
                                    <i class="bi bi-play-circle mb-3" style="font-size: 4rem;"></i>
                                    <h4 class="fw-medium text-white">Video content unavailable</h4>
                                    <p class="mb-0">Please refer to the lesson text below.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Lesson Content --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="card-body p-4 p-lg-5 bg-white">
                            <h2 class="h4 fw-bold mb-4 pb-3 border-bottom">Lesson Content</h2>
                            <div class="text-secondary lh-lg fs-6 article-content">
                                {!! $lesson->content !!}
                            </div>
                        </div>
                    </div>

                    {{-- Completion Action --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                        <div
                            class="card-body p-4 d-flex flex-column flex-md-row align-items-center justify-content-between gap-4">
                            @if($isCurrentCompleted)
                                <div>
                                    <h3 class="h5 fw-bold mb-1 text-success"><i class="bi bi-check-circle-fill me-1"></i> Lesson
                                        Completed!</h3>
                                    <p class="text-muted small mb-0">You have successfully finished this lesson.</p>
                                </div>

                                @php
                                    $nextLesson = $course->lessons->where('order', '>', $lesson->order)->first();
                                @endphp

                                @if($nextLesson)
                                    <a href="{{ route('education.lesson', [$course->slug, $nextLesson->slug]) }}"
                                        class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-2 transition hover-scale">
                                        Next Lesson <i class="bi bi-arrow-right"></i>
                                    </a>
                                @else
                                    <a href="{{ route('education.course', $course->slug) }}"
                                        class="btn btn-outline-success rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-2 transition hover-scale">
                                        <i class="bi bi-award"></i> View Course Dashboard
                                    </a>
                                @endif

                            @elseif($isEnrolled)
                                <div>
                                    <h3 class="h5 fw-bold mb-1">Finished this lesson?</h3>
                                    <p class="text-muted small mb-0">Mark it as complete to track your progress.</p>
                                </div>

                                <form method="POST"
                                    action="{{ route('education.lesson.complete', [$course->slug, $lesson->slug]) }}">
                                    @csrf
                                    <button
                                        class="btn btn-success rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-2 shadow-sm transition hover-scale">
                                        <i class="bi bi-check-circle-fill"></i> Mark Completed
                                    </button>
                                </form>
                            @else
                                <div>
                                    <h3 class="h5 fw-bold mb-1"><i class="bi bi-lock-fill text-warning me-1"></i> You are not
                                        enrolled</h3>
                                    <p class="text-muted small mb-0">Enroll in this course to track your progress.</p>
                                </div>

                                <a href="{{ route('education.course', $course->slug) }}"
                                    class="btn btn-brand rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-2 shadow-sm transition hover-scale">
                                    <i class="bi bi-rocket-takeoff-fill"></i> Enroll Now
                                </a>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- SIDEBAR: COURSE SYLLABUS --}}
                <div class="col-lg-4">
                    <div class="sticky-top" style="top: 100px; z-index: 10; width: 100%;">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body p-4 bg-white">
                                <h3 class="h5 fw-bold mb-4 border-bottom pb-3">Course Syllabus</h3>

                                <div class="list-group list-group-flush gap-2">
                                    @foreach($course->lessons as $index => $courseLesson)
                                        @php
                                            $isActive = $courseLesson->id === $lesson->id;
                                            $isLessonCompleted = in_array($courseLesson->id, $completedLessonIds);

                                            $isLocked = $courseLesson->is_restricted && !$course->is_free && !auth()->check();
                                            if (!$course->is_free && !$isEnrolled && $courseLesson->is_restricted) {
                                                $isLocked = true;
                                            }

                                            // Sequential check
                                            if (!$isLocked && $index > 0) {
                                                $prevLesson = $course->lessons[$index - 1];
                                                if (!in_array($prevLesson->id, $completedLessonIds)) {
                                                    $isLocked = true;
                                                }
                                            }
                                        @endphp
                                        @if($isLocked)
                                            <div
                                                class="list-group-item list-group-item-action rounded-3 border-0 d-flex justify-content-between align-items-center p-3 opacity-75 bg-light">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm fw-bold"
                                                        style="width: 32px; height: 32px; font-size: 0.85rem;">
                                                        {{ $index + 1 }}
                                                    </div>
                                                    <div class="fw-medium small text-secondary lh-sm">
                                                        {{ Str::limit($courseLesson->title, 35) }}
                                                    </div>
                                                </div>
                                                <div class="text-muted">
                                                    <i class="bi bi-lock-fill text-warning small" title="{{ !$isEnrolled && $courseLesson->is_restricted ? 'Premium Content' : 'Complete previous lessons to unlock' }}"></i>
                                                </div>
                                            </div>
                                        @else
                                            <a href="{{ route('education.lesson', [$course->slug, $courseLesson->slug]) }}"
                                                class="list-group-item list-group-item-action rounded-3 border-0 d-flex justify-content-between align-items-center p-3 transition {{ $isActive ? 'bg-primary-subtle text-primary border-start border-4 border-primary' : ($isLessonCompleted ? 'bg-success-subtle' : 'bg-light hover-shadow') }}">

                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="{{ $isLessonCompleted ? 'bg-success text-white' : ($isActive ? 'bg-primary text-white' : 'bg-white text-secondary') }} rounded-circle d-flex align-items-center justify-content-center shadow-sm fw-bold"
                                                        style="width: 32px; height: 32px; font-size: 0.85rem;">
                                                        @if($isLessonCompleted && !$isActive)
                                                            <i class="bi bi-check-lg"></i>
                                                        @else
                                                            {{ $index + 1 }}
                                                        @endif

                                                    </div>
                                                    <div
                                                        class="fw-medium small {{ $isLessonCompleted ? 'text-success-emphasis' : ($isActive ? 'text-primary' : 'text-dark') }} lh-sm">
                                                        {{ Str::limit($courseLesson->title, 35) }}
                                                    </div>
                                                </div>

                                                <div class="text-muted">
                                                    @if($isLessonCompleted)
                                                        <i class="bi bi-check-circle-fill text-success small"></i>
                                                    @elseif($isActive)
                                                        <i class="bi bi-play-fill text-primary"></i>
                                                    @else
                                                        <i class="bi bi-play-circle text-muted small"></i>
                                                    @endif
                                                </div>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection