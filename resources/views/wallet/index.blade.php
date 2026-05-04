@extends('layouts.app')

@section('title', 'My Wallet')

@section('content')
<div class="container py-4">
    <h1 class="page-title mb-4">
        <span class="page-title-accent"><i class="bi bi-wallet2"></i></span> MY WALLET
    </h1>

    <div class="row g-4 mb-5">
        <!-- Balance Card -->
        <div class="col-md-4">
            <div class="neon-card text-center p-4 h-100 d-flex flex-column justify-content-center" style="border-color: #00f0ff;">
                <h5 class="text-muted small mb-2" style="font-family: 'Orbitron', sans-serif; letter-spacing: 2px;">CURRENT BALANCE</h5>
                <h2 class="display-4 mb-0" style="color: #00f0ff; text-shadow: 0 0 10px rgba(0, 240, 255, 0.5);">
                    <i class="bi bi-gem"></i> {{ number_format($balance, 2) }}
                </h2>
                <div class="mt-3">
                    <span class="badge" style="background-color: rgba(0,240,255,0.1); border: 1px solid #00f0ff; color: #00f0ff;">DIAMONDS</span>
                </div>
            </div>
        </div>

        <!-- Info/Action Card -->
        <div class="col-md-8">
            <div class="neon-card p-4 h-100" style="border-color: rgba(255, 255, 255, 0.1);">
                <h4 style="font-family: 'Orbitron', sans-serif; color: #fff;"><i class="bi bi-info-circle me-2"></i> DIAMONDS CURRENCY</h4>
                <p class="text-muted mt-3 mb-0" style="line-height: 1.6;">
                    Diamonds are the official in-game currency of Brag. You can use Diamonds to forge new Digital Cards, purchase premium templates, special borders, or trade them for services. 
                    <br><br>
                    <i class="bi bi-shield-lock text-warning me-1"></i> <em>Player-to-player transfers are currently disabled but will be arriving in a future update.</em>
                </p>
                <div class="mt-4">
                    <button class="btn btn-outline-secondary disabled" title="Coming Soon">
                        <i class="bi bi-arrow-left-right me-1"></i> TRANSFER DIAMONDS
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <h5 class="section-header mb-4">
                <i class="bi bi-cart section-icon"></i> PURCHASE DIAMONDS
            </h5>
            
            @if(session('error'))
                <div class="alert alert-danger mb-4 border-danger bg-danger bg-opacity-10 text-white rounded-3 shadow-sm">
                    <i class="bi bi-exclamation-octagon-fill me-2"></i> {{ session('error') }}
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

    <!-- Payment Selection Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
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
                                <button type="submit" class="btn btn-lg btn-neon-cyan w-100 py-3 fw-bold">
                                    <i class="bi bi-credit-card me-2"></i> PAY VIA HITPAY
                                    <div class="x-small fw-normal mt-1 opacity-75">Credit Card, GCash, GrabPay, Maya</div>
                                </button>
                            </form>
                        </div>

                        <div id="manualOption" class="d-none">
                            <a href="#" id="manualPaymentLink" class="btn btn-lg btn-outline-warning w-100 py-3 fw-bold">
                                <i class="bi bi-qr-code me-2"></i> MANUAL PAYMENT
                                <div class="x-small fw-normal mt-1 opacity-75">Scan QR & Upload Receipt</div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-info bg-dark bg-opacity-50">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Maybe Later</button>
                </div>
            </div>
        </div>
    </div>

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
    </style>

    <!-- Ledger Table -->
    <h5 class="section-header mb-4">
        <i class="bi bi-clock-history section-icon"></i> TRANSACTION HISTORY
    </h5>

    <div class="neon-card overflow-hidden">
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
                            <td class="py-3 px-4 text-muted small align-middle">
                                {{ $txn->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="py-3 px-4 align-middle">
                                @if($txn->type === 'system')
                                    <span class="badge bg-secondary text-light">System</span>
                                @elseif($txn->type === 'transfer')
                                    <span class="badge bg-info text-dark">Transfer</span>
                                @elseif($txn->type === 'purchased')
                                    <span class="badge bg-warning text-dark">Purchased</span>
                                @else
                                    <span class="badge bg-light text-dark">User</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 align-middle text-light" style="max-width: 300px;">
                                {{ $txn->remarks ?? '-' }}
                                
                                {{-- Show related user info if it was a transfer --}}
                                @if($txn->type === 'transfer')
                                    @if($txn->fromUser && $txn->fromUser->id !== auth()->id())
                                        <br><small class="text-muted">From: {{ $txn->fromUser->username }}</small>
                                    @endif
                                    @if($txn->transferUser && $txn->transferUser->id !== auth()->id())
                                        <br><small class="text-muted">To: {{ $txn->transferUser->username }}</small>
                                    @endif
                                @endif
                            </td>
                            <td class="py-3 px-4 text-end align-middle fw-bold" style="font-family: monospace; font-size: 1.1rem;">
                                @if($txn->credit > 0)
                                    <span class="text-success">+{{ number_format($txn->credit, 2) }}</span>
                                @elseif($txn->debit > 0)
                                    <span class="text-danger">-{{ number_format($txn->debit, 2) }}</span>
                                @else
                                    <span class="text-muted">0.00</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <div class="empty-state">
                                    <div class="empty-icon fs-1 mb-3"><i class="bi bi-inbox"></i></div>
                                    <div class="empty-text">No transactions found</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4 d-flex justify-content-center">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
