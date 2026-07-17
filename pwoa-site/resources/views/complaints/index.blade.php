<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dark leading-tight">
            {{ __('My Complaints') }}
        </h2>
    </x-slot>

    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">My Complaints</h1>
            <a href="{{ route('complaints.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Submit New Complaint
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success shadow-sm border-0 mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="card shadow-sm border-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Ticket ID</th>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Date</th>
                            <th class="px-4 py-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $complaint)
                            <tr>
                                <td class="px-4 py-3 font-monospace fw-bold text-primary">{{ $complaint->ticket_id }}</td>
                                <td class="px-4 py-3">
                                    <span class="d-block fw-semibold">{{ $complaint->title }}</span>
                                    <small class="text-muted">{{ $complaint->category->name }}</small>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="badge rounded-pill bg-{{ $complaint->status->getColor() == 'warning' ? 'warning' : ($complaint->status->getColor() == 'danger' ? 'danger' : ($complaint->status->getColor() == 'success' ? 'success' : 'secondary')) }}">
                                        {{ $complaint->status->getLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-muted">
                                    {{ $complaint->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('complaints.show', $complaint) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-5 text-center text-muted italic">
                                    No complaints found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($complaints->hasPages())
                <div class="card-footer bg-white px-4 py-3">
                    {{ $complaints->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
