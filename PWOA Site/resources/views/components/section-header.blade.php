@props(['badge' => null, 'title', 'subtitle' => null, 'center' => true])
<div class="mb-4 {{ $center ? 'text-center' : '' }}">
    @if($badge)
    <div class="mb-2"><span class="badge rounded-pill badge-soft-primary px-3 py-2 text-uppercase">{{ $badge }}</span></div>
    @endif
    <h2 class="display-6 fw-bold mb-2">{{ $title }}</h2>
    @if($subtitle)
    <p class="lead text-secondary {{ $center ? 'mx-auto' : '' }}" style="max-width: 760px;">{{ $subtitle }}</p>
    @endif
</div>
