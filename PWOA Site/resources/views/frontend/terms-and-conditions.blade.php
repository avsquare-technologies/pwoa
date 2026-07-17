@extends('layouts.front')

@section('title', 'Terms & Conditions')
@section('meta_description', 'Terms & Conditions for Pressure Washers of America (PWOA). Review membership obligations, acceptable platform use, listing directories guidelines, and legal framework.')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        .terms-card {
            background: #fff;
            border-radius: 1.5rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }
        .terms-nav-link {
            transition: all 0.3s ease;
            font-weight: 500;
            display: block;
            padding: 0.5rem 0;
            border-left: 2px solid transparent;
            padding-left: 1rem;
        }
        .terms-nav-link:hover, .terms-nav-link.active {
            color: var(--ag-primary) !important;
            border-left-color: var(--ag-primary);
            font-weight: 600;
            text-decoration: none;
        }
        .terms-section {
            scroll-margin-top: 100px;
        }
        .terms-section h2 {
            position: relative;
            padding-bottom: 0.75rem;
            margin-bottom: 1.25rem;
        }
        .terms-section h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: var(--ag-primary);
            border-radius: 2px;
        }
    </style>
@endpush

@section('content')

    <!-- Hero Section -->
    <section class="inner-page-hero bg-hero-compliance text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 position-relative" style="z-index: 2;" data-aos="fade-up" data-aos-duration="1000">
                    <span class="badge rounded-pill bg-white text-dark px-3 py-2 mb-4 shadow-sm fw-semibold ls-wide text-uppercase" data-aos="zoom-in" data-aos-delay="200">
                        Legal & Compliance
                    </span>
                    <h1 class="display-3 fw-bold mb-4 text-white text-shadow">Terms & Conditions</h1>
                    <p class="lead text-white-50 px-md-5 mx-auto mb-3" style="max-width: 800px;" data-aos="fade-up" data-aos-delay="400">
                        Please read these terms carefully before accessing PWOA tools, certification systems, and directories.
                    </p>
                    <p class="text-white-50 small" data-aos="fade-up" data-aos-delay="500">
                        Last Updated: May 22, 2026
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Area -->
    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="row g-5">
                <!-- Sidebar (Left) -->
                <div class="col-lg-4" data-aos="fade-right" data-aos-delay="100">
                    <div class="card glass-card border-0 shadow-sm sticky-lg-top" style="top: 100px; z-index: 100;">
                        <div class="card-body p-4 p-xl-5">
                            <h3 class="h5 fw-bold mb-4 border-bottom pb-3">Table of Contents</h3>
                            <ul class="list-unstyled mb-0 d-grid gap-2">
                                <li><a href="#acceptance" class="text-secondary terms-nav-link">1. Acceptance of Terms</a></li>
                                <li><a href="#membership" class="text-secondary terms-nav-link">2. Membership & Accounts</a></li>
                                <li><a href="#directories" class="text-secondary terms-nav-link">3. Contractor Directory Guidelines</a></li>
                                <li><a href="#education" class="text-secondary terms-nav-link">4. Education & Certification</a></li>
                                <li><a href="#acceptable-use" class="text-secondary terms-nav-link">5. Acceptable Platform Use</a></li>
                                <li><a href="#payment-tokens" class="text-secondary terms-nav-link">6. Tokens & Financial Operations</a></li>
                                <li><a href="#liability" class="text-secondary terms-nav-link">7. Limitation of Liability</a></li>
                                <li><a href="#governing-law" class="text-secondary terms-nav-link">8. Governing Law</a></li>
                                <li><a href="#support" class="text-secondary terms-nav-link">9. Legal Contact</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Content Area (Right) -->
                <div class="col-lg-8" data-aos="fade-up" data-aos-delay="200">
                    <div class="card terms-card border-0 p-4 p-md-5">
                        
                        <!-- Section 1 -->
                        <div class="terms-section mb-5" id="acceptance">
                            <h2 class="h4 fw-bold text-dark"><i class="bi bi-file-earmark-check-fill text-brand me-2"></i> 1. Acceptance of Terms</h2>
                            <p class="text-muted lh-lg">
                                By registering for an account, purchasing PWOA tokens, applying for active membership, or accessing resources at Pressure Washers of America (PWOA), you signify your explicit agreement to follow and be bound by these Terms and Conditions. If you do not accept these provisions, you must immediately suspend platform use.
                            </p>
                        </div>

                        <!-- Section 2 -->
                        <div class="terms-section mb-5" id="membership">
                            <h2 class="h4 fw-bold text-dark"><i class="bi bi-person-check-fill text-brand me-2"></i> 2. Membership & Accounts</h2>
                            <p class="text-muted lh-lg">
                                Accessing certain certifications and directory privileges requires an active PWOA membership subscription.
                            </p>
                            <ul class="text-muted list-styled ps-4 mb-0">
                                <li class="mb-2"><strong>Account Accuracy:</strong> Members must supply fully accurate business names, license files, contact details, and update them proactively if details change.</li>
                                <li class="mb-2"><strong>Access Security:</strong> Credentials must be guarded. You are exclusively responsible for actions occurring under your logged account.</li>
                                <li class="mb-2"><strong>Tier Cancellation:</strong> Membership subscriptions can be cancelled via the User Dashboard under billing settings in accordance with standard billing intervals.</li>
                            </ul>
                        </div>

                        <!-- Section 3 -->
                        <div class="terms-section mb-5" id="directories">
                            <h2 class="h4 fw-bold text-dark"><i class="bi bi-journal-bookmark-fill text-brand me-2"></i> 3. Contractor Directory Guidelines</h2>
                            <p class="text-muted lh-lg">
                                Our national Verified Contractor Directory is designed to establish customer trust. Listing entities must follow high standards of professionalism:
                            </p>
                            <ul class="text-muted list-unstyled ps-0 mb-4 d-grid gap-2">
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-patch-check text-brand me-2 mt-1"></i>
                                    <span>Directory listings must only advertise services, licenses, and insurance policies that the contractor currently maintains.</span>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-patch-check text-brand me-2 mt-1"></i>
                                    <span>PWOA reserves the right to suspend or remove any directory entry that generates persistent consumer complaints, violates EPA wastewater regulations, or engages in fraudulent operations.</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Section 4 -->
                        <div class="terms-section mb-5" id="education">
                            <h2 class="h4 fw-bold text-dark"><i class="bi bi-mortarboard-fill text-brand me-2"></i> 4. Education & Certification</h2>
                            <p class="text-muted lh-lg">
                                All course guides, exam engines, downloadable SOP files, and regulatory templates remain the intellectual property of PWOA.
                            </p>
                            <ul class="text-muted list-styled ps-4 mb-0">
                                <li class="mb-2">Certificates are granted solely upon successful, honest completion of training modules and exams.</li>
                                <li class="mb-2">Sharing course access keys, duplicating intellectual properties, or fabricating credential scores will lead to permanent membership termination.</li>
                            </ul>
                        </div>

                        <!-- Section 5 -->
                        <div class="terms-section mb-5" id="acceptable-use">
                            <h2 class="h4 fw-bold text-dark"><i class="bi bi-shield-slash-fill text-brand me-2"></i> 5. Acceptable Platform Use</h2>
                            <p class="text-muted lh-lg">
                                Members are strictly prohibited from exploiting PWOA platform infrastructure to:
                            </p>
                            <ul class="text-muted list-unstyled ps-0 mb-4 d-grid gap-2">
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-x-circle text-danger me-2 mt-1"></i>
                                    <span>Introduce malicious software, scrapers, or launch denial-of-service processes.</span>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-x-circle text-danger me-2 mt-1"></i>
                                    <span>Post misleading claims, defamatory business reviews, or mock directory entries.</span>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-x-circle text-danger me-2 mt-1"></i>
                                    <span>Attempt reverse-engineering of ledger token verification mechanics.</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Section 6 -->
                        <div class="terms-section mb-5" id="payment-tokens">
                            <h2 class="h4 fw-bold text-dark"><i class="bi bi-coin text-brand me-2"></i> 6. Tokens & Financial Operations</h2>
                            <p class="text-muted lh-lg">
                                The platform includes tokens (such as WASH) to handle exam enrollments and dynamic platform micro-transactions.
                            </p>
                            <ul class="text-muted list-styled ps-4 mb-0">
                                <li class="mb-2"><strong>Purchases:</strong> Payment calculations are powered by Stripe. You agree to pay the standard listed rate for all subscriptions or packages.</li>
                                <li class="mb-2"><strong>Ledger Interaction:</strong> Tokens are tracked in part by cryptographic distributed structures. PWOA is not liable for fluctuations in decentralized gas fees, ledger connectivity, or external token transfers initiated by third-party integrations.</li>
                            </ul>
                        </div>

                        <!-- Section 7 -->
                        <div class="terms-section mb-5" id="liability">
                            <h2 class="h4 fw-bold text-dark"><i class="bi bi-exclamation-triangle-fill text-brand me-2"></i> 7. Limitation of Liability</h2>
                            <p class="text-muted lh-lg text-uppercase fw-semibold small bg-light p-3 rounded-3 border">
                                PWOA delivers resources "as-is" and "as-available." We express no warranties or legal assurances regarding the completeness of environmental guides or local runoff laws. To the maximum scope permitted by law, Pressure Washers of America is not liable for direct, incidental, or property damage claims resulting from a contractor's physical operations, field compliance failures, or application downtime.
                            </p>
                        </div>

                        <!-- Section 8 -->
                        <div class="terms-section mb-5" id="governing-law">
                            <h2 class="h4 fw-bold text-dark"><i class="bi bi-bank text-brand me-2"></i> 8. Governing Law</h2>
                            <p class="text-muted lh-lg">
                                These Terms and Conditions shall be governed by, and evaluated under, the laws of the United States. Any formal legal disputes arising from these services must be resolved exclusively within standard federal or state courts.
                            </p>
                        </div>

                        <!-- Section 9 -->
                        <div class="terms-section" id="support">
                            <h2 class="h4 fw-bold text-dark"><i class="bi bi-envelope-at-fill text-brand me-2"></i> 9. Legal Contact</h2>
                            <p class="text-muted lh-lg mb-4">
                                For inquiries about PWOA regulations, corporate guidelines, or intellectual property rights, please contact our administrative board:
                            </p>
                            <div class="p-4 rounded-4 bg-light border border-light">
                                <p class="mb-2"><i class="bi bi-envelope text-brand me-2"></i> <strong>Email:</strong> <a href="mailto:info@pwoa.org" class="text-decoration-none text-muted">info@pwoa.org</a></p>
                                <p class="mb-0"><i class="bi bi-telephone text-brand me-2"></i> <strong>Phone:</strong> <a href="tel:+18669207962" class="text-decoration-none text-muted">(866) 920-PWOA</a></p>
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
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize animations
            AOS.init({
                once: true,
                offset: 50,
                duration: 800,
            });

            // Smooth Scroll Active Links Helper
            const links = document.querySelectorAll('.terms-nav-link');
            const sections = document.querySelectorAll('.terms-section');

            function changeActiveLink() {
                let index = sections.length;

                while(--index && window.scrollY + 150 < sections[index].offsetTop) {}
                
                links.forEach((link) => link.classList.remove('active'));
                if(links[index]) {
                    links[index].classList.add('active');
                }
            }

            changeActiveLink();
            window.addEventListener('scroll', changeActiveLink);
        });
    </script>
@endpush
