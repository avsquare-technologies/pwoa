@extends('layouts.front')

@section('title', 'Ticket QR Scanner - PWOA Admin')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white p-4 text-center">
                    <h4 class="fw-bold mb-1">Ticket Scanner</h4>
                    <p class="small mb-0 opacity-75">Scan attendee QR codes to verify entry</p>
                </div>
                <div class="card-body p-4 text-center">
                    
                    {{-- Camera Container --}}
                    <div id="reader-wrapper" class="position-relative bg-light rounded-4 overflow-hidden mb-4 border-dashed" style="min-height: 300px; border: 2px dashed #dee2e6;">
                        <div id="reader" style="width: 100%;"></div>
                        <div id="reader-placeholder" class="position-absolute top-50 start-50 translate-middle w-100">
                            <i class="bi bi-camera display-4 text-muted"></i>
                            <p class="text-muted mt-2">Waiting for camera permission...</p>
                        </div>
                    </div>

                    {{-- Scan Result Container --}}
                    <div id="result-container" class="d-none">
                        <div id="result-alert" class="alert p-4 rounded-4 shadow-sm border-0 mb-0">
                            <div class="d-flex align-items-center gap-3 text-start">
                                <div id="result-icon-bg" class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 60px; height: 60px;">
                                    <i id="result-icon" class="bi display-5"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <h5 id="result-title" class="fw-bold mb-1"></h5>
                                    <p id="result-message" class="mb-0 text-muted small"></p>
                                </div>
                            </div>
                            
                            <div id="attendee-details" class="mt-4 pt-3 border-top d-none">
                                <div class="row g-3 text-start">
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase tracking-wider">Pass Holder</label>
                                        <p id="attendee-name" class="fw-bold mb-0 text-dark"></p>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase tracking-wider">Event</label>
                                        <p id="attendee-event" class="fw-bold mb-0 text-dark text-truncate"></p>
                                    </div>
                                    <div class="col-12">
                                        <label class="text-muted small fw-bold text-uppercase tracking-wider">Check-in Time</label>
                                        <p id="attendee-time" class="fw-bold mb-0 text-dark"></p>
                                    </div>
                                </div>
                            </div>

                            <button onclick="resetScanner()" class="btn btn-dark w-100 mt-4 rounded-pill fw-bold">
                                <i class="bi bi-arrow-repeat me-2"></i> Scan Next Ticket
                            </button>
                        </div>
                    </div>

                    <div id="scanner-controls" class="mt-3">
                        <p class="text-muted small"><i class="bi bi-info-circle me-1"></i> Position the QR code within the frame</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #reader {
        border: none !important;
    }
    #reader__dashboard_section_csr button {
        background-color: var(--ag-primary) !important;
        border: none !important;
        color: white !important;
        padding: 10px 20px !important;
        border-radius: 50px !important;
        font-weight: bold !important;
        margin-bottom: 10px !important;
    }
    #reader video {
        border-radius: 1rem !important;
        object-fit: cover !important;
    }
    .tracking-wider {
        letter-spacing: 0.1em;
        font-size: 0.65rem;
    }
</style>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrCode;
    const readerWrapper = document.getElementById('reader-wrapper');
    const readerPlaceholder = document.getElementById('reader-placeholder');
    const resultContainer = document.getElementById('result-container');
    const resultAlert = document.getElementById('result-alert');
    const resultIconBg = document.getElementById('result-icon-bg');
    const resultIcon = document.getElementById('result-icon');
    const resultTitle = document.getElementById('result-title');
    const resultMessage = document.getElementById('result-message');
    const attendeeDetails = document.getElementById('attendee-details');
    
    document.addEventListener('DOMContentLoaded', function() {
        startScanner();
    });

    function startScanner() {
        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        html5QrCode.start(
            { facingMode: "environment" }, 
            config,
            onScanSuccess
        ).catch(err => {
            console.error("Scanner error:", err);
            readerPlaceholder.innerHTML = `<p class="text-danger p-3">Camera access denied or not found. Please ensure you are using HTTPS and have granted camera permissions.</p>`;
        });
    }

    function onScanSuccess(decodedText, decodedResult) {
        // Vibrate if supported
        if (navigator.vibrate) navigator.vibrate(100);
        
        // Stop scanner to prevent multiple pings
        html5QrCode.stop().then(() => {
            readerWrapper.classList.add('d-none');
            validateTicket(decodedText);
        });
    }

    function validateTicket(qrData) {
        let ticketData;
        try {
            ticketData = JSON.parse(qrData);
        } catch (e) {
            showError("Invalid QR Code Format", "The scanned code is not a valid PWOA ticket.");
            return;
        }

        if (!ticketData.ticket_id || !ticketData.token) {
            showError("Incomplete Ticket Data", "The QR code is missing required verification fields.");
            return;
        }

        // Show loading state
        resultContainer.classList.remove('d-none');
        resultAlert.className = 'alert p-4 rounded-4 shadow-sm border-0 mb-0 bg-light';
        resultTitle.innerText = "Verifying Ticket...";
        resultMessage.innerText = "Connecting to PWOA secure servers...";
        resultIconBg.className = 'rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 bg-primary-subtle text-primary';
        resultIcon.className = 'bi bi-shield-lock display-5';

        fetch("{{ route('api.validate-ticket') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                ticket_id: ticketData.ticket_id,
                token: ticketData.token
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data);
            } else {
                showError(data.message || "Validation Failed", "Check the details and try again.");
            }
        })
        .catch(err => {
            showError("Network Error", "Could not connect to the validation server.");
        });
    }

    function showSuccess(data) {
        resultAlert.className = 'alert p-4 rounded-4 shadow-sm border-0 mb-0 bg-success-subtle';
        resultIconBg.className = 'rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 bg-success text-white';
        resultIcon.className = 'bi bi-check-circle-fill display-5';
        resultTitle.className = 'fw-bold mb-1 text-success';
        resultTitle.innerText = data.message;
        resultMessage.innerText = "Ticket is valid for entry.";
        
        attendeeDetails.classList.remove('d-none');
        document.getElementById('attendee-name').innerText = data.attendee.name;
        document.getElementById('attendee-event').innerText = data.attendee.event;
        document.getElementById('attendee-time').innerText = data.attendee.time;
    }

    function showError(title, message) {
        resultContainer.classList.remove('d-none');
        resultAlert.className = 'alert p-4 rounded-4 shadow-sm border-0 mb-0 bg-danger-subtle';
        resultIconBg.className = 'rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 bg-danger text-white';
        resultIcon.className = 'bi bi-x-circle-fill display-5';
        resultTitle.className = 'fw-bold mb-1 text-danger';
        resultTitle.innerText = title;
        resultMessage.innerText = message;
        attendeeDetails.classList.add('d-none');
    }

    function resetScanner() {
        resultContainer.classList.add('d-none');
        readerWrapper.classList.remove('d-none');
        attendeeDetails.classList.add('d-none');
        startScanner();
    }
</script>
@endpush
