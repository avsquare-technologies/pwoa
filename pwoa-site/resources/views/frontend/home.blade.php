@extends('layouts.front')

@section('title', 'Home')
@section('meta_description', 'PWOA connects pressure washing pros through membership, education, events, vendors, and compliance resources.')

@push('styles')
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endpush

@section('content')

    <!-- Hero Section -->
    <section class="home-hero">
        <div class="container py-4">
            <div class="row justify-content-center text-center">
                <div class="col-lg-9" data-aos="fade-up" data-aos-duration="1000">
                    <span
                        class="badge rounded-pill bg-white text-dark px-3 py-2 mb-4 shadow-sm fw-semibold ls-wide text-uppercase"
                        data-aos="zoom-in" data-aos-delay="300">Pressure Washers of America</span>
                    <h1 class="display-3 fw-bold mb-4 text-white">Raising the Standard for Professionals Shaping the Future</h1>
                    <p class="lead text-white-50 px-md-5 mb-5" data-aos="fade-up" data-aos-delay="200">Join a member-driven organization advancing certification, smarter systems, and a connected network built for long-term success in the pressure washing industry.</p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3" data-aos="fade-up" data-aos-delay="400">
                        <a href="{{ route('membership.index') }}"
                            class="btn btn-accent btn-lg px-md-5 fw-bold btn-premium w-100 w-sm-auto">Become a Member</a>
                        <a href="{{ route('contractors.index') }}"
                            class="btn btn-outline-light btn-lg px-md-5 fw-bold btn-premium w-100 w-sm-auto">Find a Contractor</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Floating Directories Section -->
    <div class="container floating-directories mb-5">
        <div class="row g-4">
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="card directory-card h-100 rounded-4 p-2">
                    <div class="card-body text-center">
                        <div class="fs-1 text-brand mb-3"><i class="bi bi-briefcase-fill"></i></div>
                        <h3 class="h5 fw-bold">Contractors</h3>
                        <p class="text-secondary small">Search residential, commercial, and specialty contractors.</p>
                        <a href="{{ route('contractors.index') }}"
                            class="stretched-link text-decoration-none fw-semibold">Browse <i
                                class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="card directory-card h-100 rounded-4 p-2">
                    <div class="card-body text-center">
                        <div class="fs-1 text-accent mb-3"><i class="bi bi-shop"></i></div>
                        <h3 class="h5 fw-bold">Vendors</h3>
                        <p class="text-secondary small">Discover suppliers and manufacturers serving pros.</p>
                        <a href="{{ route('vendors.index') }}"
                            class="stretched-link text-decoration-none fw-semibold text-accent">Browse <i
                                class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="card directory-card h-100 rounded-4 p-2">
                    <div class="card-body text-center">
                        <div class="fs-1 text-info mb-3"><i class="bi bi-journal-bookmark-fill"></i></div>
                        <h3 class="h5 fw-bold">Education</h3>
                        <p class="text-secondary small">See courses and certification paths for your team.</p>
                        <a href="{{ route('education.index') }}"
                            class="stretched-link text-decoration-none fw-semibold text-info">View <i
                                class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                <div class="card directory-card h-100 rounded-4 p-2">
                    <div class="card-body text-center">
                        <div class="fs-1 text-success mb-3"><i class="bi bi-shield-check"></i></div>
                        <h3 class="h5 fw-bold">Compliance</h3>
                        <p class="text-secondary small">Review operational checklists and guidance.</p>
                        <a href="{{ route('compliance.index') }}"
                            class="stretched-link text-decoration-none fw-semibold text-success">Open <i
                                class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Community/Membership Anchor Section -->
    {{-- <section class="py-5 bg-white">
        <div class="container py-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="position-relative">
                        <img src="{{ asset('images/frontend/home_contractor.png') }}" alt="PWOA Contractor"
                            class="img-fluid rounded-4 shadow-lg">
                        <!-- Decorator -->
                        <div class="position-absolute bg-accent rounded-circle"
                            style="width: 80px; height: 80px; top: -20px; right: -20px; z-index: -1; opacity: 0.2;"></div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="ps-lg-4">
                        <x-section-header badge="Membership" title="Pick the membership that matches your stage."
                            subtitle="Standard gives you access and visibility. Gold adds priority, more education access, and stronger member benefits." />

                        <div class="row g-4 mt-2">
                            <!-- Standard Tier -->
                            <div class="col-md-6">
                                <div class="card card-soft h-100 border-0 shadow-sm rounded-4">
                                    <div class="card-body p-4">
                                        <h3 class="h4 mb-2">Standard</h3>
                                        <p class="display-6 fw-bold mb-3 text-dark">$99<span
                                                class="fs-6 text-secondary fw-normal"> / year</span></p>
                                        <ul class="text-secondary mb-4 list-unstyled">
                                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>
                                                Directory listing</li>
                                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>
                                                Compliance resources</li>
                                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>
                                                Course access</li>
                                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Member event
                                                discounts</li>
                                        </ul>
                                        <a href="{{ route('membership.index') }}"
                                            class="btn btn-outline-brand w-100 rounded-pill">Join Standard</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Gold Tier -->
                            <div class="col-md-6">
                                <div class="card card-soft h-100 bg-white rounded-4 membership-card-gold">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h3 class="h4 mb-0 text-dark">Gold</h3>
                                            <span class="badge bg-warning text-dark px-2 py-1 rounded-pill">Most
                                                Popular</span>
                                        </div>
                                        <p class="display-6 fw-bold mb-3 text-dark">$299<span
                                                class="fs-6 text-secondary fw-normal"> / year</span></p>
                                        <ul class="text-secondary mb-4 list-unstyled">
                                            <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>
                                                Everything in Standard</li>
                                            <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>
                                                Priority directory</li>
                                            <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>
                                                Unlimited courses</li>
                                            <li><i class="bi bi-check-circle-fill text-warning me-2"></i> Higher event
                                                benefits</li>
                                        </ul>
                                        <a href="{{ route('membership.gold') }}"
                                            class="btn btn-warning w-100 rounded-pill fw-bold text-dark">Join Gold</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
<section class="py-5 bg-white">
    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="position-relative">
                    <img src="{{ asset('images/frontend/home_contractor.png') }}" alt="PWOA Contractor"
                        class="img-fluid rounded-4 shadow-lg">

                    <!-- Decorator -->
                    <div class="position-absolute bg-accent rounded-circle"
                        style="width: 80px; height: 80px; top: -20px; right: -20px; z-index: -1; opacity: 0.2;">
                    </div>
                </div>
            </div>

            <div class="col-lg-6" data-aos="fade-left">
                <div class="ps-lg-4">

                    <x-section-header
                        badge="Membership"
                        title="Join the professionals shaping the future of the industry."
                        subtitle="Whether you’re getting started or ready to lead, PWOA memberships are designed to provide real value, real visibility, and a voice through our blockchain-backed voting system." />

                    <div class="row g-4 mt-1">

                        <!-- Standard Tier -->
                        <div class="col-md-6">
                            <div class="card card-soft h-100 border-0 shadow-sm rounded-4">
                                <div class="card-body p-4 d-flex flex-column">

                                    <h3 class="h4 mb-2">Shape Standard</h3>

                                    <p class="display-6 fw-bold mb-3 text-dark">
                                        $99
                                        <span class="fs-6 text-secondary fw-normal"> / year</span>
                                    </p>

                                    <ul class="text-secondary mb-4 list-unstyled small">
                                        <li class="mb-2">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            Directory listing
                                        </li>

                                        <li class="mb-2">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            Compliance resources
                                        </li>

                                        <li class="mb-2">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            Course access
                                        </li>

                                        <li class="mb-2">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            Member event discounts
                                        </li>

                                        <li class="mb-2">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            Voting rights on industry standards and initiatives
                                        </li>

                                        <li>
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            Blockchain-backed voting system for transparency and accountability
                                        </li>
                                    </ul>

                                    <a href="{{ route('membership.index') }}"
                                        class="btn btn-outline-brand w-100 rounded-pill mt-auto">
                                        Join Standard
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Gold Tier -->
                        <div class="col-md-6">
                            <div class="card card-soft h-100 bg-white rounded-4 membership-card-gold">
                                <div class="card-body p-4 d-flex flex-column">

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h3 class="h4 mb-0 text-dark">Shape Gold</h3>

                                        <span class="badge bg-warning text-dark px-2 py-1 rounded-pill">
                                            Most Popular
                                        </span>
                                    </div>

                                    <p class="display-6 fw-bold mb-3 text-dark">
                                        $299
                                        <span class="fs-6 text-secondary fw-normal"> / year</span>
                                    </p>

                                    <ul class="text-secondary mb-4 list-unstyled small">

                                        <li class="mb-2">
                                            <i class="bi bi-check-circle-fill text-warning me-2"></i>
                                            Everything in Standard
                                        </li>

                                        <li class="mb-2">
                                            <i class="bi bi-check-circle-fill text-warning me-2"></i>
                                            Priority directory placement
                                        </li>

                                        <li class="mb-2">
                                            <i class="bi bi-check-circle-fill text-warning me-2"></i>
                                            Unlimited training & certification access
                                        </li>

                                        <li class="mb-2">
                                            <i class="bi bi-check-circle-fill text-warning me-2"></i>
                                            Enhanced event access & benefits
                                        </li>

                                        <li class="mb-2">
                                            <i class="bi bi-check-circle-fill text-warning me-2"></i>
                                            Gold Member recognition badge
                                        </li>

                                        <li class="mb-2">
                                            <i class="bi bi-check-circle-fill text-warning me-2"></i>
                                            Voting rights on industry standards and initiatives
                                        </li>

                                        <li class="mb-2">
                                            <i class="bi bi-check-circle-fill text-warning me-2"></i>
                                            Blockchain-backed voting system for transparency and accountability
                                        </li>

                                        <li>
                                            <i class="bi bi-check-circle-fill text-warning me-2"></i>
                                            Eligibility for leadership and governance roles within PWOA
                                        </li>
                                    </ul>

                                    <a href="{{ route('membership.gold') }}"
                                        class="btn btn-warning w-100 rounded-pill fw-bold text-dark mt-auto">
                                        Join Gold
                                    </a>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Main SEO Heading -->
                <div class="text-center mb-5">
                    <span class="badge bg-accent-subtle text-accent px-3 py-2 rounded-pill mb-3">
                        Industry Leadership
                    </span>
                    <h2 class="display-6 fw-bold text-dark mb-3">
                        Pressure Washing Organizations Setting the Industry Standard
                    </h2>
                    <p class="lead text-secondary">
                        Pressure Washers Of America (PWOA) is a national organization dedicated to advancing
                        the pressure washing and power washing industry through certification, education,
                        and professional standards.
                    </p>
                </div>
                <!-- Content Block 1 -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 p-lg-5">
                        <p class="text-secondary mb-4">
                            As more contractors enter the market, the need for a trusted pressure washing
                            organization has never been greater. Pressure Washers Of America provides a
                            centralized platform where professionals can access training, stay aligned with
                            industry best practices, and connect with a verified network of contractors
                            committed to delivering high-quality results.
                        </p>
                        <p class="text-secondary mb-0">
                            Unlike generic directories or informal groups, PWOA is built to establish real
                            standards that elevate both contractor performance and customer confidence across
                            the pressure washing industry.
                        </p>
                    </div>
                </div>
                <!-- Secondary Heading -->
                <div class="text-center my-5">
                    <h3 class="fw-bold text-dark mb-3">
                        Power Washing Organizations Built for Professionals
                    </h3>
                </div>
                <!-- Content Block 2 -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-lg-5">
                        <p class="text-secondary mb-4">
                            Not all pressure washing organizations and power washing organizations are created equal.
                            Many lack the structure, training systems, and accountability needed to truly support
                            contractors at scale.
                        </p>
                        <p class="text-secondary mb-4">
                            Pressure Washers Of America was created to solve this problem by providing a
                            professional environment where pressure washing contractors can access real education,
                            earn recognition through certification, and become part of a trusted network focused
                            on quality and long-term growth.
                        </p>
                        <p class="text-secondary mb-4">
                            Through its membership model, Pressure Washers Of America combines the benefits
                            of a traditional pressure washing organization with modern systems designed to
                            drive real results.
                        </p>
                        <p class="text-secondary mb-0">
                            From contractor directory exposure and certification programs to blockchain-backed
                            voting and governance, members are not just participants — they actively help shape
                            the future of the industry.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- Events Section -->
    <section class="py-5 bg-light">
        <div class="container py-5">
            <div class="d-flex flex-wrap justify-content-between align-items-end mb-5" data-aos="fade-up">
                <div>
                    <x-section-header badge="Events" title="See what is coming up next."
                        subtitle="Join virtual webinars and in-person summits." />
                </div>
                <a href="{{ route('events.index') }}" class="btn btn-outline-dark mb-3 mb-md-0">View All Events</a>
            </div>

            <div class="row g-4">
                @forelse($featuredEvents as $index => $event)
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ 100 * ($index % 3 + 1) }}">
                        <div class="card card-soft h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                            <!-- Placeholder image for event, falling back to a gradient -->
                            <div class="event-card-img w-100 bg-brand-gradient position-relative">
                                @if($event->has_nft_tickets)
                                    <span class="badge text-bg-warning position-absolute top-0 end-0 m-3 shadow">NFT
                                        Ticket</span>
                                @endif
                                <div class="position-absolute bottom-0 start-0 m-3">
                                    <span
                                        class="badge rounded-pill badge-soft-primary bg-white text-dark">{{ $event->category?->name ?? 'General' }}</span>
                                </div>
                            </div>
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="small text-brand fw-semibold mb-2"><i class="bi bi-calendar3 me-1"></i>
                                    {{ optional($event->starts_at)->format('M d, Y') }}</div>
                                <h3 class="h5 fw-bold mb-2">{{ $event->title }}</h3>
                                <p class="text-secondary small description-clamp flex-grow-1">
                                    {!! strip_tags($event->description) !!}
                                </p>
                                <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                                    <span class="small text-secondary fw-medium"><i
                                            class="bi {{ $event->is_virtual ? 'bi-laptop' : 'bi-geo-alt' }} me-1"></i>
                                        {{ $event->is_virtual ? 'Virtual Event' : ($event->location ?? 'TBA') }}</span>
                                    <a href="{{ route('events.show', $event->slug ?? '') }}"
                                        class="btn btn-sm btn-outline-brand rounded-pill px-3">Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-light border rounded-4">Events coming soon.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>


    <!-- Trusted Partners Section -->
    <section class="py-5 bg-white border-top border-bottom">
        <div class="container py-4 text-center">
            <h5 class="text-secondary fw-semibold mb-4 text-uppercase ls-wide" data-aos="fade-up">Trusted by Industry
                Leaders</h5>
            <div class="d-flex flex-wrap justify-content-center align-items-center gap-4 gap-md-5" data-aos="fade-up"
                data-aos-delay="200">
                <!-- Using placeholder svgs for logos -->
                <img src="{{ asset('images/frontend/logos/power-logo.png') }}" alt="Partner"
                    class="partner-logo">
                <img src="{{ asset('images/frontend/logos/eco-log.png') }}" alt="Partner"
                    class="partner-logo">
                     <img src="{{ asset('images/frontend/logos/image 6.png') }}" alt="Partner"
                    class="partner-logo">
                <img src="{{ asset('images/frontend/logos/output-onlinepngtools.png') }}" alt="Partner"
                    class="partner-logo">


                <img src="{{ asset('images/frontend/logos/BE_Logo.svg') }}" alt="Partner"
                    class="partner-logo">
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-5" style="background-color: #f8fafc;">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <x-section-header badge="Success Stories" title="Hear from our members."
                    subtitle="Discover how PWOA is helping contractors build better businesses." />
            </div>

            <div class="row g-4">
                <!-- Testimonial 1 -->
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card testimonial-card h-100 rounded-4 p-4 border-0">
                        <div class="text-warning mb-3">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <p class="fs-5 text-dark fst-italic mb-4">"Joining PWOA gave me the exact operational blueprints I
                            needed to scale my crew from 2 to 8 guys in a single year."</p>
                        <div class="d-flex align-items-center mt-auto">
                            <img src="https://ui-avatars.com/api/?name=Michael+T&background=0D8ABC&color=fff&rounded=true"
                                alt="Michael" class="testimonial-avatar me-3 shadow-sm">
                            <div>
                                <h6 class="fw-bold mb-0">Michael Thompson</h6>
                                <span class="small text-secondary">Elite Wash Pros</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Testimonial 2 -->
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card testimonial-card h-100 rounded-4 p-4 border-0">
                        <div class="text-warning mb-3">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <p class="fs-5 text-dark fst-italic mb-4">"The Gold membership pays for itself just from the vendor
                            discounts. But the community and education? Priceless."</p>
                        <div class="d-flex align-items-center mt-auto">
                            <img src="https://ui-avatars.com/api/?name=Sarah+J&background=F59E0B&color=fff&rounded=true"
                                alt="Sarah" class="testimonial-avatar me-3 shadow-sm">
                            <div>
                                <h6 class="fw-bold mb-0">Sarah Jenkins</h6>
                                <span class="small text-secondary">Crystal Clear Exteriors</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Testimonial 3 -->
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card testimonial-card h-100 rounded-4 p-4 border-0">
                        <div class="text-warning mb-3">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <p class="fs-5 text-dark fst-italic mb-4">"I finally have a place to find reliable answers to
                            compliance and insurance questions. It takes the guesswork out of ownership."</p>
                        <div class="d-flex align-items-center mt-auto">
                            <img src="https://ui-avatars.com/api/?name=David+R&background=10B981&color=fff&rounded=true"
                                alt="David" class="testimonial-avatar me-3 shadow-sm">
                            <div>
                                <h6 class="fw-bold mb-0">David Rodriguez</h6>
                                <span class="small text-secondary">ProWash Solutions</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="final-cta-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8" data-aos="zoom-in" data-aos-duration="800">
                    <h2 class="display-4 fw-bold mb-4 text-white">Stop washing alone.</h2>
                    <p class="lead text-white-50 mb-5 px-md-4">Join the strongest community of pressure washing
                        professionals. Access the education, vendor discounts, and network you need to dominate your market.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('membership.index') }}"
                            class="btn btn-brand btn-lg px-5 py-3 fw-bold rounded-pill shadow-lg">Become a Member Today</a>
                        <a href="{{ route('contact') }}"
                            class="btn btn-outline-light btn-lg px-5 py-3 fw-bold rounded-pill">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
<style>
    .membership-card-gold,
.card-soft {
    min-height: 100%;
}

.card-soft .card-body {
    height: 100%;
}

.card-soft ul li {
    line-height: 1.45;
}

.card-soft .btn {
    margin-top: auto;
}

@media (min-width: 992px) {
    .membership-card-gold,
    .card-soft {
        min-height: 590px;
    }
}
</style>

@push('scripts')
    <!-- AOS Animation Initialization -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            AOS.init({
                duration: 800,
                once: true,
                offset: 50,
                easing: 'ease-out-cubic',
            });
        });
    </script>
@endpush
