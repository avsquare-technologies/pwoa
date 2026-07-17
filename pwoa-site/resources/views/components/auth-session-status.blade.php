@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'alert alert-success border-0 d-flex align-items-center p-3 rounded-3']) }} role="alert">
        <i class="bi bi-check-circle-fill me-2 text-success"></i>
        <div class="small fw-bold">{{ $status }}</div>
    </div>
@endif
