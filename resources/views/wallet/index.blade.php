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
                    <span class="badge" style="background-color: rgba(0,240,255,0.1); border: 1px solid #00f0ff; color: #00f0ff;">SHARDS</span>
                </div>
            </div>
        </div>

        <!-- Info/Action Card -->
        <div class="col-md-8">
            <div class="neon-card p-4 h-100" style="border-color: rgba(255, 255, 255, 0.1);">
                <h4 style="font-family: 'Orbitron', sans-serif; color: #fff;"><i class="bi bi-info-circle me-2"></i> SHARDS CURRENCY</h4>
                <p class="text-muted mt-3 mb-0" style="line-height: 1.6;">
                    Shards are the official in-game currency of Brag. You can use Shards to forge new Digital Cards, purchase premium templates, special borders, or trade them for services. 
                    <br><br>
                    <i class="bi bi-shield-lock text-warning me-1"></i> <em>Player-to-player transfers are currently disabled but will be arriving in a future update.</em>
                </p>
                <div class="mt-4">
                    <button class="btn btn-outline-secondary disabled" title="Coming Soon">
                        <i class="bi bi-arrow-left-right me-1"></i> TRANSFER SHARDS
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <h5 class="section-header mb-4">
                <i class="bi bi-cart section-icon"></i> PURCHASE SHARDS
            </h5>
            @if(isset($packages) && count($packages) > 0)
                <!-- Desktop View -->
                <div class="row g-3 d-none d-md-flex">
                    @foreach($packages as $package)
                        <div class="col-md-3">
                            <div class="neon-card text-center p-3 h-100" style="border-color: rgba(0, 240, 255, 0.3);">
                                <h6 style="color: #00f0ff; letter-spacing: 1px;">{{ $package['name'] }}</h6>
                                <div class="my-3">
                                    <i class="bi bi-gem" style="font-size: 2rem; color: #00f0ff; text-shadow: 0 0 10px rgba(0,240,255,0.5);"></i>
                                    <div class="fw-bold fs-4 mt-2">{{ $package['shards'] }} <small class="text-muted" style="font-size: 0.5em;">SHARDS</small></div>
                                </div>
                                <form method="POST" action="{{ route('payments.checkout') }}">
                                    @csrf
                                    <input type="hidden" name="package_id" value="{{ $package['id'] }}">
                                    <button type="submit" class="btn btn-outline-neon w-100" style="border-color: #39ff14; color: #39ff14;">
                                        Buy for {{ $package['currency'] }} {{ number_format($package['price'], 0) }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Mobile View (Carousel) -->
                <div class="d-block d-md-none position-relative px-4">
                    <div id="shardsCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner pb-2">
                            @php $isFirst = true; @endphp
                            @foreach($packages as $index => $package)
                                <div class="carousel-item {{ $isFirst ? 'active' : '' }}">
                                    @php $isFirst = false; @endphp
                                    <div class="neon-card text-center p-3 h-100 mx-auto" style="border-color: rgba(0, 240, 255, 0.3); max-width: 250px;">
                                        <h6 style="color: #00f0ff; letter-spacing: 1px;">{{ $package['name'] }}</h6>
                                        <div class="my-3">
                                            <i class="bi bi-gem" style="font-size: 2rem; color: #00f0ff; text-shadow: 0 0 10px rgba(0,240,255,0.5);"></i>
                                            <div class="fw-bold fs-4 mt-2">{{ $package['shards'] }} <small class="text-muted" style="font-size: 0.5em;">SHARDS</small></div>
                                        </div>
                                        <form method="POST" action="{{ route('payments.checkout') }}">
                                            @csrf
                                            <input type="hidden" name="package_id" value="{{ $package['id'] }}">
                                            <button type="submit" class="btn btn-outline-neon w-100" style="border-color: #39ff14; color: #39ff14;">
                                                Buy for {{ $package['currency'] }} {{ number_format($package['price'], 0) }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#shardsCarousel" data-bs-slide="prev" style="width: 10%; justify-content: flex-start; margin-left: -15px;">
                            <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: invert(1) sepia(1) saturate(5) hue-rotate(135deg);"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#shardsCarousel" data-bs-slide="next" style="width: 10%; justify-content: flex-end; margin-right: -15px;">
                            <span class="carousel-control-next-icon" aria-hidden="true" style="filter: invert(1) sepia(1) saturate(5) hue-rotate(135deg);"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            @else
                <div class="text-center text-muted">
                    No shard packages are currently available for purchase.
                </div>
            @endif
        </div>
    </div>

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
