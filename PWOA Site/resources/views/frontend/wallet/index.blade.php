<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0 text-dark fw-bold">
            {{ __('My Wallet') }}
        </h2>
    </x-slot>

    <div class="wallet-page container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 p-3 d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-3 d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Wallet Hero Card -->
                <div class="card wallet-card mb-5">
                    <div class="row align-items-center position-relative z-1">
                        <div class="col-md-7">
                            <p class="mb-1 opacity-75 text-uppercase small fw-bold tracking-wider">Available Balance</p>
                            <h1 class="display-3 fw-bold mb-4">
                                {{ number_format($balance, 2) }} <span class="token-symbol">$WASH</span>
                            </h1>
                            <div class="d-flex gap-3">
                                <a href="{{ route('token.purchase') }}" class="btn btn-wallet-action btn-wallet-primary shadow-sm text-decoration-none">
                                    <i class="bi bi-plus-lg me-2"></i> Add Funds
                                </a>
                                <button class="btn btn-wallet-action btn-wallet-outline" id="btnOpenSend">
                                    <i class="bi bi-send me-2"></i> Send Tokens
                                </button>
                            </div>
                        </div>
                        <div class="col-md-5 mt-4 mt-md-0 text-md-end">
                            <p class="mb-2 opacity-75 small fw-bold">Your XRPL Address</p>
                            <div class="d-inline-block address-badge mb-2">
                                {{ $wallet->address }}
                            </div>
                            <div class="mt-2 d-flex flex-wrap justify-content-md-end gap-3 justify-content-center">
                                <button id="copyBtn" class="btn btn-link text-white text-decoration-none p-0 small opacity-75 hover-opacity-100" onclick="copyToClipboard('{{ $wallet->address }}')">
                                    <i class="bi bi-copy me-1" id="copyIcon"></i> <span id="copyText">Copy Address</span>
                                </button>
                                <span class="text-white-50 small">|</span>
                                <a href="{{ config('services.xrpl.explorer_url') }}/{{ $wallet->address }}" target="_blank" rel="noopener noreferrer" class="btn btn-link text-white text-decoration-none p-0 small opacity-75 hover-opacity-100">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> View on XRPL Explorer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="card history-card overflow-hidden">
                    <div class="card-header bg-white py-4 px-4 border-0 d-flex justify-content-between align-items-center border-bottom">
                        <h5 class="fw-bold mb-0">Transaction History</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-light btn-sm rounded-3 px-3"><i class="bi bi-filter me-1"></i> Filter</button>
                            <button class="btn btn-light btn-sm rounded-3 px-3" onclick="location.reload()"><i class="bi bi-arrow-clockwise me-1"></i> Refresh</button>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Transaction Details</th>
                                    <th class="text-end">Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $tx)
                                    @php
                                        $tx = (object) $tx;
                                        $isCredit = ($tx->type ?? '') === 'credit';
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box me-3 {{ $isCredit ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                                    <i class="bi {{ $isCredit ? 'bi-arrow-down-left' : 'bi-arrow-up-right' }} fs-5"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $tx->description ?? ($isCredit ? 'Received Tokens' : 'Sent Tokens') }}</div>
                                                    <div class="text-muted small font-monospace">
                                                        <a href="{{ str_replace('/account', '', config('services.xrpl.explorer_url')) }}/{{ $tx->hash }}" target="_blank" class="text-decoration-none text-muted">
                                                            {{ Str::limit($tx->hash ?? 'XRPL Transaction', 20) }}
                                                            <i class="bi bi-box-arrow-up-right ms-1"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="fw-bold {{ $isCredit ? 'text-success' : 'text-danger' }}">
                                                {{ $isCredit ? '+' : '-' }}{{ number_format($tx->amount ?? 0, 2) }}
                                            </div>
                                            <div class="text-muted small text-uppercase">$WASH</div>
                                        </td>
                                        <td>
                                            @php
                                                $status = $tx->status ?? 'completed';
                                                $statusClass = match($status) {
                                                    'completed' => 'bg-success-subtle text-success',
                                                    'pending' => 'bg-warning-subtle text-warning',
                                                    'failed' => 'bg-danger-subtle text-danger',
                                                    default => 'bg-secondary-subtle text-secondary'
                                                };
                                            @endphp
                                            <span class="status-badge {{ $statusClass }}">
                                                {{ $status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-dark small">{{ isset($tx->timestamp) ? \Carbon\Carbon::parse($tx->timestamp)->format('M d, Y') : 'N/A' }}</div>
                                            <div class="text-muted extra-small">{{ isset($tx->timestamp) ? \Carbon\Carbon::parse($tx->timestamp)->format('H:i') : '' }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted italic">
                                            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                            No transactions found yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
    <!-- SEND CRYPTO MODAL -->
    <div class="modal fade" id="sendCryptoModal" tabindex="-1" aria-labelledby="sendCryptoModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="sendCryptoModalLabel">
                        <i class="bi bi-send-fill text-primary me-2"></i>Send $WASH Tokens
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Xaman Step: Sign-In / Trustline -->
                    <div id="xamanStep" class="text-center py-4">
                        <div id="xamanStatusText" class="h6 fw-bold text-dark mb-2">Initializing Xaman...</div>
                        <div id="xamanDescription" class="small text-muted mb-4">Connecting to XRPL secure gateway...</div>

                        <div class="d-flex justify-content-center align-items-center" style="min-height: 250px;">
                            <!-- Loader -->
                            <div id="xamanQrLoader">
                                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>

                            <!-- QR Container -->
                            <div id="xamanQrContainer" style="display:none;">
                                <img id="xamanQrImage" src="" alt="Xaman QR" class="img-fluid mb-3">
                                <div class="animate-pulse">
                                    <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2 border border-primary-subtle">
                                        <i class="bi bi-phone me-2"></i> Scan with Xaman App
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Form (Unlocked after Xaman verify) -->
                    <div id="transferForm" style="display:none;">
                        <div class="alert alert-success border-0 rounded-4 small text-center mb-4 py-2">
                            <i class="bi bi-shield-check me-1"></i> Identity Verified via Xaman
                        </div>

                        <div class="mb-4 p-3 bg-light rounded-4 text-center">
                            <small class="text-muted d-block text-uppercase fw-bold ls-wide" style="font-size: 0.65rem;">Available Balance</small>
                            <h4 class="fw-bold text-dark mb-0">
                                <span id="modalBalance">{{ number_format($balance, 2) }}</span> <span class="text-primary">$WASH</span>
                            </h4>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Recipient Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-wallet2 text-muted"></i></span>
                                <input type="text" id="recipientAddress" class="form-control border-start-0 ps-0" placeholder="r..." style="border-radius: 0 12px 12px 0;">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Amount to Send</label>
                            <div class="input-group">
                                <input type="number" id="sendAmount" class="form-control form-control-lg fw-bold" placeholder="0.00" step="any" style="border-radius: 12px 0 0 12px;">
                                <span class="input-group-text bg-light fw-bold" style="border-radius: 0 12px 12px 0;">$WASH</span>
                            </div>
                            <div class="mt-2 text-end">
                                <small class="text-muted">≈ <strong id="sendUsd" class="text-dark">0.00</strong> USD</small>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary btn-lg w-100 fw-bold py-3 shadow-sm" id="btnConfirmTransfer" style="border-radius: 16px;">
                            Confirm & Send Tokens
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const WASH_TO_USD = {{ config('services.xrpl.wash_to_usd', 0.05) }};
        const currentBalance = {{ $balance }};
        let xamanPollInterval = null;
        let authenticatedAccount = null;

        $(document).ready(function() {
            // Reset modal on close
            $('#sendCryptoModal').on('hidden.bs.modal', function () {
                resetXamanModal();
            });

            // Open Modal
            $('#btnOpenSend').click(function() {
                const modal = new bootstrap.Modal(document.getElementById('sendCryptoModal'));
                modal.show();
                initXamanLogin();
            });

            function resetXamanModal() {
                if (xamanPollInterval) clearInterval(xamanPollInterval);
                xamanPollInterval = null;
                authenticatedAccount = null;
                
                updateXamanUI('LOADING', 'Initializing Xaman...', 'Connecting to XRPL secure gateway...');
                $('#xamanQrImage').attr('src', '');
                $('#recipientAddress').val('');
                $('#sendAmount').val('');
                $('#sendUsd').text('0.00');
                $('#transferForm').hide();
                $('#xamanStep').show();
            }

            function updateXamanUI(state, title, desc) {
                $('#xamanStatusText').text(title);
                $('#xamanDescription').text(desc);
                if (state === 'LOADING') {
                    $('#xamanQrLoader').show();
                    $('#xamanQrContainer').hide();
                } else {
                    $('#xamanQrLoader').hide();
                    $('#xamanQrContainer').fadeIn();
                }
            }

            function initXamanLogin() {
                $.post('{{ route('wallet.xaman.login') }}', { _token: '{{ csrf_token() }}' })
                    .done(function (res) {
                        $('#xamanQrImage').attr('src', res.qr_png);
                        updateXamanUI('QR', 'Secure Sign-In', 'Scan with Xaman App to verify your wallet ownership.');
                        startPolling(res.uuid, 'LOGIN');
                    })
                    .fail(function () {
                        Swal.fire('Error', 'Failed to initialize Xaman connection.', 'error');
                        $('#sendCryptoModal').modal('hide');
                    });
            }

            function startPolling(uuid, mode) {
                if (xamanPollInterval) clearInterval(xamanPollInterval);
                xamanPollInterval = setInterval(function () {
                    $.get('/wallet/xaman/status/' + uuid).done(function (res) {
                        if (res.signed === true) {
                            clearInterval(xamanPollInterval);
                            xamanPollInterval = null;
                            if (mode === 'LOGIN') {
                                checkLedgerAccess(res.account);
                            } else {
                                unlockTransferForm(authenticatedAccount);
                            }
                        }
                    });
                }, 3000);
            }

            function checkLedgerAccess(account) {
                authenticatedAccount = account;
                updateXamanUI('LOADING', 'Verifying Assets...', 'Checking trustline status for $WASH tokens.');

                $.post('{{ route('wallet.xaman.check-access') }}', {
                    _token: '{{ csrf_token() }}',
                    address: account
                }).done(function (res) {
                    if (res.needs_trustline) {
                        $('#xamanQrImage').attr('src', res.qr_png);
                        Swal.fire({
                            title: 'Trustline Required',
                            text: 'Your wallet needs to enable $WASH tokens. One more scan is required.',
                            icon: 'info',
                            confirmButtonText: 'Show Trustline QR'
                        }).then(() => {
                            updateXamanUI('QR', 'Enable $WASH', 'Scan to create a trustline for $WASH tokens.');
                            startPolling(res.uuid, 'TRUSTLINE');
                        });
                    } else {
                        unlockTransferForm(account);
                    }
                }).fail(function () {
                    Swal.fire('Error', 'Ledger verification failed.', 'error');
                    $('#sendCryptoModal').modal('hide');
                });
            }

            function unlockTransferForm(account) {
                $('#xamanStep').hide();
                $('#transferForm').fadeIn();
            }

            $('#sendAmount').on('input', function() {
                const amount = parseFloat($(this).val()) || 0;
                $('#sendUsd').text((amount * WASH_TO_USD).toFixed(2));
            });

            $('#btnConfirmTransfer').click(function() {
                const destination = $('#recipientAddress').val();
                const amount = parseFloat($('#sendAmount').val()) || 0;

                if (!destination || destination.length < 25) {
                    return Swal.fire('Invalid Address', 'Please enter a valid XRPL destination address.', 'warning');
                }

                if (amount <= 0) {
                    return Swal.fire('Invalid Amount', 'Please enter an amount greater than zero.', 'warning');
                }

                if (amount > currentBalance) {
                    return Swal.fire('Insufficient Balance', 'You do not have enough $WASH tokens.', 'error');
                }

                Swal.fire({
                    title: 'Authorize Transfer',
                    text: `Send ${amount} $WASH to ${destination.substring(0, 8)}...${destination.substring(destination.length - 4)}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Send Now',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.post('{{ route('wallet.transfer') }}', {
                            _token: '{{ csrf_token() }}',
                            destination: destination,
                            amount: amount
                        }).fail(xhr => {
                            Swal.showValidationMessage(xhr.responseJSON?.message || 'Transfer failed');
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Tokens have been submitted to the ledger.',
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    }
                });
            });
        });

        function copyToClipboard(text) {
            const copyText = document.getElementById('copyText');
            const copyIcon = document.getElementById('copyIcon');

            function updateUI() {
                copyText.innerText = 'Copied!';
                copyIcon.classList.replace('bi-copy', 'bi-check-lg');
                setTimeout(() => {
                    copyText.innerText = 'Copy Address';
                    copyIcon.classList.replace('bi-check-lg', 'bi-copy');
                }, 2000);
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(updateUI);
            } else {
                // Fallback for non-secure contexts
                const textArea = document.createElement("textarea");
                textArea.value = text;
                textArea.style.position = "fixed";
                textArea.style.left = "-999999px";
                textArea.style.top = "-999999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    updateUI();
                } catch (err) {
                    console.error('Fallback: Oops, unable to copy', err);
                }
                document.body.removeChild(textArea);
            }
        }
    </script>
    @endpush
</x-app-layout>
