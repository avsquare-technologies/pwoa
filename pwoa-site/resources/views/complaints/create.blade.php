<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dark leading-tight">
            {{ __('Submit Complaint') }}
        </h2>
    </x-slot>

    <div class="container-fluid px-4 py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-primary">Submit New Complaint</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="title" class="form-label fw-bold">Subject</label>
                                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="What is the issue about?" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="category_id" class="form-label fw-bold">Category</label>
                                    <select name="category_id" id="category_id" class="form-select" required>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="priority" class="form-label fw-bold">Priority</label>
                                    <select name="priority" id="priority" class="form-select" required>
                                        <option value="low">Low</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label fw-bold">Detailed Description</label>
                                <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Please provide as much detail as possible..." required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="attachment" class="form-label fw-bold">Attachment (Optional)</label>
                                <input type="file" name="attachment" id="attachment" class="form-control @error('attachment') is-invalid @enderror">
                                <div class="form-text text-muted">Allowed: jpg, png, pdf, doc (Max 2MB)</div>
                                @error('attachment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-3">
                                <a href="{{ route('complaints.index') }}" class="text-muted text-decoration-none small">
                                    <i class="bi bi-arrow-left"></i> Back to List
                                </a>
                                <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">
                                    Submit Complaint
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
