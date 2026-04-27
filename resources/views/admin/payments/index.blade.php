@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5 fw-bold text-uppercase" style="color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                <i class="bi bi-cash-coin"></i> Sales Transactions
            </h1>
            <p class="text-secondary lead">View and filter all shard purchases via HitPay.</p>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-dark bg-opacity-50 border-secondary" style="backdrop-filter: blur(10px);">
                <div class="card-body d-flex gap-3 align-items-center">
                    <span class="text-uppercase fw-bold text-muted small"><i class="bi bi-link-45deg"></i> Quick Actions:</span>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-dark bg-opacity-75 border-secondary rounded-4" style="backdrop-filter: blur(10px);">
                <div class="card-header border-secondary border-bottom p-3">
                    <h5 class="mb-0 text-uppercase fw-bold text-white" style="font-family: 'Orbitron', sans-serif;">
                        <i class="bi bi-funnel-fill me-2" style="color: var(--neon-magenta);"></i> Filters & Sorting
                    </h5>
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('admin.payments.index') }}" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label text-muted small text-uppercase fw-bold">Search User</label>
                            <input type="text" name="user_search" class="form-control bg-dark text-white border-secondary" placeholder="Username or Email" value="{{ request('user_search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small text-uppercase fw-bold">Status</label>
                            <select name="status" class="form-select bg-dark text-white border-secondary">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small text-uppercase fw-bold">Payment Type</label>
                            <select name="payment_type" class="form-select bg-dark text-white border-secondary">
                                <option value="">All Types</option>
                                @foreach($paymentTypes as $pt)
                                    <option value="{{ $pt }}" {{ request('payment_type') == $pt ? 'selected' : '' }}>{{ $pt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small text-uppercase fw-bold">Date From</label>
                            <input type="date" name="date_from" class="form-control bg-dark text-white border-secondary" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small text-uppercase fw-bold">Date To</label>
                            <input type="date" name="date_to" class="form-control bg-dark text-white border-secondary" value="{{ request('date_to') }}">
                        </div>
                        
                        <div class="col-md-2 mt-3">
                            <label class="form-label text-muted small text-uppercase fw-bold">Sort By</label>
                            <select name="sort_by" class="form-select bg-dark text-white border-secondary">
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date</option>
                                <option value="amount" {{ request('sort_by') == 'amount' ? 'selected' : '' }}>Amount</option>
                                <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                            </select>
                        </div>
                        <div class="col-md-2 mt-3">
                            <label class="form-label text-muted small text-uppercase fw-bold">Sort Dir</label>
                            <select name="sort_dir" class="form-select bg-dark text-white border-secondary">
                                <option value="desc" {{ request('sort_dir', 'desc') == 'desc' ? 'selected' : '' }}>Desc</option>
                                <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>Asc</option>
                            </select>
                        </div>

                        <div class="col-md-3 mt-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100" style="background-color: var(--neon-cyan); color: #000; border-color: var(--neon-cyan); box-shadow: 0 0 10px rgba(0,240,255,0.5);">Filter</button>
                            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-dark bg-opacity-75 border-secondary rounded-4" style="backdrop-filter: blur(10px);">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0 align-middle text-nowrap">
                            <thead>
                                <tr>
                                    <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold px-4 py-3">Date</th>
                                    <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Ref</th>
                                    <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">User</th>
                                    <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Gross</th>
                                    <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Net</th>
                                    <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Method</th>
                                    <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Status</th>
                                    <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3 text-end px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr onclick="window.location='{{ route('admin.payments.show', $payment->id) }}'" style="cursor: pointer;">
                                        <td class="px-4 py-3 text-secondary">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="py-3 text-white">{{ $payment->reference }}</td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ $payment->user->avatar_url }}" alt="{{ $payment->user->username }}" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;">
                                                <div>
                                                    <div class="fw-bold text-white small">{{ $payment->user->username }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 text-white">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                                        <td class="py-3" style="color: var(--neon-green);">
                                            {{ $payment->net_amount !== null ? $payment->currency . ' ' . number_format($payment->net_amount, 2) : '-' }}
                                        </td>
                                        <td class="py-3 text-info">{{ $payment->payment_type ?: '-' }}</td>
                                        <td class="py-3">
                                            @if($payment->status === 'completed')
                                                <span class="badge rounded-pill bg-success text-uppercase tracking-wide">Completed</span>
                                            @elseif($payment->status === 'pending')
                                                <span class="badge rounded-pill bg-warning text-dark text-uppercase tracking-wide">Pending</span>
                                            @else
                                                <span class="badge rounded-pill bg-danger text-uppercase tracking-wide">{{ ucfirst($payment->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-end px-4">
                                            <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-5">No transactions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($payments->hasPages())
                    <div class="card-footer border-secondary border-top p-3 bg-transparent">
                        {{ $payments->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .tracking-wide { letter-spacing: 1px; }
</style>
@endsection
