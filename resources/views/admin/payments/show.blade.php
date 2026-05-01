@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col d-flex justify-content-between align-items-center">
            <div>
                <h1 class="display-5 fw-bold text-uppercase mb-1" style="color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                    <i class="bi bi-receipt"></i> Transaction Details
                </h1>
                <p class="text-secondary lead mb-0">Ref: {{ $payment->reference }}</p>
            </div>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Transaction Info -->
        <div class="col-md-8">
            <div class="card bg-dark border-info rounded-4 overflow-hidden mb-4" style="box-shadow: 0 0 15px rgba(0, 240, 255, 0.1);">
                <div class="card-header border-info p-3 border-bottom-0 bg-transparent">
                    <h5 class="mb-0 text-uppercase fw-bold text-white" style="font-family: 'Orbitron', sans-serif;">Payment Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Status</div>
                            @if($payment->status === 'completed')
                                <span class="badge bg-success fs-6 text-uppercase tracking-wide px-3 py-2">Completed</span>
                            @elseif($payment->status === 'pending')
                                <span class="badge bg-warning text-dark fs-6 text-uppercase tracking-wide px-3 py-2">Pending</span>
                            @else
                                <span class="badge bg-danger fs-6 text-uppercase tracking-wide px-3 py-2">{{ ucfirst($payment->status) }}</span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Date</div>
                            <div class="text-white fs-5">{{ $payment->created_at->format('F j, Y, g:i a') }}</div>
                        </div>

                        <div class="col-sm-6">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Payment Method</div>
                            <div class="text-info fs-5 fw-bold">{{ $payment->payment_type ?: 'Unknown/Pending' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small text-uppercase fw-bold mb-1">HitPay ID</div>
                            <div class="text-white font-monospace">{{ $payment->hitpay_id ?: 'N/A' }}</div>
                        </div>
                        
                        <div class="col-sm-6">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Diamonds Purchased</div>
                            <div class="fs-4 fw-bold" style="color: var(--neon-magenta); text-shadow: 0 0 5px rgba(255,0,255,0.5);">
                                <i class="bi bi-gem"></i> {{ number_format($payment->diamonds_amount) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Breakdown -->
            <div class="card bg-dark border-info rounded-4 overflow-hidden" style="box-shadow: 0 0 15px rgba(57, 255, 20, 0.1);">
                <div class="card-header border-info p-3 border-bottom-0 bg-transparent">
                    <h5 class="mb-0 text-uppercase fw-bold text-white" style="font-family: 'Orbitron', sans-serif;">Financial Breakdown</h5>
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush bg-transparent">
                        <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center px-0 py-3 border-info">
                            <span class="text-muted text-uppercase fw-bold">Gross Amount</span>
                            <span class="fs-5">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</span>
                        </li>
                        <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center px-0 py-3 border-info">
                            <span class="text-muted text-uppercase fw-bold">HitPay Fees</span>
                            <span class="fs-5 text-danger">{{ $payment->fees !== null ? '- ' . $payment->currency . ' ' . number_format($payment->fees, 2) : 'Pending' }}</span>
                        </li>
                        <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center px-0 py-3 border-info border-bottom-0 mt-2 rounded">
                            <span class="text-uppercase fw-bold" style="color: var(--neon-green);">Net Amount (To Bank)</span>
                            <span class="fs-3 fw-bold" style="color: var(--neon-green); text-shadow: 0 0 10px rgba(57, 255, 20, 0.5);">
                                {{ $payment->net_amount !== null ? $payment->currency . ' ' . number_format($payment->net_amount, 2) : 'Pending' }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- User Info Sidebar -->
        <div class="col-md-4">
            <div class="card bg-dark border-info rounded-4 overflow-hidden h-100" style="box-shadow: 0 0 15px rgba(255, 221, 0, 0.1);">
                <div class="card-header border-info p-3 border-bottom-0 bg-transparent">
                    <h5 class="mb-0 text-uppercase fw-bold text-white" style="font-family: 'Orbitron', sans-serif;">Customer Info</h5>
                </div>
                <div class="card-body p-4 text-center">
                    <img src="{{ $payment->user->avatar_url }}" alt="{{ $payment->user->username }}" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid var(--neon-cyan); box-shadow: 0 0 15px rgba(0, 240, 255, 0.5);">
                    <h4 class="text-white fw-bold mb-1">{{ $payment->user->username }}</h4>
                    <p class="text-muted mb-4">{{ $payment->user->email }}</p>
                    
                    <hr class="border-info">
                    
                    <div class="text-start mt-4">
                        <div class="mb-3">
                            <div class="text-muted small text-uppercase fw-bold mb-1">User ID</div>
                            <div class="text-white">#{{ $payment->user->id }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Joined Date</div>
                            <div class="text-white">{{ $payment->user->created_at->format('M j, Y') }}</div>
                        </div>
                        <div>
                            <div class="text-muted small text-uppercase fw-bold mb-1">Current Balance</div>
                            <div class="text-white fw-bold" style="color: var(--neon-magenta);">
                                <i class="bi bi-gem"></i> {{ number_format($payment->user->diamonds) }} Diamonds
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer border-info border-top p-3 bg-transparent text-center">
                    <a href="{{ route('profile.show', $payment->user->username) }}" class="btn btn-outline-cyan w-100" style="color: var(--neon-cyan); border-color: var(--neon-cyan);">
                        View Full Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tracking-wide { letter-spacing: 1px; }
</style>
@endsection
