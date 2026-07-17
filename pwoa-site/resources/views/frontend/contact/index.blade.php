@extends('layouts.front')

@section('title', 'Contact PWOA')
@section('meta_description', 'Get in touch with PWOA. Questions about membership, events, education, or partnerships? Our team is here to help.')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endpush

@section('content')

    <!-- Hero Section -->
    <section class="inner-page-hero bg-hero-contact text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 position-relative" style="z-index: 2;" data-aos="fade-up" data-aos-duration="1000">
                    <span
                        class="badge rounded-pill bg-white text-dark px-3 py-2 mb-4 shadow-sm fw-semibold ls-wide text-uppercase"
                        data-aos="zoom-in" data-aos-delay="200">
                        Contact Us
                    </span>
                    <h1 class="display-3 fw-bold mb-4 text-white text-shadow">Get in touch with PWOA.</h1>
                    <p class="lead text-white-50 px-md-5 mx-auto mb-5" style="max-width: 800px;" data-aos="fade-up"
                        data-aos-delay="400">
                        Questions about membership, events, education, or partnerships? Send us a message and our team will
                        get back to you shortly.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-bg">
        <div class="container py-5">

            <!-- Contact Info Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card glass-card h-100 border-0 p-4 text-center hover-scale">
                        <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-4"
                            style="width: 70px; height: 70px;">
                            <i class="bi bi-envelope-fill fs-2"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-2">Email Us</h3>
                        <p class="text-secondary mb-0">info@pwoa.org</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card glass-card h-100 border-0 p-4 text-center hover-scale">
                        <div class="bg-success-subtle text-success rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-4"
                            style="width: 70px; height: 70px;">
                            <i class="bi bi-telephone-fill fs-2"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-2">Call Us</h3>
                        <p class="text-secondary mb-0">866-920-PWOA</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card glass-card h-100 border-0 p-4 text-center hover-scale">
                        <div class="bg-warning-subtle text-warning rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-4"
                            style="width: 70px; height: 70px;">
                            <i class="bi bi-clock-fill fs-2"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-2">Business Hours</h3>
                        <p class="text-secondary mb-0">Mon-Fri, 9 AM to 5 PM ET</p>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10" data-aos="fade-up" data-aos-delay="400">
                    <div class="card glass-card border-0 shadow-sm p-4 p-lg-5">
                        <div class="text-center mb-5">
                            <h2 class="fw-bold mb-2">Send a Message</h2>
                            <p class="text-secondary">Fill out the form below and we'll route your inquiry to the right
                                department.</p>
                        </div>

                        @if(session('contact_success'))
                            <div class="alert alert-success border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center">
                                <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                                <div>{{ session('contact_success') }}</div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('contact.send') }}" class="row g-4">
                            @csrf
                            <div class="col-md-6">
                                <label class="form-label-premium">Inquiry Type</label>
                                <select class="form-select-premium @error('inquiry_type') is-invalid @enderror"
                                    name="inquiry_type">
                                    <option value="">Select one</option>
                                    <option value="membership" @selected(old('inquiry_type') === 'membership')>Membership
                                    </option>
                                    <option value="education" @selected(old('inquiry_type') === 'education')>Education
                                    </option>
                                    <option value="events" @selected(old('inquiry_type') === 'events')>Events</option>
                                    <option value="vendor" @selected(old('inquiry_type') === 'vendor')>Vendor</option>
                                    <option value="other" @selected(old('inquiry_type') === 'other')>Other</option>
                                </select>
                                @error('inquiry_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-premium">Subject</label>
                                <input type="text" class="form-control-premium @error('subject') is-invalid @enderror"
                                    name="subject" value="{{ old('subject') }}" placeholder="What is this regarding?">
                                @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-premium">Your Name</label>
                                <input type="text" class="form-control-premium @error('name') is-invalid @enderror"
                                    name="name" value="{{ old('name') }}" placeholder="Full Name">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-premium">Email Address</label>
                                <input type="email" class="form-control-premium @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" placeholder="email@example.com">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-premium">Phone Number</label>
                                <input type="text" class="form-control-premium" name="phone" value="{{ old('phone') }}"
                                    placeholder="(Optional)">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-premium">Company Name</label>
                                <input type="text" class="form-control-premium" name="company" value="{{ old('company') }}"
                                    placeholder="(Optional)">
                            </div>
                            <div class="col-12">
                                <label class="form-label-premium">Message</label>
                                <textarea class="form-control-premium @error('message') is-invalid @enderror" rows="5"
                                    name="message" placeholder="How can we help you?">{{ old('message') }}</textarea>
                                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 text-center mt-5">
                                <button class="btn btn-brand btn-lg px-5 py-3 fw-bold rounded-pill shadow-sm hover-scale"
                                    type="submit">
                                    Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <!-- AOS Animation Library JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            AOS.init({
                once: true,
                offset: 50,
                duration: 800,
            });
        });
    </script>
@endpush
