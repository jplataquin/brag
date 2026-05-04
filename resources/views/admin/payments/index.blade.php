@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5 fw-bold text-uppercase" style="color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                <i class="bi bi-cash-coin"></i> Sales Transactions
            </h1>
            <p class="text-secondary lead">Manage diamond purchases, manual payments, and collections.</p>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-dark bg-opacity-50 border-info" style="backdrop-filter: blur(10px);">
                <div class="card-body d-flex gap-3 align-items-center">
                    <span class="text-uppercase fw-bold text-muted small"><i class="bi bi-link-45deg"></i> Quick Actions:</span>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-info btn-sm d-print-none">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-light btn-sm ms-auto d-print-none">
                        <i class="bi bi-printer"></i> Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4 d-print-none">
        <div class="col-12">
            <div class="card bg-dark bg-opacity-75 border-info rounded-4" style="backdrop-filter: blur(10px);">
                <div class="card-header border-info border-bottom p-3">
                    <h5 class="mb-0 text-uppercase fw-bold text-white" style="font-family: 'Orbitron', sans-serif;">
                        <i class="bi bi-funnel-fill me-2" style="color: var(--neon-magenta);"></i> Filters & Sorting
                    </h5>
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('admin.payments.index') }}" method="GET" class="row g-3 align-items-end" id="filterForm">
                        <div class="col-md-3 position-relative">
                            <label class="form-label text-muted small text-uppercase fw-bold">Username</label>
                            <input type="text" name="username" id="usernameInput" class="form-control bg-dark text-white border-info" placeholder="Auto-suggest..." value="{{ request('username') }}" autocomplete="off">
                            <div id="userSuggestions" class="position-absolute w-100 bg-dark border border-info rounded-bottom d-none shadow-lg" style="z-index: 1000; top: 100%;"></div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small text-uppercase fw-bold">Status</label>
                            <select name="status" class="form-select bg-dark text-white border-info">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small text-uppercase fw-bold">Method</label>
                            <select name="payment_method" class="form-select bg-dark text-white border-info">
                                <option value="">All Methods</option>
                                <option value="hitpay" {{ request('payment_method') == 'hitpay' ? 'selected' : '' }}>HitPay</option>
                                <option value="manual" {{ request('payment_method') == 'manual' ? 'selected' : '' }}>Manual</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small text-uppercase fw-bold">Date From</label>
                            <input type="date" name="date_from" class="form-control bg-dark text-white border-info" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small text-uppercase fw-bold">Date To</label>
                            <input type="date" name="date_to" class="form-control bg-dark text-white border-info" value="{{ request('date_to') }}">
                        </div>

                        <div class="col-md-3 mt-3 d-flex gap-2">
                            <button type="submit" class="btn w-100 fw-bold text-white" style="background-color: var(--neon-cyan); box-shadow: 0 0 10px rgba(0,240,255,0.5);">Filter</button>
                            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Mass Actions Bar -->
    <div id="massActionsBar" class="p-3 bg-dark border border-info rounded-3 d-none d-print-none mb-3 d-flex align-items-center gap-3 shadow-lg">
        <span class="text-info fw-bold text-uppercase small"><i class="bi bi-check2-all me-1"></i> <span id="selectedCount">0</span> Selected</span>
        <button type="button" onclick="confirmMassAction('approve')" class="btn btn-sm btn-success rounded-pill px-3 fw-bold">
            <i class="bi bi-check-circle me-1"></i> Mass Approve
        </button>
        <button type="button" onclick="confirmMassAction('reject')" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">
            <i class="bi bi-x-circle me-1"></i> Mass Reject
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success p-2 small mb-3"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger p-2 small mb-3"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
    @endif

    <!-- Payments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-dark bg-opacity-75 border-info rounded-4 shadow-lg" style="backdrop-filter: blur(10px);">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0 align-middle text-nowrap">
                            <thead>
                                <tr>
                                    <th scope="col" class="bg-transparent px-4 py-3 d-print-none">
                                        <input type="checkbox" id="selectAll" class="form-check-input bg-dark border-info">
                                    </th>
                                    <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Date</th>
                                    <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Ref</th>
                                    <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">User</th>
                                    <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Package</th>
                                    <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Amount</th>
                                    <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Method</th>
                                    <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Status</th>
                                    <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Collection</th>
                                    <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3 text-end px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td class="px-4 py-3 d-print-none">
                                            @if($payment->status === 'pending' && $payment->payment_method === 'manual')
                                                <input type="checkbox" name="payment_ids[]" value="{{ $payment->id }}" class="form-check-input payment-checkbox bg-dark border-info">
                                            @endif
                                        </td>
                                        <td class="py-3 text-secondary small">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="py-3 text-white small">{{ $payment->reference }}</td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ $payment->user->avatar_url }}" alt="{{ $payment->user->username }}" class="rounded-circle" style="width: 25px; height: 25px; object-fit: cover;">
                                                <span class="fw-bold text-white small">{{ $payment->user->username }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 small text-white-50">
                                            @if($payment->package)
                                                {{ $payment->package->name }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="py-3 fw-bold text-white">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                                        <td class="py-3">
                                            @if($payment->payment_method === 'hitpay')
                                                <span class="badge bg-primary bg-opacity-25 text-primary border border-primary small px-2 py-1">HitPay</span>
                                            @else
                                                <span class="badge bg-warning bg-opacity-25 text-warning border border-warning small px-2 py-1">Manual</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            @if($payment->status === 'completed')
                                                <span class="badge rounded-pill bg-success small px-2">Completed</span>
                                            @elseif($payment->status === 'pending')
                                                <span class="badge rounded-pill bg-warning text-dark small px-2">Pending</span>
                                            @else
                                                <span class="badge rounded-pill bg-danger small px-2">{{ strtoupper($payment->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 small">
                                            @if($payment->collected_at)
                                                <div class="text-success d-flex align-items-center gap-1">
                                                    <i class="bi bi-check2-circle"></i> Collected
                                                </div>
                                                <div class="text-muted x-small" style="font-size: 0.8em;">
                                                    {{ $payment->collected_at->format('m/d H:i') }} by {{ $payment->collector->username ?? 'Auto' }}
                                                </div>
                                            @else
                                                <div class="text-muted">Uncollected</div>
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
                                        <td colspan="10" class="text-center text-muted py-5">No transactions found for the selected criteria.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot style="border-top: 2px solid var(--neon-cyan); background-color: rgba(255, 255, 255, 0.03);">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold text-uppercase py-3 text-muted tracking-wide">Grand Total (Uncollected Only):</td>
                                    <td class="fw-bold py-3 fs-5" style="color: var(--neon-cyan);">PHP {{ number_format($grandTotal, 2) }}</td>
                                    <td colspan="4"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @if($payments->hasPages())
                    <div class="card-footer border-info border-top p-3 bg-transparent">
                        {{ $payments->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Mass Action Forms (Hidden) -->
<form id="massApproveForm" action="{{ route('admin.payments.mass_approve') }}" method="POST" class="d-none">@csrf</form>
<form id="massRejectForm" action="{{ route('admin.payments.mass_reject') }}" method="POST" class="d-none">@csrf</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.payment-checkbox');
    const massActionsBar = document.getElementById('massActionsBar');
    const selectedCount = document.getElementById('selectedCount');

    function updateMassActionsBar() {
        const checkedCount = document.querySelectorAll('.payment-checkbox:checked').length;
        selectedCount.textContent = checkedCount;
        if (checkedCount > 0) {
            massActionsBar.classList.remove('d-none');
        } else {
            massActionsBar.classList.add('d-none');
        }
    }

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateMassActionsBar();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateMassActionsBar);
    });

    // Username Auto-suggest
    const usernameInput = document.getElementById('usernameInput');
    const suggestionsBox = document.getElementById('userSuggestions');
    let debounceTimer;

    usernameInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const term = this.value;

        if (term.length < 2) {
            suggestionsBox.classList.add('d-none');
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`{{ route('admin.payments.users_suggest') }}?term=${term}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        suggestionsBox.innerHTML = '';
                        data.forEach(username => {
                            const item = document.createElement('div');
                            item.className = 'p-2 border-bottom border-secondary text-white cursor-pointer hover-bg-dark';
                            item.style.cursor = 'pointer';
                            item.textContent = username;
                            item.onclick = () => {
                                usernameInput.value = username;
                                suggestionsBox.classList.add('d-none');
                            };
                            suggestionsBox.appendChild(item);
                        });
                        suggestionsBox.classList.remove('d-none');
                    } else {
                        suggestionsBox.classList.add('d-none');
                    }
                });
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!usernameInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.classList.add('d-none');
        }
    });
});

function confirmMassAction(action) {
    const selectedIds = Array.from(document.querySelectorAll('.payment-checkbox:checked')).map(cb => cb.value);
    const form = action === 'approve' ? document.getElementById('massApproveForm') : document.getElementById('massRejectForm');
    
    window.neonConfirm(`Are you sure you want to mass ${action} ${selectedIds.length} payments?`).then(confirmed => {
        if (confirmed) {
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
            });
            form.submit();
        }
    });
}
</script>

<style>
    .hover-bg-dark:hover { background-color: rgba(255,255,255,0.1); }
    .tracking-wide { letter-spacing: 1px; }
    .x-small { font-size: 0.75rem; }
    
    @media print {
        nav, header, footer, .d-print-none, form, .pagination, .card-footer, #massActionsBar {
            display: none !important;
        }
        body, .container, .card, .card-body {
            background: transparent !important;
            color: #000 !important;
        }
        .table { color: #000 !important; border: 1px solid #000 !important; }
        .table th, .table td { border-bottom: 1px solid #000 !important; color: #000 !important; }
        .badge { border: 1px solid #000 !important; color: #000 !important; background: transparent !important; }
    }
</style>
@endsection
