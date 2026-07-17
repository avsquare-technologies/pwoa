<div class="row g-4 h-100">
    <!-- Lesson Progress Sidebar -->
    <div class="col-xl-3 col-lg-4 d-none d-lg-block">
        <div class="card border-0 glass-card h-100 overflow-hidden">
            <div class="card-header bg-white border-0 p-4">
                <p class="small fw-bold text-muted text-uppercase mb-1 ls-wide">Course Progress</p>
                <h5 class="fw-bold mb-0 text-dark">{{ $course->title }}</h5>
            </div>
            <div class="card-body p-0 scroll-y" style="max-height: calc(100vh - 250px);">
                <div class="list-group list-group-flush">
                    @foreach($course->lessons as $index => $lesson)
                        @php
                            $isCompleted = in_array($lesson->id, $completedLessonIds);
                            $isActive = $currentLesson->id == $lesson->id;
                            $isLocked = $this->isLessonLocked($lesson);
                        @endphp
                        <button 
                            @if($isLocked) disabled style="opacity: 0.55; pointer-events: none;" @else wire:click="selectLesson({{ $lesson->id }})" @endif
                            class="list-group-item list-group-item-action border-0 py-3 d-flex align-items-center gap-3 {{ $isActive ? 'bg-primary-subtle text-primary fw-bold active-lesson-indicator' : ($isCompleted ? 'bg-success-subtle text-success-emphasis' : '') }}">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold {{ $isActive ? 'bg-primary text-white' : ($isCompleted ? 'bg-success text-white' : 'bg-light text-muted') }}"
                                style="width: 32px; height: 32px; min-width: 32px; font-size: 0.8rem;">
                                @if($isLocked)
                                    <i class="bi bi-lock-fill text-warning"></i>
                                @elseif($isCompleted && !$isActive)
                                    <i class="bi bi-check-lg"></i>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </div>
                            <div class="flex-grow-1 text-truncate small d-flex align-items-center gap-2">
                                {{ $lesson->title }}
                                @if($lesson->is_restricted)
                                    <i class="bi bi-lock-fill text-warning small"></i>
                                @endif
                            </div>
                            @if($isCompleted && !$isActive)
                                <i class="bi bi-check-circle-fill text-success small"></i>
                            @endif
                            @if($isActive)
                                <span class="spinner-grow spinner-grow-sm text-primary" role="status"></span>
                            @endif
                        </button>
                    @endforeach

                    @if($course->quiz)
                        <div class="p-3">
                            @php
                                $allCompleted = count($completedLessonIds) === $course->lessons->count();
                            @endphp
                            @if($allCompleted)
                                <a href="{{ route('quiz.engine', $course->quiz->id) }}"
                                    class="btn btn-dark w-100 py-3 rounded-4 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-patch-check-fill text-primary"></i>
                                    <span>Take Honor Exam</span>
                                </a>
                            @else
                                <button class="btn btn-dark w-100 py-3 rounded-4 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 opacity-50 cursor-not-allowed" disabled>
                                    <i class="bi bi-lock-fill text-muted"></i>
                                    <span>Exam Locked</span>
                                </button>
                                <p class="text-center text-muted small mt-2 mb-0" style="font-size: 0.75rem;">Complete all lessons to unlock the exam</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="col-xl-9 col-lg-8">
        <div class="card border-0 glass-card h-100 overflow-hidden">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                    <h2 class="fw-bold mb-0 display-6">{{ $currentLesson->title }}</h2>
                    <div class="d-flex gap-2">
                        <button wire:click="previousLesson"
                            class="btn btn-outline-light text-dark border p-2 rounded-circle" {{ $course->lessons->first()->id == $currentLesson->id ? 'disabled' : '' }} title="Previous">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button wire:click="nextLesson"
                            class="btn btn-outline-light text-dark border p-2 rounded-circle" {{ $course->lessons->last()->id == $currentLesson->id ? 'disabled' : '' }} title="Next">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>

                @if($isLocked)
                    <div class="restricted-content-wrapper text-center py-5 my-5 px-4 rounded-5 shadow-lg border-0" 
                        style="background: linear-gradient(145deg, #1a1c1e, #2d3436); color: #fff;">
                        <div class="premium-badge mb-4">
                            <div class="badge-icon bg-warning text-dark rounded-circle d-inline-flex align-items-center justify-content-center shadow" 
                                style="width: 80px; height: 80px; font-size: 2.5rem;">
                                <i class="bi bi-gem"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-3 gold-text">Exclusive Content</h2>
                        <p class="lead mb-5 text-light-50 opacity-75">
                            This lesson is reserved for our <span class="fw-bold text-warning">Gold Members</span>. 
                            Unlock this and all other premium courses today.
                        </p>
                        <div class="d-grid gap-3 d-sm-flex justify-content-center">
                            <a href="#" class="btn btn-warning btn-lg px-5 py-3 rounded-pill fw-bold shadow-sm hvr-grow">
                                Unlock Now - Join Gold Member
                            </a>
                            <a href="#" class="btn btn-outline-light btn-lg px-4 py-3 rounded-pill fw-bold border-opacity-25">
                                View Plans
                            </a>
                        </div>
                    </div>
                @else
                    @if($currentLesson->video_url)
                        <div class="ratio ratio-16x9 bg-dark shadow-sm mb-5 rounded-4 overflow-hidden">
                            <iframe src="{{ youtube_embed($currentLesson->video_url) }}"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        </div>
                    @endif

                    <div class="prose max-w-none text-muted lh-lg fs-5 mb-5 pb-5">
                        {!! $currentLesson->content !!}
                    </div>
                @endif

                <div class="pt-4 border-top d-flex justify-content-between align-items-center">
                    <div class="d-none d-md-block">
                        <p class="mb-0 small text-muted fw-bold text-uppercase">Up Next</p>
                        <p class="mb-0 fw-bold">
                            {{ $course->lessons->skipWhile(fn($l) => $l->id != $currentLesson->id)->skip(1)->first()->title ?? 'Honor Exam' }}
                        </p>
                    </div>
                    @if($course->lessons->last()->id == $currentLesson->id && $course->quiz)
                        <button wire:click="startExam"
                            class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow-sm hvr-grow">
                            Start Final Exam <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    @else
                        <button wire:click="nextLesson"
                            class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow-sm hvr-grow" {{ $course->lessons->last()->id == $currentLesson->id ? 'disabled' : '' }}>
                            Continue Learning <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

