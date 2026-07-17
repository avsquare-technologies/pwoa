@extends('layouts.front')

@section('title', $course->title)

@section('content')

@php
    $isEnrolled = auth()->check() ? auth()->user()->enrolledCourses->contains($course->id) : false;
    $completedLessonIds = auth()->check() ? auth()->user()->completedLessons->pluck('id')->toArray() : [];
@endphp

{{-- Hero Section --}}
<section class="position-relative" style="height: 350px;">
    @if($course->thumbnail_path)
        <img src="{{ str_starts_with($course->thumbnail_path, 'http') ? $course->thumbnail_path : Storage::url($course->thumbnail_path) }}" class="w-100 h-100 object-fit-cover position-absolute top-0 start-0" alt="{{ $course->title }}">
    @else
        <div class="w-100 h-100 bg-brand-gradient position-absolute top-0 start-0"></div>
    @endif
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.8) 100%);"></div>

    <div class="container position-relative h-100 d-flex flex-column justify-content-end pb-5">
        <a href="{{ route('education.index') }}" class="text-white text-decoration-none mb-3 d-inline-flex align-items-center gap-1 small opacity-75 hover-opacity-100 transition">
            <i class="bi bi-arrow-left"></i> Back to Education
        </a>

        <div class="d-flex flex-wrap gap-2 mb-3">
            <span class="badge bg-white text-dark rounded-pill px-3 py-1 shadow-sm">{{ $course->category?->name ?? 'General' }}</span>
            @if($course->is_free)
                <span class="badge bg-success rounded-pill px-3 py-1 shadow-sm"><i class="bi bi-unlock-fill me-1"></i> Free Course</span>
            @else
                <span class="badge bg-warning text-dark rounded-pill px-3 py-1 shadow-sm"><i class="bi bi-lock-fill me-1"></i> Premium Course</span>
            @endif
            @if($isEnrolled)
                <span class="badge bg-primary rounded-pill px-3 py-1 shadow-sm"><i class="bi bi-person-check-fill me-1"></i> Enrolled</span>
            @endif
        </div>

        <h1 class="display-5 fw-bold text-white mb-2">{{ $course->title }}</h1>
        <div class="d-flex align-items-center gap-4 text-white-50 small">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-clock"></i> {{ $course->duration_hours }}h {{ $course->duration_minutes ?? 0 }}m
            </div>
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-journal-text"></i> {{ $course->lessons->count() }} Lessons
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light min-vh-100">
    <div class="container">
        <div class="row g-5">

            {{-- LEFT SIDE: DESCRIPTION & DETAILS --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-body p-4 p-lg-5 bg-white">
                        <h2 class="h4 fw-bold mb-4 border-bottom pb-3">Course Overview</h2>
                        <div class="text-secondary lh-lg fs-6">
                            {!! $course->description !!}
                        </div>
                    </div>
                </div>

                {{-- Course Content / Lessons List --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-lg-5 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <h2 class="h4 fw-bold mb-0">Course Curriculum</h2>
                            <span class="text-muted small">{{ $course->lessons->count() }} total lessons</span>
                        </div>

                        <div class="list-group list-group-flush gap-2">
                            @forelse($course->lessons as $index => $lesson)
                                @php
                                    $isCompleted = in_array($lesson->id, $completedLessonIds);
                                    $isLocked = $lesson->is_restricted && !$course->is_free && !auth()->check();

                                    // If premium course, maybe we lock lessons if not enrolled?
                                    if (!$course->is_free && !$isEnrolled && $lesson->is_restricted) {
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
                                    <div class="list-group-item list-group-item-action rounded-3 border-0 bg-light d-flex justify-content-between align-items-center p-3 opacity-75">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm fw-bold" style="width: 40px; height: 40px;">
                                                {{ $index + 1 }}
                                            </div>
                                            <div class="fw-medium text-secondary">{{ $lesson->title }}</div>
                                        </div>
                                        <div class="text-muted">
                                            @if(!$isEnrolled && $lesson->is_restricted)
                                                <i class="bi bi-lock-fill text-warning fs-5" title="Enroll to unlock"></i>
                                            @else
                                                <i class="bi bi-lock-fill text-warning fs-5" title="Complete previous lessons to unlock"></i>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ route('education.lesson', [$course->slug, $lesson->slug]) }}"
                                       class="list-group-item list-group-item-action rounded-3 border-0 {{ $isCompleted ? 'bg-success-subtle border-start border-4 border-success' : 'bg-light' }} d-flex justify-content-between align-items-center p-3 hover-shadow transition">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="{{ $isCompleted ? 'bg-success text-white' : 'bg-white text-primary' }} rounded-circle d-flex align-items-center justify-content-center shadow-sm fw-bold" style="width: 40px; height: 40px;">
                                                @if($isCompleted)
                                                    <i class="bi bi-check-lg"></i>
                                                @else
                                                    {{ $index + 1 }}
                                                @endif
                                            </div>
                                            <div class="fw-medium {{ $isCompleted ? 'text-success-emphasis' : 'text-dark' }}">{{ $lesson->title }}</div>
                                        </div>
                                        <div class="text-muted">
                                            @if($isCompleted)
                                                <span class="badge bg-success text-white rounded-pill small">Completed</span>
                                            @else
                                                <i class="bi bi-play-circle-fill text-primary fs-5"></i>
                                            @endif
                                        </div>
                                    </a>
                                @endif
                            @empty
                            <div class="text-center py-4 text-muted">
                                No lessons available for this course yet.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- ASSESSMENT SECTION (Moved to Main Column) --}}
                @if($isEnrolled && $progress == 100)
                    <div id="assessment-section">
                    @if(!$quizResult || !$quizResult->passed || request()->has('retake'))
                        {{-- SHOW QUIZ --}}
                        @if($course->quiz)
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-4">
                                <div class="card-body p-4 p-lg-5 bg-white">
                                    @if(session('quiz_result') && !session('quiz_result')['passed'])
                                        <div class="alert alert-danger rounded-4 mb-4 d-flex align-items-center gap-3">
                                            <i class="bi bi-exclamation-octagon-fill fs-4"></i>
                                            <div>
                                                <strong>Assessment Not Passed:</strong> {{ session('quiz_result')['message'] }}
                                                <span class="badge bg-danger ms-2">{{ session('quiz_result')['score'] }}%</span>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                                            <i class="bi bi-pencil-square fs-5"></i>
                                        </div>
                                        <div>
                                            <h2 class="h4 fw-bold mb-0">Final Assessment</h2>
                                            <p class="small text-muted mb-0">Passing score required: <strong>{{ $course->quiz->pass_percentage }}%</strong></p>
                                        </div>
                                    </div>

                                    {{-- <form method="POST" action="{{ route('quiz.submit', $course->slug) }}" id="quiz-form">
                                        @csrf
                                        @foreach($course->quiz->questions as $index => $question)
                                            <div class="mb-4 p-4 bg-light rounded-4 border-start border-4 border-primary">
                                                <p class="fw-bold mb-3 fs-5">
                                                    {{ $index + 1 }}. {{ $question->question_text }}
                                                </p>
                                                <div class="row g-3">
                                                    @foreach($question->options as $key => $option)
                                                        <div class="col-md-6">
                                                            <div class="form-check custom-option p-0">
                                                                <input class="btn-check" type="radio"
                                                                       name="answers[{{ $question->id }}]"
                                                                       id="q{{ $question->id }}_{{ $key }}"
                                                                       value="{{ $key }}" required>
                                                                <label class="btn btn-outline-secondary w-100 text-start py-3 px-3 rounded-3 h-100 d-flex align-items-center" for="q{{ $question->id }}_{{ $key }}">
                                                                    {{ is_array($option) ? ($option['text'] ?? ($option['label'] ?? json_encode($option))) : $option }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach

                                        <div class="mt-5">
                                            <button type="submit" class="btn btn-primary btn-lg px-5 py-3 rounded-pill fw-bold shadow transform-hover">
                                                Submit Your Assessment <i class="bi bi-send-fill ms-2"></i>
                                            </button>
                                        </div>
                                    </form> --}}

                                      <livewire:public.quiz-engine :quiz="$course->quiz" />
                                </div>
                            </div>
                        @endif
                    @else
                        {{-- SHOW SUCCESS RESULT --}}
                        <!-- <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-4 border-top border-5 border-success">
                            <div class="card-body p-4 p-lg-5 text-center bg-white">
                                <div class="mb-4">
                                    <div class="bg-success text-white rounded-circle mx-auto d-flex align-items-center justify-content-center shadow mb-3" style="width: 80px; height: 80px;">
                                        <i class="bi bi-trophy-fill fs-1"></i>
                                    </div>
                                    <h2 class="fw-bold text-success">Congratulations! You Passed</h2>
                                    <p class="text-muted">You have successfully completed the assessment for this course.</p>
                                </div>

                                <div class="row justify-content-center mb-4">
                                    <div class="col-auto">
                                        <div class="p-3 bg-light rounded-4 px-5 border">
                                            <div class="display-4 fw-bold mb-0 text-success">{{ round($quizResult->score) }}%</div>
                                            <div class="text-muted small fw-medium">Your Final Score</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap justify-content-center gap-3">
                                    @if($certificate)
                                        <a href="{{ route('certificates.show', $certificate->certificate_number) }}"
                                           class="btn btn-success btn-lg px-5 py-3 rounded-pill fw-bold shadow transform-hover">
                                            <i class="bi bi-award-fill me-2"></i> View & Download Certificate
                                        </a>
                                    @endif

                                    <a href="?retake=1" class="btn btn-outline-secondary btn-lg px-4 py-3 rounded-pill fw-bold">
                                        <i class="bi bi-arrow-clockwise me-2"></i> Retake Assessment
                                    </a>
                                </div>
                            </div>
                        </div> -->
                        {{-- SHOW SUCCESS RESULT --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-4 border-top border-5 border-success">
        <div class="card-body p-4 p-lg-5 text-center bg-white">

            <div class="mb-4">
                <div class="bg-success text-white rounded-circle mx-auto d-flex align-items-center justify-content-center shadow mb-3"
                     style="width: 80px; height: 80px;">
                    <i class="bi bi-trophy-fill fs-1"></i>
                </div>

                <h2 class="fw-bold text-success">Congratulations! You Passed</h2>
                <p class="text-muted">You have successfully completed the assessment for this course.</p>
            </div>

            {{-- SCORE --}}
            <div class="row justify-content-center mb-4">
                <div class="col-auto">
                    <div class="p-3 bg-light rounded-4 px-5 border">
                        <div class="display-4 fw-bold mb-0 text-success">
                            {{ round($quizResult->score) }}%
                        </div>
                        <div class="text-muted small fw-medium">Your Final Score</div>
                    </div>
                </div>
            </div>

            {{-- CERTIFICATE --}}
            @if($certificate)
                <div class="mb-3">
                    <div class="small text-muted">
                        Certificate: <strong>{{ $certificate->certificate_number }}</strong>
                    </div>

                    <a href="{{ route('certificates.show', $certificate->certificate_number) }}"
                       class="btn btn-success mt-2">
                        <i class="bi bi-download me-2"></i>
                        Download Certificate
                    </a>
                </div>
            @endif

            {{-- ACTIONS --}}
            <div class="d-flex flex-wrap justify-content-center gap-3 mt-3">
                <a href="?retake=1" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise me-2"></i> Retake
                </a>
            </div>

        </div>
    </div>
@endif
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="sticky-top" style="top: 100px; z-index: 10; width: 100%;">
                    <div class="card border-0 shadow-lg rounded-4">
                        <div class="card-body p-4 p-lg-5 text-center bg-white">

                            <div class="icon-box {{ $isEnrolled ? 'bg-success text-white' : 'bg-brand-gradient text-white' }} rounded-circle mb-4 mx-auto d-flex align-items-center justify-content-center shadow" style="width: 80px; height: 80px; font-size: 2rem;">
                                @if($isEnrolled)
                                    <i class="bi bi-check-circle-fill"></i>
                                @else
                                    <i class="bi bi-mortarboard-fill"></i>
                                @endif
                            </div>

                            @if($isEnrolled)
                                <h3 class="h4 fw-bold mb-2 text-success">You're Enrolled!</h3>

                                <div class="mb-4 text-start">
                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span>Course Progress</span>
                                        <span class="fw-bold">{{ $progress }}%</span>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>

                                @if($progress < 100)
                                    {{-- RESUME LEARNING --}}
                                    @php
                                        $resumeLesson = $course->lessons->first(fn($l) => !in_array($l->id, $completedLessonIds));
                                    @endphp
                                    @if($resumeLesson)
                                        <a href="{{ route('education.lesson', [$course->slug, $resumeLesson->slug]) }}"
                                           class="btn btn-brand w-100 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-play-fill"></i> Resume Learning
                                        </a>
                                    @endif
                                @else
                                    {{-- STATUS INDICATOR IN SIDEBAR --}}
                                    <div class="mt-3">
                                        @if($quizResult && $quizResult->passed)
                                            <div class="alert alert-success rounded-4 small mb-0 border-0 shadow-sm">
                                                <i class="bi bi-check-circle-fill me-2"></i> Assessment Completed
                                            </div>
                                        @elseif($quizResult && !$quizResult->passed && !request()->has('retake'))
                                            <div class="alert alert-danger rounded-4 small mb-0 border-0 shadow-sm">
                                                <i class="bi bi-exclamation-circle-fill me-2"></i> Assessment Failed
                                            </div>
                                        @else
                                            <div class="alert alert-primary rounded-4 small mb-0 border-0 shadow-sm">
                                                <i class="bi bi-info-circle-fill me-2"></i> Scroll down to start assessment
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @else
                                {{-- NOT ENROLLED --}}
                                <h3 class="h4 fw-bold mb-2">Ready to Start?</h3>
                                <p class="text-muted mb-4">Enroll today to access all lessons and earn your official certification.</p>

                                @auth
                                    <form method="POST" action="{{ route('education.enroll', $course->slug) }}">
                                        @csrf
                                        <button class="btn btn-brand w-100 py-3 rounded-pill fw-bold shadow-lg transform-hover">
                                            Enroll in Course <i class="bi bi-arrow-right ms-2"></i>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 py-3 rounded-pill fw-bold">
                                        Log in to Enroll
                                    </a>
                                @endauth
                            @endif

                            <hr class="my-4 text-muted opacity-25">

                            <ul class="list-unstyled text-start small text-secondary mb-0 d-flex flex-column gap-3">
                                <li class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-success"></i> Self-paced learning</li>
                                <li class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-success"></i> Expert instructors</li>
                                <li class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-success"></i> Certificate of completion</li>
                                <li class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-success"></i> Access on any device</li>
                            </ul>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
