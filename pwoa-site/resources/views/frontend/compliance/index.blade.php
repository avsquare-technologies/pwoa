@extends('layouts.front')

@section('title', 'Compliance Center')
@section('meta_description', 'PWOA Compliance Center. Find resources for wastewater handling, documentation, safety, and local regulations.')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endpush

@section('content')

    <!-- Hero Section -->
    <section class="inner-page-hero bg-hero-compliance text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 position-relative" style="z-index: 2;" data-aos="fade-up" data-aos-duration="1000">
                    <span class="badge rounded-pill bg-white text-dark px-3 py-2 mb-4 shadow-sm fw-semibold ls-wide text-uppercase" data-aos="zoom-in" data-aos-delay="200">
                        Compliance Center
                    </span>
                    <h1 class="display-3 fw-bold mb-4 text-white text-shadow">Stay compliant.<br>Stay professional.</h1>
                    <p class="lead text-white-50 px-md-5 mx-auto mb-5" style="max-width: 800px;" data-aos="fade-up" data-aos-delay="400">
                        Use these practical references as a starting point for field compliance, wastewater handling, documentation, and risk reduction on every jobsite.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Area -->
    <section class="py-5 bg-bg">
        <div class="container py-4">

            <!-- Disclaimer Alert -->
            <div class="row justify-content-center mb-5" data-aos="fade-up">
                <div class="col-lg-10">
                    <div class="alert bg-white border border-warning border-opacity-50 shadow-sm rounded-4 d-flex align-items-center p-4"
                        role="alert">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-2 me-4"></i>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Important Disclaimer</h5>
                            <p class="mb-0 text-muted small">This page is informational and does not replace legal or local
                                regulatory advice. Always consult with your local authorities and legal counsel for specific
                                compliance requirements in your area of operation.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-5">
                <!-- Checklist Sidebar (Left) -->
                <div class="col-lg-4" data-aos="fade-right" data-aos-delay="100">
                    <div class="card glass-card border-0 shadow-sm sticky-lg-top" style="top: 100px;">
                        <div class="card-body p-4 p-xl-5">
                            <h3 class="h4 fw-bold mb-4 border-bottom pb-3">Quick Checklist</h3>
                            <ul class="list-unstyled mb-0 d-grid gap-3">
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success fs-5 me-3 mt-1"></i>
                                    <span class="text-secondary fw-medium">Document site conditions before work
                                        begins</span>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success fs-5 me-3 mt-1"></i>
                                    <span class="text-secondary fw-medium">Confirm wastewater collection and handling
                                        plan</span>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success fs-5 me-3 mt-1"></i>
                                    <span class="text-secondary fw-medium">Verify PPE (Personal Protective Equipment) is
                                        worn</span>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success fs-5 me-3 mt-1"></i>
                                    <span class="text-secondary fw-medium">Review and check local runoff requirements</span>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success fs-5 me-3 mt-1"></i>
                                    <span class="text-secondary fw-medium">Store job records safely for repeatable
                                        jobs</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Core Pillars Grid (Right) -->
                <div class="col-lg-8">
                    <div class="row g-4">
                        <!-- Wastewater -->
                        <div class="col-sm-6" data-aos="fade-up" data-aos-delay="200">
                            <div class="card glass-card h-100 border-0 p-4 hover-scale">
                                <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                                    style="width: 60px; height: 60px;">
                                    <i class="bi bi-droplet-half fs-3"></i>
                                </div>
                                <h3 class="h5 fw-bold mb-3">Wastewater & Reclaim</h3>
                                <p class="text-secondary mb-0">Build a repeatable workflow for water control, collection,
                                    reclaim, and disposal documentation to protect the environment and your business.</p>
                            </div>
                        </div>

                        <!-- Documentation -->
                        <div class="col-sm-6" data-aos="fade-up" data-aos-delay="300">
                            <div class="card glass-card h-100 border-0 p-4 hover-scale">
                                <div class="bg-success-subtle text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                                    style="width: 60px; height: 60px;">
                                    <i class="bi bi-file-earmark-check-fill fs-3"></i>
                                </div>
                                <h3 class="h5 fw-bold mb-3">Jobsite Documentation</h3>
                                <p class="text-secondary mb-0">Create field-ready Standard Operating Procedures (SOPs),
                                    incident logs, and comprehensive pre-job inspection records for your crews.</p>
                            </div>
                        </div>

                        <!-- Chemicals -->
                        <div class="col-sm-6" data-aos="fade-up" data-aos-delay="400">
                            <div class="card glass-card h-100 border-0 p-4 hover-scale">
                                <div class="bg-warning-subtle text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                                    style="width: 60px; height: 60px;">
                                    <i class="bi bi-exclamation-octagon-fill fs-3"></i>
                                </div>
                                <h3 class="h5 fw-bold mb-3">Chemical Handling</h3>
                                <p class="text-secondary mb-0">Keep SDS (Safety Data Sheets) references, dilution guidance,
                                    storage controls, and crew safety training procedures accurate and up to date.</p>
                            </div>
                        </div>

                        <!-- Local Regs -->
                        <div class="col-sm-6" data-aos="fade-up" data-aos-delay="500">
                            <div class="card glass-card h-100 border-0 p-4 hover-scale">
                                <div class="bg-info-subtle text-info rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                                    style="width: 60px; height: 60px;">
                                    <i class="bi bi-shield-check fs-3"></i>
                                </div>
                                <h3 class="h5 fw-bold mb-3">Local Regulations</h3>
                                <p class="text-secondary mb-0">Always confirm specific city, county, site, and HOA
                                    requirements before starting any new, recurring, or commercial contract work.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Membership CTA -->
    <section class="py-5 border-top bg-white">
        <div class="container py-5 text-center" data-aos="zoom-in">
            <h2 class="fw-bold mb-4">Need comprehensive compliance resources?</h2>
            <p class="text-secondary mb-5 mx-auto" style="max-width: 600px;">
                PWOA Members gain exclusive access to our extensive library of compliance templates, SOPs, and regulatory
                guides designed specifically for pressure washing professionals.
            </p>
            <a href="{{ route('membership.index') }}"
                class="btn btn-brand btn-lg px-5 py-3 fw-bold rounded-pill shadow-sm hover-scale">
                Join PWOA for Member Resources
            </a>
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
