<x-slot name="header">
    <h2 class="h4 mb-0 text-dark">
        <i class="bi bi-clock-history me-2 text-primary"></i> {{ __('Payment History') }}
    </h2>
</x-slot>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0">Transaction Records</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted small text-uppercase">Reference</th>
                            <th class="py-3 text-muted small text-uppercase">Amount</th>
                            <th class="py-3 text-muted small text-uppercase text-center">Status</th>
                            <th class="py-3 text-muted small text-uppercase">Date</th>
                            <th class="pe-4 py-3 text-end text-muted small text-uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($payments as $payment)
                            <tr>
                                <td class="ps-4 py-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <i class="bi bi-receipt"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $payment->description ?? 'Membership Subscription' }}</div>
                                            <div class="text-muted small">Inv: {{ $payment->stripe_invoice_id ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 fw-bold">
                                    {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="py-4 text-center">
                                    <span class="badge bg-{{ $payment->status === 'succeeded' ? 'success' : 'danger' }}-subtle text-{{ $payment->status === 'succeeded' ? 'success' : 'danger' }} rounded-pill px-3 py-2">
                                        <i class="bi bi-{{ $payment->status === 'succeeded' ? 'check-circle' : 'x-circle' }} me-1"></i>
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="py-4">
                                    <div class="text-dark">{{ $payment->paid_at->format('M d, Y') }}</div>
                                    <div class="text-muted small">{{ $payment->paid_at->format('h:i A') }}</div>
                                </td>
                                <td class="pe-4 py-4 text-end">
                                    @if($payment->receipt_url)
                                        <a href="{{ $payment->receipt_url }}" target="_blank" class="btn btn-light btn-sm rounded-pill px-3">
                                            <i class="bi bi-download me-1"></i> Receipt
                                        </a>
                                    @elseif($payment->tx_hash)
                                        <a href="{{ str_replace('/account', '', config('services.xrpl.explorer_url', 'https://testnet.xrpl.org')) }}/{{ $payment->tx_hash }}" target="_blank" class="btn btn-light btn-sm rounded-pill px-3 text-primary border-primary border-opacity-25">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> View Tx
                                        </a>
                                    @else
                                        <span class="text-muted small">No link</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-5 text-center">
                                    <div class="text-muted opacity-50 mb-3">
                                        <i class="bi bi-inboxes display-4"></i>
                                    </div>
                                    <h6 class="fw-bold">No payments found</h6>
                                    <p class="text-muted small">Once you subscribe, your records will appear here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
                <div class="card-footer bg-white py-3 border-top border-light">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
