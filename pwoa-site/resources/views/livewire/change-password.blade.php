<x-slot name="header">
    <h2 class="h4 mb-0 text-dark">
        <i class="bi bi-shield-lock me-2 text-primary"></i> {{ __('Security Settings') }}
    </h2>
</x-slot>

<div class="row">
    <div class="col-lg-6 offset-lg-3">
        @if(session('status'))
            <div class="alert alert-success border-0 shadow-sm d-flex align-items-center rounded-4 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                <div class="fw-bold">{{ session('status') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-5">
                    <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-key fs-2"></i>
                    </div>
                    <h5 class="fw-bold">Update Password</h5>
                    <p class="text-muted small">Ensure your account is using a long, random password to stay secure.</p>
                </div>

                <form wire:submit.prevent="updatePassword">
                    <div class="mb-4">
                        <label for="current_password" class="form-label small fw-bold text-muted text-uppercase">Current Password</label>
                        <input type="password" id="current_password" wire:model="current_password" class="form-control bg-light border-0 p-3 @error('current_password') is-invalid @enderror">
                        @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label small fw-bold text-muted text-uppercase">New Password</label>
                        <input type="password" id="password" wire:model="password" class="form-control bg-light border-0 p-3 @error('password') is-invalid @enderror">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-5">
                        <label for="password_confirmation" class="form-label small fw-bold text-muted text-uppercase">Confirm New Password</label>
                        <input type="password" id="password_confirmation" wire:model="password_confirmation" class="form-control bg-light border-0 p-3">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2" wire:loading.attr="disabled">
                        <span wire:loading wire:target="updatePassword" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <i class="bi bi-shield-check me-2" wire:loading.remove wire:target="updatePassword"></i> 
                        <span wire:loading wire:target="updatePassword">Updating...</span>
                        <span wire:loading.remove wire:target="updatePassword">Update Security</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
