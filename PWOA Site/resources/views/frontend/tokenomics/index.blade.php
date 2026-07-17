@extends('layouts.front')

@section('title', 'Tokenomics')
@section('meta_description', 'PWOA Tokenomics. Learn about our token model designed around participation, community value, and ecosystem benefits.')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endpush

@section('content')

    <!-- Hero Section -->
    <section class="inner-page-hero bg-hero-tokenomics text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 position-relative" style="z-index: 2;" data-aos="fade-up" data-aos-duration="1000">
                    <span class="badge rounded-pill bg-white text-dark px-3 py-2 mb-4 shadow-sm fw-semibold ls-wide text-uppercase" data-aos="zoom-in" data-aos-delay="200">
                        Tokenomics
                    </span>
                    <h1 class="display-3 fw-bold mb-4 text-white text-shadow">Value driven by<br>the community.</h1>
                    <p class="lead text-white-50 px-md-5 mx-auto mb-5" style="max-width: 800px;" data-aos="fade-up" data-aos-delay="400">
                        The PWOA token model is designed around participation and ecosystem growth. Members earn tokens through engagement and can use them for ecosystem benefits, education, and event participation.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Value Pillars -->
    <section class="py-5 bg-bg">
        <div class="container py-5">
            <div class="row g-4 mb-5">
                <!-- Earn -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card glass-card h-100 border-0 p-4 p-xl-5 hover-scale text-center">
                        <div class="bg-success-subtle text-success rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="bi bi-piggy-bank-fill fs-1"></i>
                        </div>
                        <h3 class="h4 fw-bold mb-3">Earn</h3>
                        <p class="text-secondary mb-0">Rewards can be tied to active membership, course completion, referrals, and robust community participation.</p>
                    </div>
                </div>

                <!-- Use -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card glass-card h-100 border-0 p-4 p-xl-5 hover-scale text-center">
                        <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="bi bi-arrow-repeat fs-1"></i>
                        </div>
                        <h3 class="h4 fw-bold mb-3">Use</h3>
                        <p class="text-secondary mb-0">Tokens can support event access, exclusive ecosystem perks, professional certifications, or future member-facing utilities.</p>
                    </div>
                </div>

                <!-- Grow -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card glass-card h-100 border-0 p-4 p-xl-5 hover-scale text-center">
                        <div class="bg-info-subtle text-info rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="bi bi-rocket-takeoff-fill fs-1"></i>
                        </div>
                        <h3 class="h4 fw-bold mb-3">Grow</h3>
                        <p class="text-secondary mb-0">A sustainable distribution model helps reinforce community activity and long-term program alignment for all members.</p>
                    </div>
                </div>
            </div>

            <!-- Allocation Breakdown -->
            <div class="row justify-content-center mt-5" data-aos="fade-up" data-aos-delay="400">
                <div class="col-lg-10">
                    <div class="card glass-card border-0 shadow-sm p-4 p-lg-5">
                        <h2 class="h3 fw-bold mb-4 text-center">Illustrative Allocation</h2>
                        
                        <!-- Visual Progress Bar -->
                        <div class="progress mb-5 rounded-pill shadow-sm" style="height: 25px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 30%" title="Community Rewards: 30%">30%</div>
                            <div class="progress-bar bg-success" role="progressbar" style="width: 20%" title="Member Loyalty: 20%">20%</div>
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 20%" title="Development & Reserve: 20%">20%</div>
                            <div class="progress-bar bg-info" role="progressbar" style="width: 15%" title="Ecosystem & Partnerships: 15%">15%</div>
                            <div class="progress-bar bg-secondary" role="progressbar" style="width: 10%" title="Team & Advisors: 10%">10%</div>
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 5%" title="Initial Offering: 5%">5%</div>
                        </div>

                        <!-- Data List -->
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="bg-primary rounded-circle mt-1 me-3" style="width: 16px; height: 16px; min-width: 16px;"></div>
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h5 class="fw-bold mb-0">Community Rewards</h5>
                                            <span class="badge bg-primary-subtle text-primary rounded-pill">30%</span>
                                        </div>
                                        <p class="text-secondary small mb-0">Participation, engagement, and staking-style incentives.</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start mb-4">
                                    <div class="bg-success rounded-circle mt-1 me-3" style="width: 16px; height: 16px; min-width: 16px;"></div>
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h5 class="fw-bold mb-0">Member Loyalty</h5>
                                            <span class="badge bg-success-subtle text-success rounded-pill">20%</span>
                                        </div>
                                        <p class="text-secondary small mb-0">Retention and long-term member benefits.</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start mb-4">
                                    <div class="bg-warning rounded-circle mt-1 me-3" style="width: 16px; height: 16px; min-width: 16px;"></div>
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h5 class="fw-bold mb-0">Development & Reserve</h5>
                                            <span class="badge bg-warning-subtle text-warning rounded-pill">20%</span>
                                        </div>
                                        <p class="text-secondary small mb-0">Operational flexibility and roadmap support.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="bg-info rounded-circle mt-1 me-3" style="width: 16px; height: 16px; min-width: 16px;"></div>
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h5 class="fw-bold mb-0">Ecosystem & Partnerships</h5>
                                            <span class="badge bg-info-subtle text-info rounded-pill">15%</span>
                                        </div>
                                        <p class="text-secondary small mb-0">Vendor, event, and partner expansion.</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start mb-4">
                                    <div class="bg-secondary rounded-circle mt-1 me-3" style="width: 16px; height: 16px; min-width: 16px;"></div>
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h5 class="fw-bold mb-0">Team & Advisors</h5>
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill">10%</span>
                                        </div>
                                        <p class="text-secondary small mb-0">Long-term alignment.</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div class="bg-danger rounded-circle mt-1 me-3" style="width: 16px; height: 16px; min-width: 16px;"></div>
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h5 class="fw-bold mb-0">Initial Offering</h5>
                                            <span class="badge bg-danger-subtle text-danger rounded-pill">5%</span>
                                        </div>
                                        <p class="text-secondary small mb-0">Market entry and early community access.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                once: true,
                offset: 50,
                duration: 800,
            });
        });
    </script>
@endpush
