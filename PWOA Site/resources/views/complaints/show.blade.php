<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dark leading-tight">
            {{ __('Ticket Detail') }} - {{ $complaint->ticket_id }}
        </h2>
    </x-slot>

    <div class="container-fluid px-4 py-4">
        <div class="row g-4">
            <!-- Complaint Info -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 sticky-top" style="top: 2rem;">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-primary">Ticket Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="small text-muted text-uppercase fw-bold d-block mb-1">Ticket ID</label>
                            <span class="font-monospace h5">{{ $complaint->ticket_id }}</span>
                        </div>
                        <div class="mb-4">
                            <label class="small text-muted text-uppercase fw-bold d-block mb-1">Status</label>
                            <span class="badge rounded-pill bg-{{ $complaint->status->getColor() == 'warning' ? 'warning' : ($complaint->status->getColor() == 'danger' ? 'danger' : ($complaint->status->getColor() == 'success' ? 'success' : 'secondary')) }}">
                                {{ $complaint->status->getLabel() }}
                            </span>
                        </div>
                        <div class="mb-4">
                            <label class="small text-muted text-uppercase fw-bold d-block mb-1">Priority</label>
                            <span class="text-{{ $complaint->priority->getColor() == 'danger' ? 'danger' : ($complaint->priority->getColor() == 'warning' ? 'warning' : 'primary') }} fw-bold">
                                <i class="bi bi-flag-fill me-1"></i> {{ $complaint->priority->getLabel() }}
                            </span>
                        </div>
                        <div class="mb-4">
                            <label class="small text-muted text-uppercase fw-bold d-block mb-1">Category</label>
                            <p class="mb-0">{{ $complaint->category->name }}</p>
                        </div>
                        <div class="mb-0">
                            <label class="small text-muted text-uppercase fw-bold d-block mb-1">Submitted On</label>
                            <p class="mb-0">{{ $complaint->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-0">
                        <a href="{{ route('complaints.index') }}" class="btn btn-link btn-sm text-decoration-none p-0 text-muted">
                            <i class="bi bi-arrow-left"></i> Back to My Complaints
                        </a>
                    </div>
                </div>
            </div>

            <!-- Chat / Discussion -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h4 class="mb-1 fw-bold">{{ $complaint->title }}</h4>
                        <p class="text-muted mb-0">{{ $complaint->description }}</p>
                        
                        @if($complaint->attachment_path)
                            <div class="mt-3">
                                <a href="{{ asset('storage/' . $complaint->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill">
                                    <i class="bi bi-paperclip"></i> View Attachment
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Messages -->
                    <div class="card-body p-4 bg-light" style="max-height: 600px; overflow-y: auto;">
                        @forelse($complaint->replies as $reply)
                            <div class="d-flex mb-4 {{ $reply->admin_id ? 'justify-content-start' : 'justify-content-end' }}">
                                <div class="max-w-75">
                                    <div class="card {{ $reply->admin_id ? 'bg-white' : 'bg-primary text-white' }} border-0 shadow-sm rounded-4">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1 gap-4">
                                                <small class="fw-bold {{ $reply->admin_id ? 'text-primary' : 'text-white-50' }}">
                                                    {{ $reply->sender()->name }}
                                                </small>
                                                <small class="text-muted {{ $reply->admin_id ? '' : 'text-white-50' }}" style="font-size: 0.7rem;">
                                                    {{ $reply->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                            <p class="mb-0">{{ $reply->message }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-chat-dots fs-1 d-block mb-3 opacity-25"></i>
                                <p class="italic">No replies yet. Our team will get back to you soon.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Reply Form -->
                    <div class="card-footer bg-white p-4">
                        @if($complaint->status !== App\Enums\ComplaintStatus::CLOSED)
                            <form action="{{ route('complaints.reply', $complaint) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <textarea name="message" rows="3" class="form-control" placeholder="Type your message here..." required></textarea>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">
                                        Send Reply
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-light text-center mb-0 text-muted">
                                <i class="bi bi-lock-fill me-1"></i> This ticket is closed and cannot be replied to.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
