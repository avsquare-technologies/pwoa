<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('certificates.index') }}" class="btn btn-outline-secondary rounded-circle p-2">
                    <i class="bi bi-chevron-left"></i>
                </a>
                <h2 class="h4 mb-0 text-dark fw-bold">Official Credential</h2>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($certificate->nft_status === 'minted' && $certificate->nft_token_id)
                    <a href="{{ str_replace('/account', '', rtrim(config('services.xrpl.explorer_url', 'https://testnet.xrpl.org'), '/')) }}/nft/{{ $certificate->nft_token_id }}" target="_blank"
                        class="btn btn-outline-info rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-box-arrow-up-right me-2"></i> View on XRPL
                    </a>
                @endif
                <button id="download-btn" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-download me-2"></i> Download as Image
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Certificate Container -->

    @if($certificate->nft_status === 'pending')
        <div id="minting-overlay"
            class="position-fixed top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center bg-white bg-opacity-75"
            style="z-index: 1050; backdrop-filter: blur(5px);">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <!-- <h3 class="mt-4 fw-bold text-dark">Minting your Official NFT Credential...</h3> -->
           <h3 class="mt-4 fw-bold text-dark">Please wait while we are processing your certificate...</h3>
            <p class="text-muted">Please do not close this page. This will only take a moment.</p>
        </div>
    @endif

    <div class="py-5 bg-light">
        <div id="certificate-canvas" class="certificate-canvas shadow-2xl mx-auto position-relative">
            <!-- Background Pattern Layer -->
            <div class="cert-watermark"></div>

            <div class="cert-outer-border">
                
                <div class="cert-middle-border">
                    <div class="cert-inner-border">
                        <!-- Ornate Corners -->
                        <div class="corner-ornament top-left"></div>
                        <div class="corner-ornament top-right"></div>
                        <div class="corner-ornament bottom-left"></div>
                        <div class="corner-ornament bottom-right"></div>

                        <!-- Certificate Content -->
                        <div class="cert-content text-center">
                            <div class="cert-header mb-2">
                                <h4 class="cert-platform-name text-uppercase ls-extra-wide mb-1">Pressure Washers of
                                    America</h4>
                                <div class="cert-divider mx-auto mb-2"></div>
                                <h1 class="cert-main-title mb-0">Certificate of Completion</h1>
                            </div>

                            <div class="cert-primary-content mb-2">
                                <p class="cert-declaration mb-3">This hereby certifies that</p>
                                <h2 class="cert-user-name mb-3">{{ auth()->user()->name }}</h2>
                                <p class="cert-declaration mb-3">has demonstrated exceptional proficiency and
                                    successfully completed</p>
                                <h3 class="cert-course-title mb-4">{{ $certificate->course->title }}</h3>
                                <p class="cert-score-text mb-0 italic">Attaining a final evaluation score of <span
                                        class="fw-bold">{{ $certificate->score }}%</span></p>
                            </div>

                            <div class="cert-footer mt-0">
                                <div class="row align-items-end gx-5">
                                    <div class="col-4">
                                        <div class="cert-signature-block">
                                            <p class="cert-signature-font mb-0">Authorized Officer</p>
                                            <div class="cert-signature-line mt-2 mb-2"></div>
                                            <p class="cert-signature-label">Director of Education</p>
                                        </div>
                                    </div>
                                    <div class="col-4 text-center position-relative">
                                        <!-- Wax Seal & Ribbons -->
                                        <div class="cert-seal-ribbons">
                                            <div class="ribbon-red left"></div>
                                            <div class="ribbon-red right"></div>
                                        </div>
                                        <div class="cert-wax-seal mx-auto shadow-sm">
                                            <div class="wax-seal-inner">
                                                <i class="bi bi-patch-check-fill"></i>
                                            </div>
                                        </div>
                                        <p class="cert-seal-text mt-3 mb-0 uppercase ls-extra-wide">Verified Platform
                                            Credential</p>
                                    </div>
                                    <div class="col-4">
                                        <div class="cert-signature-block">
                                            <p class="cert-signature-font mb-0">Board Registry</p>
                                            <div class="cert-signature-line mt-2 mb-2"></div>
                                            <p class="cert-signature-label">Registry Officer</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="cert-metadata mt-4 text-start">
                                <div class="d-flex justify-content-between align-items-center opacity-75">
                                    <div class="small cert-meta-item">
                                        <span class="fw-bold">CERTIFICATE ID:</span>
                                        {{ $certificate->certificate_number }}
                                    </div>
                                    <div class="small cert-meta-item text-end">
                                        <span class="fw-bold">VALIDATION DATE:</span>
                                        {{ $certificate->issued_at->format('M d, Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts for Export -->
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <script>
            document.getElementById('download-btn').addEventListener('click', function () {
                const btn = this;
                const canvasContainer = document.getElementById('certificate-canvas');

                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Generating Image...';

                html2canvas(canvasContainer, {
                    scale: 4, // Ultra-High Resolution for printing
                    useCORS: true,
                    backgroundColor: '#ffffff',
                    logging: false,
                    windowWidth: 1000,
                    windowHeight: 700
                }).then(canvas => {
                    const link = document.createElement('a');
                    link.download = `PWOA-Certificate-${'{{ $certificate->certificate_number }}'}.png`;
                    link.href = canvas.toDataURL('image/png', 1.0);
                    link.click();

                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-download me-2"></i> Download as Image';
                }).catch(err => {
                    console.error('Export failed:', err);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i> Error';
                });
            });

            @if($certificate->nft_status === 'pending')
                window.addEventListener('load', function () {
                    setTimeout(() => {
                        const canvasContainer = document.getElementById('certificate-canvas');
                        html2canvas(canvasContainer, {
                            scale: 2, // 2x scale is sufficient for Pinata/NFT to balance quality and size
                            useCORS: true,
                            backgroundColor: '#ffffff',
                            logging: false,
                            windowWidth: 1000,
                            windowHeight: 700
                        }).then(canvas => {
                            const imageData = canvas.toDataURL('image/png', 1.0);

                            fetch('{{ route("certificates.mint", $certificate->id) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ image: imageData })
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        window.location.reload();
                                    } else {
                                        alert('Minting failed: ' + data.message);
                                        document.getElementById('minting-overlay').style.display = 'none';
                                    }
                                })
                                .catch(err => {
                                    console.error('Minting error:', err);
                                    alert('An error occurred during minting. Please refresh and try again.');
                                    document.getElementById('minting-overlay').style.display = 'none';
                                });
                        }).catch(err => {
                            console.error('html2canvas error:', err);
                            alert('Could not render certificate for minting.');
                            document.getElementById('minting-overlay').style.display = 'none';
                        });
                    }, 1000); // Give fonts a second to load
                });
            @endif
        </script>
    @endpush

</x-app-layout>