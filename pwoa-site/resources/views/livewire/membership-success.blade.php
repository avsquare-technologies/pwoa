<x-slot name="header">
    <h2 class="h4 mb-0 text-dark">
        <i class="bi bi-patch-check me-2 text-primary"></i> {{ __('Membership Success') }}
    </h2>
</x-slot>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card border-0 shadow-lg text-center overflow-hidden rounded-5 py-5 px-4" style="background: radial-gradient(circle at center, #ffffff 0%, #f0f7ff 100%);">
            <div class="card-body p-md-5">
                <!-- Multi-layer Success Icon -->
                <div class="position-relative d-inline-block mb-5">
                    <div class="bg-success opacity-10 rounded-circle position-absolute top-50 start-50 translate-middle" style="width: 140px; height: 140px;"></div>
                    <div class="bg-success opacity-25 rounded-circle position-absolute top-50 start-50 translate-middle" style="width: 110px; height: 110px;"></div>
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-lg" style="width: 80px; height: 80px; position: relative;">
                        <i class="bi bi-check-lg display-5"></i>
                    </div>
                </div>
                
                <h1 class="display-5 fw-bold mb-3">Welcome to PWOA Premium</h1>
                <p class="text-muted fs-5 mb-5 mx-auto" style="max-width: 500px;">Your annual membership is now active. You have full access to our professional business network.</p>
                
                <div class="row g-4 mb-5 text-start justify-content-center">
                    <div class="col-sm-auto">
                        <div class="d-flex align-items-center gap-2 text-dark fw-bold">
                            <i class="bi bi-lightning-charge text-warning"></i> Instant Access Enabled
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="d-flex align-items-center gap-2 text-dark fw-bold">
                            <i class="bi bi-shield-check text-info"></i> Verified Profile Active
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-5 py-3 rounded-4 shadow-sm fw-bold">
                        Go to Dashboard
                    </a>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-lg px-5 py-3 rounded-4 fw-bold">
                        Update Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
