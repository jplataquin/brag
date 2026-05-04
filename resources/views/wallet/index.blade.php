@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <!-- Balance Overview -->
        <div class="col-md-4">
            <div class="neon-card text-center p-5 h-100" style="border-color: var(--neon-magenta); background: rgba(255, 0, 255, 0.05);">
                <div class="mb-3">
                    <i class="bi bi-gem display-1" style="color: var(--neon-magenta); text-shadow: 0 0 20px rgba(255, 0, 255, 0.5);"></i>
                </div>
                <h4 class="text-uppercase text-secondary tracking-wide mb-2" style="font-family: 'Orbitron', sans-serif;">Diamond Balance</h4>
                <h1 class="display-3 fw-bold text-white mb-0">{{ number_format($balance) }}</h1>
            </div>
        </div>

        <!-- Buy Diamonds Section -->
        <div class="col-md-8">
            <h5 class="section-header mb-4">
                <i class="bi bi-cart section-icon"></i> PURCHASE DIAMONDS
            </h5>
            
            @if(session('error'))
                <div class="alert alert-danger mb-4 border-danger bg-danger bg-opacity-10 text-white rounded-3 shadow-sm">
                    <i class="bi bi-exclamation-octagon-fill me-2"></i> {{ session('error') }}
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success mb-4 border-success bg-success bg-opacity-10 text-white rounded-3 shadow-sm">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                </div>
            @endif

            @if(isset($packages) && count($packages) > 0)
                <!-- Desktop View -->
                <div class="row g-3 d-none d-md-flex">
                    @foreach($packages as $package)
                        <div class="col-md-3">
                            <div class="neon-card text-center p-3 h-100" style="border-color: rgba(0, 240, 255, 0.3);">
                                <h6 style="color: #00f0ff; letter-spacing: 1px;">{{ $package['name'] }}</h6>
                                <div class="my-3">
                                    <i class="bi bi-gem" style="font-size: 2rem; color: #00f0ff; text-shadow: 0 0 10px rgba(0,240,255,0.5);"></i>
                                    <div class="fw-bold fs-4 mt-2">{{ $package['diamonds'] }} <small class="text-muted" style="font-size: 0.5em;">DIAMONDS</small></div>
                                    <div class="text-white mt-1">
                                        @if($package['promo_price'])
                                            <span class="text-decoration-line-through text-muted small">{{ $package['currency'] }} {{ number_format($package['price'], 0) }}</span> 
                                            <span class="text-warning fw-bold">{{ $package['currency'] }} {{ number_format($package['promo_price'], 0) }}</span>
                                        @else
                                            {{ $package['currency'] }} {{ number_format($package['price'], 0) }}
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="d-flex flex-column gap-2 mt-auto">
                                    <button type="button" class="btn btn-outline-neon w-100 buy-now-btn" 
                                            style="border-color: #39ff14; color: #39ff14;"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#paymentModal"
                                            data-id="{{ $package->id }}"
                                            data-name="{{ $package->name }}"
                                            data-diamonds="{{ $package->diamonds }}"
                                            data-price="{{ $package->currency }} {{ number_format($package->final_price, 2) }}"
                                            data-allow-hitpay="{{ $package->allow_hitpay ? 'true' : 'false' }}"
                                            data-allow-manual="{{ $package->allow_manual ? 'true' : 'false' }}">
                                        <i class="bi bi-cart-plus me-1"></i> Buy Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Mobile View (Carousel) -->
                <div class="d-block d-md-none position-relative px-4">
                    <div id="diamondsCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner pb-2">
                            @php $isFirst = true; @endphp
                            @foreach($packages as $index => $package)
                                <div class="carousel-item {{ $isFirst ? 'active' : '' }}">
                                    @php $isFirst = false; @endphp
                                    <div class="neon-card text-center p-3 h-100 mx-auto" style="border-color: rgba(0, 240, 255, 0.3); max-width: 250px;">
                                        <h6 style="color: #00f0ff; letter-spacing: 1px;">{{ $package['name'] }}</h6>
                                        <div class="my-3">
                                            <i class="bi bi-gem" style="font-size: 2rem; color: #00f0ff; text-shadow: 0 0 10px rgba(0,240,255,0.5);"></i>
                                            <div class="fw-bold fs-4 mt-2">{{ $package['diamonds'] }} <small class="text-muted" style="font-size: 0.5em;">DIAMONDS</small></div>
                                            <div class="text-white mt-1">
                                                @if($package['promo_price'])
                                                    <span class="text-decoration-line-through text-muted small">{{ $package['currency'] }} {{ number_format($package['price'], 0) }}</span> 
                                                    <span class="text-warning fw-bold">{{ $package['currency'] }} {{ number_format($package['promo_price'], 0) }}</span>
                                                @else
                                                    {{ $package['currency'] }} {{ number_format($package['price'], 0) }}
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex flex-column gap-2">
                                            <button type="button" class="btn btn-outline-neon w-100 buy-now-btn" 
                                                    style="border-color: #39ff14; color: #39ff14;"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#paymentModal"
                                                    data-id="{{ $package->id }}"
                                                    data-name="{{ $package->name }}"
                                                    data-diamonds="{{ $package->diamonds }}"
                                                    data-price="{{ $package->currency }} {{ number_format($package->final_price, 2) }}"
                                                    data-allow-hitpay="{{ $package->allow_hitpay ? 'true' : 'false' }}"
                                                    data-allow-manual="{{ $package->allow_manual ? 'true' : 'false' }}">
                                                <i class="bi bi-cart-plus me-1"></i> Buy Now
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#diamondsCarousel" data-bs-slide="prev" style="width: 10%; justify-content: flex-start; margin-left: -15px;">
                            <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: invert(1) sepia(1) saturate(5) hue-rotate(135deg);"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#diamondsCarousel" data-bs-slide="next" style="width: 10%; justify-content: flex-end; margin-right: -15px;">
                            <span class="carousel-control-next-icon" aria-hidden="true" style="filter: invert(1) sepia(1) saturate(5) hue-rotate(135deg);"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            @else
                <div class="text-center text-muted">
                    No diamond packages are currently available for purchase.
                </div>
            @endif
        </div>
    </div>

    <!-- My Ledger Table -->
    <div class="row mt-5">
        <div class="col-12">
            <h5 class="section-header mb-4">
                <i class="bi bi-clock-history section-icon"></i> MY LEDGER
            </h5>

            <div class="neon-card overflow-hidden mb-5">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0" style="background: transparent;">
                        <thead>
                            <tr>
                                <th class="py-3 px-4" style="border-bottom-color: rgba(255,255,255,0.1);">Date</th>
                                <th class="py-3 px-4" style="border-bottom-color: rgba(255,255,255,0.1);">Type</th>
                                <th class="py-3 px-4" style="border-bottom-color: rgba(255,255,255,0.1);">Remarks</th>
                                <th class="py-3 px-4 text-end" style="border-bottom-color: rgba(255,255,255,0.1);">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $txn)
                                <tr>
                                    <td class="py-3 px-4 text-white-50 small">{{ $txn->created_at->format('M d, Y H:i') }}</td>
                                    <td class="py-3 px-4">
                                        <span class="badge bg-{{ $txn->amount > 0 ? 'success' : 'danger' }} bg-opacity-10 text-{{ $txn->amount > 0 ? 'success' : 'danger' }} border border-{{ $txn->amount > 0 ? 'success' : 'danger' }} text-uppercase" style="font-size: 0.7rem;">
                                            {{ str_replace('_', ' ', $txn->type) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 small">
                                        {{ $txn->remarks }}
                                        @if($txn->from_user_id)
                                            <span class="text-info">(From: {{ $txn->fromUser->username }})</span>
                                        @elseif($txn->transfer_user_id)
                                            <span class="text-info">(To: {{ $txn->transferUser->username }})</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-end fw-bold {{ $txn->amount > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $txn->amount > 0 ? '+' : '' }}{{ number_format($txn->amount) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-5 text-center text-muted">No diamond transactions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($transactions->hasPages())
                    <div class="p-3 border-top border-secondary border-opacity-25">
                        {{ $transactions->appends(['purchase_page' => $purchaseHistory->currentPage()])->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>

            <!-- Purchase History Table -->
            <h5 class="section-header mb-4">
                <i class="bi bi-cart-check section-icon"></i> PURCHASE HISTORY
            </h5>

            <div class="neon-card overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0" style="background: transparent;">
                        <thead>
                            <tr>
                                <th class="py-3 px-4" style="border-bottom-color: rgba(255,255,255,0.1);">Date</th>
                                <th class="py-3 px-4" style="border-bottom-color: rgba(255,255,255,0.1);">Package</th>
                                <th class="py-3 px-4" style="border-bottom-color: rgba(255,255,255,0.1);">Method</th>
                                <th class="py-3 px-4" style="border-bottom-color: rgba(255,255,255,0.1);">Status</th>
                                <th class="py-3 px-4 text-end" style="border-bottom-color: rgba(255,255,255,0.1);">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchaseHistory as $purchase)
                                <tr @if($purchase->payment_method === 'manual') onclick="window.location='{{ route('payments.show', $purchase->id) }}'" style="cursor: pointer;" @endif class="{{ $purchase->payment_method === 'manual' ? 'clickable-row' : '' }}">
                                    <td class="py-3 px-4 text-white-50 small">{{ $purchase->created_at->format('M d, Y H:i') }}</td>
                                    <td class="py-3 px-4 small fw-bold text-info">
                                        {{ $purchase->package->name ?? 'Custom Package' }}
                                        @if($purchase->payment_method === 'manual')
                                            <i class="bi bi-chat-dots ms-2 text-warning" title="Open Discussion Thread"></i>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="badge bg-secondary bg-opacity-25 text-white small px-2">
                                            {{ strtoupper($purchase->payment_method) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($purchase->status === 'completed')
                                            <span class="badge rounded-pill bg-success small px-3">Completed</span>
                                        @elseif($purchase->status === 'pending')
                                            <span class="badge rounded-pill bg-warning text-dark small px-3">Pending</span>
                                        @else
                                            <span class="badge rounded-pill bg-danger small px-3">{{ strtoupper($purchase->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-end fw-bold text-white">
                                        {{ $purchase->currency }} {{ number_format($purchase->amount, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-5 text-center text-muted">No purchase attempts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($purchaseHistory->hasPages())
                    <div class="p-3 border-top border-secondary border-opacity-25">
                        {{ $purchaseHistory->appends(['ledger_page' => $transactions->currentPage()])->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('modals')
<!-- Payment Selection Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-info shadow-lg" style="backdrop-filter: blur(15px);">
            <div class="modal-header border-info">
                <h5 class="modal-title text-white text-uppercase fw-bold" id="paymentModalLabel" style="font-family: 'Orbitron', sans-serif;">
                    <i class="bi bi-credit-card-2-front me-2 text-info"></i> Select Payment Method
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <h4 id="modalPackageName" class="text-white mb-2"></h4>
                <div id="modalPackagePrice" class="text-info fs-5 fw-bold mb-4"></div>
                
                <p class="text-secondary small mb-4">Choose how you want to complete your purchase:</p>

                <div class="d-grid gap-3">
                    <div id="hitpayOption" class="d-none">
                        <form method="POST" action="{{ route('payments.store') }}">
                            @csrf
                            <input type="hidden" name="package_id" id="modalPackageId">
                            <input type="hidden" name="payment_method" value="hitpay">
                            <button type="submit" class="btn btn-lg btn-neon w-100 py-3 fw-bold">
                                <i class="bi bi-credit-card me-2"></i> PAY VIA HITPAY
                                <div class="x-small fw-normal mt-1 opacity-75">Credit Card, GCash, GrabPay, Maya</div>
                            </button>
                        </form>
                    </div>

                    <div id="manualOption" class="d-none">
                        @if($reachedManualLimit)
                            <button type="button" class="btn btn-lg btn-outline-secondary w-100 py-3 fw-bold border-dashed" disabled>
                                <i class="bi bi-qr-code me-2"></i> MANUAL PAYMENT
                                <div class="x-small fw-normal mt-1 opacity-75">Daily limit reached</div>
                            </button>
                            <div class="text-danger small mt-2 fw-bold">
                                <i class="bi bi-exclamation-circle me-1"></i> Hello!, we are currently limiting each user to only 3 manual payments per day.
                            </div>
                        @else
                            <a href="#" id="manualPaymentLink" class="btn btn-lg btn-outline-warning w-100 py-3 fw-bold">
                                <i class="bi bi-qr-code me-2"></i> MANUAL PAYMENT
                                <div class="x-small fw-normal mt-1 opacity-75">Scan QR & Upload Receipt</div>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer border-info bg-dark bg-opacity-50">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Maybe Later</button>
            </div>
        </div>
    </div>
</div>
@endpush

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentModal = document.getElementById('paymentModal');
    if (paymentModal) {
        paymentModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const diamonds = button.getAttribute('data-diamonds');
            const price = button.getAttribute('data-price');
            const allowHitpay = button.getAttribute('data-allow-hitpay') === 'true';
            const allowManual = button.getAttribute('data-allow-manual') === 'true';

            // Update text
            document.getElementById('modalPackageName').textContent = `${name} (${diamonds} Diamonds)`;
            document.getElementById('modalPackagePrice').textContent = price;
            
            // Update forms/links
            document.getElementById('modalPackageId').value = id;
            document.getElementById('manualPaymentLink').href = `{{ url('/payments/manual') }}/${id}`;

            // Toggle visibility
            document.getElementById('hitpayOption').classList.toggle('d-none', !allowHitpay);
            document.getElementById('manualOption').classList.toggle('d-none', !allowManual);
        });
    }
});
</script>

<style>
    .x-small { font-size: 0.75rem; }
    .clickable-row:hover {
        background-color: rgba(255, 255, 255, 0.05) !important;
    }
</style>
@endsection
