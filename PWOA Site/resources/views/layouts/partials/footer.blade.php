<footer class="footer-premium text-white pt-5 mt-auto" style="position: relative; z-index: 1030;">
    <div class="container pb-5">
        <div class="row g-5">
            <!-- Brand Column -->
            <div class="col-lg-4 pe-lg-5">
                <a href="{{ route('home') }}"
                    class="d-inline-flex align-items-center mb-3 text-white text-decoration-none">
                    <div style="height: 40px;">
                        <!-- <i class="bi bi-droplet-fill fs-5"></i> -->
                    </div>
                    <span class="fs-4 fw-bold ls-wide">Pressure Washers Of America (PWOA)</span>
                </a>
                <p class="text-white-50 mb-4 small lh-lg">
                    Pressure Washers Of America is the national organization setting the standard for pressure washing professionals through certification, education, and industry accountability. We provide contractors with the tools, training, and network needed to grow, operate professionally, and stay ahead in a rapidly evolving industry.
                </p>
                <div class="d-flex gap-2">
                    <a href="#"
                        class="btn btn-outline-secondary border-0 bg-white bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center p-0 footer-social-icon"><i
                            class="bi bi-facebook"></i></a>
                    <a href="#"
                        class="btn btn-outline-secondary border-0 bg-white bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center p-0 footer-social-icon"><i
                            class="bi bi-twitter-x"></i></a>
                    <a href="#"
                        class="btn btn-outline-secondary border-0 bg-white bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center p-0 footer-social-icon"><i
                            class="bi bi-linkedin"></i></a>
                    <a href="#"
                        class="btn btn-outline-secondary border-0 bg-white bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center p-0 footer-social-icon"><i
                            class="bi bi-youtube"></i></a>
                </div>
            </div>

            <!-- Links Column 1 -->
            <div class="col-6 col-lg-2">
                <h6 class="text-uppercase text-white fw-bold mb-4 ls-wide">Association</h6>
                <ul class="list-unstyled d-grid gap-3 small">
                    <li><a class="text-white-50 text-decoration-none footer-hover" href="{{ route('about') }}">About
                            Us</a></li>
                    <li><a class="text-white-50 text-decoration-none footer-hover"
                            href="{{ route('membership.index') }}">Membership</a></li>
                    <li><a class="text-white-50 text-decoration-none footer-hover"
                            href="{{ route('events.index') }}">Events & Summits</a></li>
                    <!-- <li><a class="text-white-50 text-decoration-none footer-hover" href="#">Leadership</a></li> -->
                </ul>
            </div>

            <!-- Links Column 2 -->
            <div class="col-6 col-lg-2">
                <h6 class="text-uppercase text-white fw-bold mb-4 ls-wide">Resources</h6>
                <ul class="list-unstyled d-grid gap-3 small">
                    <li><a class="text-white-50 text-decoration-none footer-hover"
                            href="{{ route('contractors.index') }}">Find a Contractor</a></li>
                    <li><a class="text-white-50 text-decoration-none footer-hover"
                            href="{{ route('vendors.index') }}">Vendor Network</a></li>
                    <li><a class="text-white-50 text-decoration-none footer-hover"
                            href="{{ route('education.index') }}">Education Center</a></li>
                    <li><a class="text-white-50 text-decoration-none footer-hover"
                            href="{{ route('compliance.index') }}">Industry Compliance</a></li>
                </ul>
            </div>

            <!-- Newsletter / Contact -->
            <div class="col-lg-4">
                <h6 class="text-uppercase text-white fw-bold mb-4 ls-wide">Stay Updated</h6>
                <p class="small text-white-50 mb-3">Get the latest PWOA news, upcoming events, certification updates, and industry insights delivered directly to your inbox. </p>
                <form class="mb-4">
                    <div class="input-group">
                        <input type="email" class="form-control bg-dark border-secondary text-white"
                            placeholder="Email address" aria-label="Email address" required>
                        <button class="btn btn-brand fw-semibold" type="button">Subscribe</button>
                    </div>
                </form>
               <div class="small text-white-50">
    <p class="mb-1">
        <i class="bi bi-envelope me-2 text-brand"></i>
        <a href="mailto:info@pwoa.org" class="text-white-50 text-decoration-none">
            info@pwoa.org
        </a>
    </p>

    <p class="mb-0">
        <i class="bi bi-telephone me-2 text-brand"></i>
        <a href="tel:+18669207962" class="text-white-50 text-decoration-none">
            (866) 920-PWOA
        </a>
    </p>
</div>
            </div>
        </div>
    </div>

    <!-- Bottom Bar -->
    <div class="border-top border-secondary border-opacity-25 py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="small text-white-50 mb-0">&copy; {{ date('Y') }} Pressure Washers of America. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <ul class="list-inline small mb-0">
                        <li class="list-inline-item me-3"><a href="{{ route('privacy-policy') }}"
                                class="text-white-50 text-decoration-none footer-hover">Privacy Policy</a></li>
                        <li class="list-inline-item me-3"><a href="{{ route('terms-and-conditions') }}"
                                class="text-white-50 text-decoration-none footer-hover">Terms & Conditions</a></li>
                        <li class="list-inline-item"><a href="{{ route('contact') }}"
                                class="text-white-50 text-decoration-none footer-hover">Contact Support</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
