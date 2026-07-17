<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="h4 mb-0 text-dark fw-bold">
                Business Administration
            </h2>
            @if($status)
                <span class="badge bg-{{ $status === 'approved' ? 'success' : 'warning' }}-subtle text-{{ $status === 'approved' ? 'success' : 'warning' }} rounded-pill px-3 py-2 fw-bold">
                    {{ ucfirst($status) }} Profile
                </span>
            @endif
        </div>
    </x-slot>

    <livewire:business.manage-business />
</x-app-layout>
