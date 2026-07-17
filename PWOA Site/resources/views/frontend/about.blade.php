@extends('layouts.front')

@section('title', 'About PWOA')

@section('content')

@push('styles')
<style>
    .glass-card-premium {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 1.5rem;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .glass-card-premium:hover {
        box-shadow: 0 20px 40px -8px rgba(15, 23, 42, 0.1);
        border-color: var(--ag-primary);
        background: #fff;
    }
    .text-shadow-premium {
        text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }
    .benefit-card {
        padding: 2rem;
        border-radius: 1.25rem;
        background: #fff;
        border: 1px solid rgba(0,0,0,0.05);
        height: 100%;
        transition: all 0.3s ease;
    }
    .benefit-card:hover {
        background: var(--ag-primary);
        color: #fff;
    }
    .benefit-card:hover p, .benefit-card:hover h3 {
        color: #fff !important;
    }
    .benefit-card:hover .feature-icon-wrapper {
        background: rgba(255,255,255,0.2);
        color: #fff;
    }
    .manifesto-bg {
        background: radial-gradient(circle at top right, rgba(0, 149, 215, 0.05), transparent),
                    radial-gradient(circle at bottom left, rgba(140, 198, 63, 0.05), transparent);
    }
</style>
@endpush

    <!-- Hero Section -->
    <section class="inner-page-hero bg-hero-about text-center d-flex align-items-center" style="min-height: 60vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9" data-aos="fade-up" data-aos-duration="1000">
                    <span class="badge rounded-pill bg-white text-dark px-3 py-2 mb-4 shadow-sm fw-semibold ls-wide text-uppercase" data-aos="zoom-in" data-aos-delay="200">
                      About Pressure Washers Of America
                    </span>
                    <h1 class="display-3 fw-bold mb-4 text-white text-shadow-premium">
                     Raising the Standard for Pressure Washing Professionals Nationwide
                    </h1>
                    <p class="lead text-white-50 px-md-5 mx-auto mb-5" style="max-width: 800px;" data-aos="fade-up" data-aos-delay="400">
                     Pressure Washers Of America exists to bring certification, accountability, and structure to an industry that has long operated without consistent standards.
                    </p>
                    <div class="mt-4 text-white-50">
                        <i class="bi bi-chevron-down fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Mission, Vision, Values -->
    <section class="py-5 bg-white">
        <div class="container pb-5">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Our Core Principles</h2>
                <div class="bg-primary mx-auto mb-4" style="width: 60px; height: 4px; border-radius: 2px;"></div>
                <p class="text-muted lead">The foundation of everything we build for the industry.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="glass-card-premium h-100 p-4 text-center">
                        <div class="card-body">
                            <div class="feature-icon-wrapper bg-primary-soft shadow-sm">
                                <i class="bi bi-bullseye"></i>
                            </div>
                            <h3 class="h4 fw-bold mb-3">Mission</h3>
                            <p class="text-muted mb-0">To establish clear standards, certification, and accountability for pressure washing professionals across the United States. </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card-premium h-100 p-4 text-center">
                        <div class="card-body">
                            <div class="feature-icon-wrapper bg-secondary-soft shadow-sm">
                                <i class="bi bi-eye"></i>
                            </div>
                            <h3 class="h4 fw-bold mb-3">Vision</h3>
                            <p class="text-muted mb-0">A future where every pressure washing contractor is trained, verified, and trusted by customers nationwide. </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card-premium h-100 p-4 text-center">
                        <div class="card-body">
                            <div class="feature-icon-wrapper bg-accent-soft shadow-sm">
                                <i class="bi bi-gem"></i>
                            </div>
                            <h3 class="h4 fw-bold mb-3">Values</h3>
                            <p class="text-muted mb-0">Professionalism, accountability, environmental responsibility, and a commitment to raising industry standards. </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 manifesto-bg">
    <div class="container py-5">
        <div class="row justify-content-center text-center">
            <div class="col-lg-10">
                <div class="feature-icon-wrapper bg-primary-soft mx-auto mb-4">
                    <i class="bi bi-award"></i>
                </div>
                <h2 class="display-4 fw-bold mb-4">Built to Set the Standard</h2>
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <p class="lead text-secondary mb-4 fs-4 fw-medium">
                            Pressure Washers Of America was created to bring structure, certification, and accountability to the pressure washing industry. 
                        </p>
                        <p class="text-muted fs-5 mb-0">
                            As the industry continues to grow, PWOA is focused on establishing clear standards, improving professionalism, and providing contractors with the systems needed to operate at a higher level. Through education, certification programs, and a connected professional network, we are building a stronger foundation for both contractors and customers nationwide.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- More Than an Association -->


    <!-- Why We Exist Section -->
    <section class="py-5 bg-light overflow-hidden">
        <div class="container py-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 order-2 order-lg-1" data-aos="fade-right">
                    <div class="pe-lg-4">
                        <span class="badge rounded-pill badge-soft-primary px-3 py-2 mb-3 fw-semibold ls-wide text-uppercase">Our Story</span>
                        <h2 class="display-5 fw-bold mb-4">Why Pressure Washers Of America Exists </h2>
                        <p class="lead text-secondary mb-4">The pressure washing industry has grown rapidly, but without consistent standards, certification, or accountability.
                            This has led to untrained operators, property damage, water waste, and a lack of trust between contractors and customers. </p>
                        <p class="text-muted mb-4 fs-5">Pressure Washers Of America was created to solve this problem. By bringing structure, education, and professional
                            standards to the industry, PWOA helps contractors operate at a higher level while giving customers confidence in who they hire.
                        </p>

                        <a href="{{ route('membership.index') }}" class="btn btn-brand btn-lg btn-premium mt-2 px-4 py-3">
                            Join the Community <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 order-1 order-lg-2" data-aos="fade-left">
                    <div class="position-relative">
                        <img src="{{ asset('images/frontend/about_mission_img.png') }}" alt="Contractors collaborating"
                            class="img-fluid rounded-4 image-card-shadow">
                        <div class="position-absolute bg-brand rounded-circle"
                            style="width: 120px; height: 120px; bottom: -30px; left: -30px; z-index: -1; opacity: 0.15; filter: blur(20px);">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 overflow-hidden">
        <div class="container py-5">
        <div class="row align-items-center g-5">
            <!-- Left Content -->
            <div class="col-lg-6" data-aos="fade-left">
                <span class="badge rounded-pill badge-soft-primary px-3 py-2 mb-3 fw-semibold ls-wide text-uppercase">
                    Industry Infrastructure
                </span>
                <h2 class="display-5 fw-bold mb-4">
                    More Than a Pressure Washing Organization
                </h2>
                <div class="bg-primary mb-4" style="width: 60px; height: 4px; border-radius: 2px;"></div>
                <p class="lead text-secondary mb-4">
                    Pressure Washers Of America is not just another industry association. It is a modern infrastructure designed to support the long-term growth of the pressure washing industry.
                </p>
                <p class="text-muted fs-5 mb-0">
                    Through certification programs, a verified contractor network, and a blockchain-backed governance system, members don’t just participate—they help shape the standards, policies, and future of the industry.
                </p>
            </div>
            <!-- Right Visual -->
            <div class="col-lg-6" data-aos="fade-right">
                <div class="position-relative">
                    <div class="glass-card-premium p-4 p-md-5 border-0 bg-light shadow-sm">
                        <div class="row g-4">
                            <div class="col-6">
                                <div class="text-center p-4 rounded-4 bg-white shadow-sm h-100 hover-scale">
                                    <div class="feature-icon-wrapper bg-primary-soft mx-auto mb-3">
                                        <i class="bi bi-patch-check"></i>
                                    </div>
                                    <h3 class="h6 fw-bold mb-2">Certification</h3>
                                    <p class="small text-muted mb-0">Verified professional standards</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-4 rounded-4 bg-white shadow-sm h-100">
                                    <div class="feature-icon-wrapper bg-secondary-soft mx-auto mb-3">
                                        <i class="bi bi-diagram-3"></i>
                                    </div>
                                    <h3 class="h6 fw-bold mb-2">Network</h3>
                                    <p class="small text-muted mb-0">Connected contractor ecosystem</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-4 rounded-4 bg-white shadow-sm h-100">
                                    <div class="feature-icon-wrapper bg-accent-soft mx-auto mb-3">
                                        <i class="bi bi-shield-check"></i>
                                    </div>
                                    <h3 class="h6 fw-bold mb-2">Accountability</h3>
                                    <p class="small text-muted mb-0">Industry trust and compliance</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-4 rounded-4 bg-white shadow-sm h-100">
                                    <div class="feature-icon-wrapper bg-primary-soft mx-auto mb-3">
                                        <i class="bi bi-cpu"></i>
                                    </div>
                                    <h3 class="h6 fw-bold mb-2">Governance</h3>
                                    <p class="small text-muted mb-0">Blockchain-backed systems</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Decorative Background Shape -->
                    <div class="position-absolute top-50 start-50 translate-middle rounded-circle bg-primary"
                        style="width: 320px; height: 320px; opacity: 0.04; z-index: -1;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- What Members Get -->
    <section class="py-5 bg-white">
        <div class="container py-5">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <h2 class="display-5 fw-bold mb-3">What Members Get</h2>
                    <div class="bg-primary mx-auto mb-4" style="width: 60px; height: 4px; border-radius: 2px;"></div>
                    <p class="lead text-muted">Joining Pressure Washers Of America provides contractors with the tools, training,
                        and visibility needed to grow their business, build trust, and operate at a professional standard. </p>
                </div>
            </div>

            <div class="row g-4 pt-3">
                <div class="col-md-6 col-lg-4">
                    <div class="benefit-card shadow-sm">
                        <div class="feature-icon-wrapper bg-primary-soft mb-4">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-3">Professional Directory Visibility</h3>
                        <p class="text-muted small mb-0">Get listed in our nationwide directory. Let customers find you as a trusted, verified professional in your local area.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="benefit-card shadow-sm">
                        <div class="feature-icon-wrapper bg-secondary-soft mb-4">
                            <i class="bi bi-mortarboard"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-3">Certification & Training Programs</h3>
                        <p class="text-muted small mb-0">Access industry-leading courses. Earn badges and certifications to stand out from uncertified competitors.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="benefit-card shadow-sm">
                        <div class="feature-icon-wrapper bg-accent-soft mb-4">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-3">Compliance & Industry Standards</h3>
                        <p class="text-muted small mb-0">Stay ahead of EPA and local regulations. Get templates, wash water recovery guides, and safety protocols.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="benefit-card shadow-sm">
                        <div class="feature-icon-wrapper bg-primary-soft mb-4">
                            <i class="bi bi-tags"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-3">Vendor & Partner Access</h3>
                        <p class="text-muted small mb-0">Unlock exclusive discounts on equipment, chemicals, insurance, and marketing services through our vendor network.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="benefit-card shadow-sm">
                        <div class="feature-icon-wrapper bg-secondary-soft mb-4">
                            <i class="bi bi-people"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-3">Events & Networking</h3>
                        <p class="text-muted small mb-0">Attend virtual webinars and in-person summits. Connect, share knowledge, and partner with top operators.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="benefit-card shadow-sm">
                        <div class="feature-icon-wrapper bg-accent-soft mb-4">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-3">Marketing & Trust-Building Assets</h3>
                        <p class="text-muted small mb-0">Use the PWOA member badge on your website, trucks, and proposals to instantly build trust with prospects.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Who PWOA Is Built For -->
    <section class="py-5 bg-light">
        <div class="container py-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="position-relative">
                        <img src="{{ asset('images/frontend/who_is_it_for.png') }}" alt="Contractors working" class="img-fluid rounded-4 image-card-shadow">
                    </div>
                </div>
                <div class="col-lg-6">
                    <h2 class="display-5 fw-bold mb-4">Who PWOA Is Built For</h2>
                    <div class="bg-primary mb-4" style="width: 60px; height: 4px; border-radius: 2px;"></div>
                    
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-4 bg-white rounded-4 shadow-sm border-0 h-100">
                                <i class="bi bi-house-door text-brand fs-2 mb-3 d-block"></i>
                                <h4 class="h6 fw-bold">Residential</h4>
                                <p class="small text-muted mb-0">Contractors focused on home services.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-4 bg-white rounded-4 shadow-sm border-0 h-100">
                                <i class="bi bi-building text-brand fs-2 mb-3 d-block"></i>
                                <h4 class="h6 fw-bold">Commercial</h4>
                                <p class="small text-muted mb-0">Industrial and business cleaning specialists.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-4 bg-white rounded-4 shadow-sm border-0 h-100">
                                <i class="bi bi-rocket-takeoff text-brand fs-2 mb-3 d-block"></i>
                                <h4 class="h6 fw-bold">Entrepreneurs</h4>
                                <p class="small text-muted mb-0">New operators entering the industry.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-4 bg-white rounded-4 shadow-sm border-0 h-100">
                                <i class="bi bi-graph-up text-brand fs-2 mb-3 d-block"></i>
                                <h4 class="h6 fw-bold">Established</h4>
                                <p class="small text-muted mb-0">Businesses looking to scale and gain credibility.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Call to Action -->
    <section class="py-5 position-relative overflow-hidden" data-aos="zoom-in">
        <div class="container py-4">
            <div class="bg-accent-gradient text-white text-center rounded-5 p-5 shadow-lg position-relative overflow-hidden">
                <div class="position-absolute top-0 start-0 w-100 h-100"
                    style="background: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); z-index: 0;">
                </div>
                <div class="position-relative" style="z-index: 1;">
                    <h2 class="display-5 fw-bold mb-3">Join the Organization Defining the Pressure Washing Industry </h2>
                    <p class="lead text-white-50 mb-5 px-md-5 mx-auto" style="max-width: 800px;">
                        Become part of a growing network of professionals committed to higher standards, better systems, and long-term success.
                    </p>
                    <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                        <a href="{{ route('membership.index') }}" class="btn btn-light btn-lg btn-premium text-dark fw-bold px-5 py-3">Become a Member</a>
                        <a href="{{ route('education.index') }}" class="btn btn-outline-light btn-lg btn-premium fw-bold px-5 py-3">Get Certified</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
