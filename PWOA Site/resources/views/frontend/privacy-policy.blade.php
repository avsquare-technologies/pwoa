@extends('layouts.front')

@section('title', 'Privacy Policy')
@section('meta_description', 'Privacy Policy for Pressure Washers of America (PWOA). Learn how we collect, use, protect, and handle your personal and business data.')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        .policy-card {
            background: #fff;
            border-radius: 1.5rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }
        .policy-nav-link {
            transition: all 0.3s ease;
            font-weight: 500;
            display: block;
            padding: 0.5rem 0;
            border-left: 2px solid transparent;
            padding-left: 1rem;
        }
        .policy-nav-link:hover, .policy-nav-link.active {
            color: var(--ag-primary) !important;
            border-left-color: var(--ag-primary);
            font-weight: 600;
            text-decoration: none;
        }
        .policy-section {
            scroll-margin-top: 100px;
        }
        .policy-section h2 {
            position: relative;
            padding-bottom: 0.75rem;
            margin-bottom: 1.25rem;
        }
        .policy-section h2::after {
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
                    <h1 class="display-3 fw-bold mb-4 text-white text-shadow">Privacy Policy</h1>
                    <p class="lead text-white-50 px-md-5 mx-auto mb-3" style="max-width: 800px;" data-aos="fade-up" data-aos-delay="400">
                        Protecting your personal and professional data is central to our mission at Pressure Washers of America.
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
                                <li><a href="#info-collect" class="text-secondary policy-nav-link">1. Information We Collect</a></li>
                                <li><a href="#how-use" class="text-secondary policy-nav-link">2. How We Use Information</a></li>
                                <li><a href="#data-sharing" class="text-secondary policy-nav-link">3. Information Sharing</a></li>
                                <li><a href="#security" class="text-secondary policy-nav-link">4. Data Security & Storage</a></li>
                                <li><a href="#user-rights" class="text-secondary policy-nav-link">5. Your Privacy Rights</a></li>
                                <li><a href="#cookies" class="text-secondary policy-nav-link">6. Cookies & Tracking</a></li>
                                <li><a href="#blockchain" class="text-secondary policy-nav-link">7. Blockchain Governance</a></li>
                                <li><a href="#changes" class="text-secondary policy-nav-link">8. Policy Updates</a></li>
                                <li><a href="#contact" class="text-secondary policy-nav-link">9. Contact Us</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Content Area (Right) -->
                <div class="col-lg-8" data-aos="fade-up" data-aos-delay="200">
                    <div class="card policy-card border-0 p-4 p-md-5">
                        
                        <!-- Section 1 -->
                        <div class="policy-section mb-5" id="info-collect">
                            <h2 class="h4 fw-bold text-dark"><i class="bi bi-shield-lock text-brand me-2"></i> 1. Information We Collect</h2>
                            <p class="text-muted lh-lg">
                                Pressure Washers of America (PWOA) collects various types of information to provide high-quality services, verify contractor credentials, and operate our member ecosystem:
                            </p>
                            <ul class="text-muted list-unstyled ps-0 mb-4 d-grid gap-2">
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-check-circle text-brand me-2 mt-1"></i>
                                    <span><strong>Personal Contact Details:</strong> Name, professional title, primary email, physical mailing address, and telephone numbers.</span>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-check-circle text-brand me-2 mt-1"></i>
                                    <span><strong>Business Profiles:</strong> Company name, service areas, professional certifications, regulatory compliance files, and insurance declarations.</span>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-check-circle text-brand me-2 mt-1"></i>
                                    <span><strong>Financial & Transactional Logs:</strong> Payment token operations, Stripe billing subscriptions, membership tier levels, and token balances.</span>
                                </li>
                            </ul>
                            <p class="text-muted lh-lg">
                                We gather this data directly through forms you submit, registration flows, membership sign-ups, and automated interactions with our web portal.
                            </p>
                        </div>

                        <!-- Section 2 -->
                        <div class="policy-section mb-5" id="how-use">
                            <h2 class="h4 fw-bold text-dark"><i class="bi bi-gear-fill text-brand me-2"></i> 2. How We Use Information</h2>
                            <p class="text-muted lh-lg">
                                PWOA processes your information to fulfill our mission of establishing high standards of accountability in the pressure washing industry:
                            </p>
                            <ul class="text-muted list-unstyled ps-0 mb-4 d-grid gap-2">
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-arrow-right-short text-brand me-1"></i>
                                    <span>To manage your membership, grant training credentials, and generate professional certification badges.</span>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-arrow-right-short text-brand me-1"></i>
                                    <span>To publish, maintain, and moderate our national Verified Contractor Directory.</span>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-arrow-right-short text-brand me-1"></i>
                                    <span>To process purchases, tokens, and distribute education certificates seamlessly.</span>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-arrow-right-short text-brand me-1"></i>
                                    <span>To send administrative notices, industry compliance updates, and standard newsletters (with opt-out preferences).</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Section 3 -->
                        <div class="policy-section mb-5" id="data-sharing">
                            <h2 class="h4 fw-bold text-dark"><i class="bi bi-share-fill text-brand me-2"></i> 3. Information Sharing</h2>
                            <p class="text-muted lh-lg">
                                We respect your privacy. PWOA does not sell, lease, or distribute your private contact details to commercial brokers. Your data is shared strictly under the following scopes:
                            </p>
                            <ul class="text-muted list-styled ps-4 mb-4">
                                <li class="mb-2"><strong>Public Directory:</strong> Business profiles, certificates, and compliance ratings are visible to the public to help them find certified operators.</li>
                                <li class="mb-2"><strong>Service Providers:</strong> Standard third-party partners (e.g., Stripe, transactional email engines) access minimal information required to secure services.</li>
                                <li class="mb-2"><strong>Regulatory Compliance:</strong> We may share necessary records with environmental protection agencies or local municipal boards only if legally mandated to establish standard compliance.</li>
                            </ul>
                        </div>

                        <!-- Section 4 -->
                        <div class="policy-section mb-5" id="security">
                            <h2 class="h4 fw-bold text-dark"><i class="bi bi-shield-fill-check text-brand me-2"></i> 4. Data Security & Storage</h2>
                            <p class="text-muted lh-lg">
                                We utilize secure database models, transport layer security (HTTPS/TLS 1.3), hashed passcodes, and tokenized credentials to guard against breaches. However, no transmission system or automated cloud database is entirely immune. PWOA guarantees standard enterprise protections, but encourages members to use robust passphrases and safeguard their credentials.
                            </p>
                        </div>

                        <!-- Section 5 -->
                        <div class="policy-section mb-5" id="user-rights">
                            <h2 class="h4 fw-bold text-dark"><i class="bi bi-person-bounding-box text-brand me-2"></i> 5. Your Privacy Rights</h2>
                            <p class="text-muted lh-lg">
                                Members hold full agency over their profile data. You have the right to request access to the personal data we hold, modify billing profiles, withdraw directory visibility, or request account deletions. Account closures can be initiated by contacting our dedicated support desk.
                            </p>
                        </div>

                        <!-- Section 6 -->
                        <div class="policy-section mb-5" id="cookies">
                            <h2 class="h4 fw-bold text-dark"><i class="bi bi-cookie text-brand me-2"></i> 6. Cookies & Tracking</h2>
                            <p class="text-muted lh-lg">
                                We employ session-based cookies and analytics libraries to deliver personalized features, authenticate credentials, and evaluate site metrics. You can manage cookie controls inside your local browser settings, but disabling essential cookies might affect certain premium dynamic services on our dashboard.
                            </p>
                        </div>

                        <!-- Section 7 -->
                        <div class="policy-section mb-5" id="blockchain">
                            <h2 class="h4 fw-bold text-dark"><i class="bi bi-cpu text-brand me-2"></i> 7. Blockchain Governance</h2>
                            <p class="text-muted lh-lg">
                                As part of PWOA’s commitment to innovative infrastructure, certain verification states, tokens, and compliance metrics utilize cryptographic blockchain ledgers (such as XRPL). Please note that transaction states committed to public, decentralized ledgers are immutable. No personal identification information is written to decentralized blocks; only verified cryptographic hashes and token allocations are stored on the ledger.
                            </p>
                        </div>

                        <!-- Section 8 -->
                        <div class="policy-section mb-5" id="changes">
                            <h2 class="h4 fw-bold text-dark"><i class="bi bi-clock-history text-brand me-2"></i> 8. Policy Updates</h2>
                            <p class="text-muted lh-lg">
                                PWOA reserves the right to adapt this privacy policy in alignment with standard legal directives or platform updates. When modifications are committed, we will adjust the date header above. We recommend checking back regularly to stay updated.
                            </p>
                        </div>

                        <!-- Section 9 -->
                        <div class="policy-section text-secondary-container" id="contact">
                            <h2 class="h4 fw-bold text-dark"><i class="bi bi-envelope-at text-brand me-2"></i> 9. Contact Us</h2>
                            <p class="text-muted lh-lg mb-4">
                                If you have questions regarding this Privacy Policy, your records, or compliance systems, please reach out to us:
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
            const links = document.querySelectorAll('.policy-nav-link');
            const sections = document.querySelectorAll('.policy-section');

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
