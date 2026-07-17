<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-1 text-dark fw-bold">My Professional Credentials</h2>
                <p class="text-muted small mb-0">Manage and share your official PWOA certifications.</p>
            </div>
            <div class="bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-bold small">
                <i class="bi bi-patch-check-fill me-1"></i> Verified Platform
            </div>
        </div>
    </x-slot>

    <div class="row g-4">
        @forelse($certificates as $cert)
            <div class="col-xl-4 col-md-6">
                <div class="card border-0 glass-card h-100 hover-scale overflow-hidden shadow-sm">
                    <div class="certificate-preview position-relative bg-dark d-flex align-items-center justify-content-center overflow-hidden" style="height: 200px;">
                        <!-- Mini Preview of Certificate Design -->
                        <div class="cert-mini-preview text-center p-4">
                            <div class="cert-border-inner border border-primary border-opacity-25 p-2">
                                <div class="bg-white p-3 rounded-1 shadow-sm">
                                    <i class="bi bi-award text-primary display-4 mb-2"></i>
                                    <h6 class="text-dark fw-bold mb-0" style="font-size: 0.6rem;">{{ $cert->course->title }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="position-absolute top-0 end-0 p-3">
                            <span class="badge bg-success shadow-sm rounded-pill px-3">Verified</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <p class="text-muted small text-uppercase fw-bold ls-wide mb-1">Issued on {{ $cert->issued_at->format('M d, Y') }}</p>
                            <h5 class="fw-bold mb-0 ls-tight">{{ $cert->course->title }}</h5>
                        </div>
                        <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                            <div class="small text-muted">
                                <span class="fw-bold text-dark">ID:</span> {{ substr($cert->certificate_number, -8) }}
                            </div>
                            <a href="{{ route('certificates.show', $cert->certificate_number) }}" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold shadow-sm">
                                View Full Certificate
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="opacity-25 mb-4">
                    <i class="bi bi-award" style="font-size: 5rem;"></i>
                </div>
                <h4 class="fw-bold">No certificates earned yet</h4>
                <p class="text-muted">Complete courses and pass honor exams to earn your official credentials!</p>
                <a href="{{ route('education.index') }}" class="btn btn-primary rounded-pill px-5 py-3 mt-3 fw-bold">
                    Explore Learning Center
                </a>
            </div>
        @endforelse
    </div>

</x-app-layout>
