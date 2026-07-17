<div class="row justify-content-center pt-4">
    <div class="col-xl-12">
        <div class="card border-0 glass-card overflow-hidden">
            <div class="card-header bg-dark p-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 40px; height: 40px;">
                        <i class="bi bi-award text-white fs-5"></i>
                    </div>
                    <h5 class="text-white mb-0 fw-bold ls-tight">
                        {{ $quiz->title }}
                    </h5>
                </div>
                <div class="text-white-50 small fw-bold text-uppercase ls-wide">
                    Question {{ $currentQuestionIndex + 1 }} / {{ count($questions) }}
                </div>
            </div>

            @if(!$isCompleted)
                <div class="card-body p-4 p-md-5">
                    <div class="progress mb-5" style="height: 10px; border-radius: 10px; background: #e2e8f0;">
                        <div class="progress-bar bg-primary shadow-sm" style="width: {{ (($currentQuestionIndex + 1) / count($questions)) * 100 }}%; border-radius: 10px;"></div>
                    </div>

                    <div class="mb-5 pb-3">
                        <span class="badge bg-primary-subtle text-primary mb-3 px-3 py-2 rounded-pill fw-bold">Current Question</span>
                        <h2 class="fw-bold mb-2 text-dark ls-tight">{{ $questions[$currentQuestionIndex]->question_text }}</h2>
                    </div>

                    <div class="row g-3">
                        @foreach($questions[$currentQuestionIndex]->options as $index => $option)
                            <div class="col-12">
                                <button wire:click="submitAnswer('{{ $option['text'] }}')" 
                                    class="quiz-option-card w-100 text-start p-4 rounded-4 transition-all d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-4">
                                        <div class="option-index rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; min-width: 40px;">
                                            {{ chr(65 + $index) }}
                                        </div>
                                        <span class="fs-5 fw-medium">{{ $option['text'] }}</span>
                                    </div>
                                    <div wire:loading wire:target="submitAnswer('{{ $option['text'] }}')" class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="card-body p-4 p-md-5 text-center">
                    <div class="result-animation mb-5">
                        <div class="rounded-circle {{ $passed ? 'bg-success' : 'bg-danger' }} text-white d-flex align-items-center justify-content-center mx-auto shadow-lg mb-4" style="width: 120px; height: 120px;">
                            <i class="bi {{ $passed ? 'bi-trophy-fill' : 'bi-x-lg' }} display-3"></i>
                        </div>
                    </div>
                    
                    <span class="badge {{ $passed ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-4 py-2 mb-3 fw-bold fs-6">
                        RESULT: {{ $score }}%
                    </span>
                    <h1 class="display-5 fw-bold mb-4 text-dark">{{ $passed ? 'Exam Completed Successfully!' : 'Not Quite There Yet' }}</h1>
                    
                    <p class="text-muted mb-5 fs-5 max-w-600 mx-auto">
                        {{ $passed ? 'You have officially earned your certification for this module. Your professional profile has been updated with a verified badge.' : 'Unfortunately, you didn\'t reach the required ' . $quiz->pass_percentage . '% mark. Take a moment to review the course materials and try again.' }}
                    </p>

                    @if($passed && $certificate)
                        <div class="card border-0 bg-success-subtle rounded-4 mx-auto mb-5" style="max-width: 480px;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="bi bi-patch-check-fill text-white fs-5"></i>
                                    </div>
                                    <div class="text-start">
                                        <p class="small text-muted mb-0 fw-bold text-uppercase">Certificate Issued</p>
                                        <h6 class="fw-bold mb-0 text-success">{{ $certificate->certificate_number }}</h6>
                                    </div>
                                </div>
                                <div class="text-start small text-muted">
                                    <i class="bi bi-calendar3 me-1"></i> Issued on {{ $certificate->issued_at->format('F d, Y') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="d-flex gap-3 justify-content-center pt-4 border-top">
                        @if($passed)
                            <a href="{{ route('certificates.index') }}" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-sm">
                                View Certificate <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        @else
                            <button onclick="window.location.reload()" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-sm">
                                <i class="bi bi-arrow-clockwise me-2"></i> Retake Exam
                            </button>
                            <a href="{{ route('courses') }}" class="btn btn-outline-secondary rounded-pill px-5 py-3 fw-bold">
                                Back to Course
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

