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

    @if(session('success'))
        <div class="alert alert-success p-2 small mb-4"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger p-2 small mb-4"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
    @endif

    <div class="row g-4">
        <!-- Main Transaction Info -->
        <div class="col-md-8">
            <!-- Payment Summary -->
            <div class="card bg-dark border-info rounded-4 overflow-hidden mb-4 shadow-lg">
                <div class="card-header border-info p-3 bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-uppercase fw-bold text-white" style="font-family: 'Orbitron', sans-serif;">Payment Information</h5>
                    @if($payment->payment_method === 'manual')
                        <span class="badge bg-warning text-dark text-uppercase small fw-bold">Manual Payment</span>
                    @else
                        <span class="badge bg-primary text-uppercase small fw-bold">HitPay Payment</span>
                    @endif
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Status</div>
                            @if($payment->status === 'completed')
                                <span class="badge bg-success fs-6 text-uppercase px-3 py-2">Completed</span>
                            @elseif($payment->status === 'pending')
                                <span class="badge bg-warning text-dark fs-6 text-uppercase px-3 py-2">Pending</span>
                            @else
                                <span class="badge bg-danger fs-6 text-uppercase px-3 py-2">{{ ucfirst($payment->status) }}</span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Transaction Date</div>
                            <div class="text-white fs-5">{{ $payment->created_at->format('F j, Y, g:i a') }}</div>
                        </div>

                        <div class="col-sm-6">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Diamond Package</div>
                            <div class="text-info fs-5 fw-bold">{{ $payment->package->name ?? 'Custom Amount' }}</div>
                        </div>
                        
                        <div class="col-sm-6">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Diamonds Credited</div>
                            <div class="fs-4 fw-bold" style="color: var(--neon-magenta); text-shadow: 0 0 5px rgba(255,0,255,0.5);">
                                <i class="bi bi-gem"></i> {{ number_format($payment->diamonds_amount) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($payment->payment_method === 'manual' && $payment->proof_path)
                <!-- Manual Payment Proof -->
                <div class="card bg-dark border-warning rounded-4 overflow-hidden mb-4 shadow-lg">
                    <div class="card-header border-warning p-3 bg-transparent">
                        <h5 class="mb-0 text-uppercase fw-bold text-white" style="font-family: 'Orbitron', sans-serif;">
                            <i class="bi bi-image me-2 text-warning"></i> Proof of Payment
                        </h5>
                    </div>
                    <div class="card-body p-4 text-center">
                        <a href="{{ asset('storage/' . $payment->proof_path) }}" target="_blank" title="Click to enlarge">
                            <img src="{{ asset('storage/' . $payment->proof_path) }}" alt="Proof of Payment" class="img-fluid rounded border border-secondary" style="max-height: 400px; cursor: zoom-in;">
                        </a>
                        
                        @if($payment->status === 'pending' || $payment->status === 'flagged')
                            <div class="mt-4 pt-4 border-top border-secondary">
                                @if($payment->status === 'pending')
                                    <div class="alert bg-warning bg-opacity-10 border-warning text-warning small mb-3">
                                        <i class="bi bi-clock-history"></i> Auto-approval timer: 
                                        <strong>{{ $payment->auto_approve_at ? $payment->auto_approve_at->diffForHumans() : 'N/A' }}</strong>
                                        ({{ $payment->auto_approve_at ? $payment->auto_approve_at->format('g:i A') : '-' }})
                                    </div>
                                @endif
                                <div class="d-flex justify-content-center gap-3">
                                    <button type="button" onclick="confirmApproval()" class="btn btn-lg btn-success px-4 fw-bold text-uppercase">
                                        <i class="bi bi-check-circle me-1"></i> Approve
                                    </button>
                                    @if($payment->status === 'pending')
                                        <button type="button" onclick="promptFlagging()" class="btn btn-lg btn-warning px-4 fw-bold text-uppercase">
                                            <i class="bi bi-flag me-1"></i> Flag
                                        </button>
                                    @endif
                                    <button type="button" onclick="promptRejection()" class="btn btn-lg btn-outline-danger px-4 fw-bold text-uppercase">
                                        <i class="bi bi-x-circle me-1"></i> Reject
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="mt-4 pt-4 border-top border-secondary">
                                <button type="button" onclick="confirmRevert()" class="btn btn-outline-warning fw-bold">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Revert to Flagged
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Discussion Thread -->
                <div class="card bg-dark border-info rounded-4 overflow-hidden mb-4 shadow-lg">
                    <div class="card-header border-info p-3 bg-transparent">
                        <h5 class="mb-0 text-uppercase fw-bold text-white" style="font-family: 'Orbitron', sans-serif;">
                            <i class="bi bi-chat-dots me-2 text-info"></i> Discussion Thread
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="comments-list mb-4" style="max-height: 400px; overflow-y: auto;">
                            @forelse($payment->comments as $comment)
                                <div class="mb-3 p-3 rounded-3 {{ $comment->user->is_admin ? 'bg-info bg-opacity-10 border border-info ms-5' : 'bg-secondary bg-opacity-10 border border-secondary me-5' }}">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold {{ $comment->user->is_admin ? 'text-info' : 'text-white' }}">
                                            <i class="bi {{ $comment->user->is_admin ? 'bi-shield-check' : 'bi-person' }} me-1"></i>
                                            {{ $comment->user->username }} 
                                            @if($comment->user->is_admin)<small class="text-uppercase" style="font-size: 0.7em;">(Admin)</small>@endif
                                        </span>
                                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="text-white">{{ $comment->comment }}</div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-3">No messages in this thread yet.</div>
                            @endforelse
                        </div>

                        <form action="{{ route('admin.payments.comments.store', $payment->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="comment" class="form-label text-muted small text-uppercase fw-bold">Post a Reply</label>
                                <textarea name="comment" id="comment" rows="3" class="form-control bg-dark text-white border-info" required placeholder="Write your message to the user here..."></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-info fw-bold">
                                    <i class="bi bi-send me-1"></i> Send Reply
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Financial Breakdown -->
            <div class="card bg-dark border-info rounded-4 overflow-hidden shadow-lg">
                <div class="card-header border-info p-3 bg-transparent">
                    <h5 class="mb-0 text-uppercase fw-bold text-white" style="font-family: 'Orbitron', sans-serif;">Financial Breakdown</h5>
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush bg-transparent">
                        <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center px-0 py-3 border-info">
                            <span class="text-muted text-uppercase fw-bold">Gross Amount</span>
                            <span class="fs-5">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</span>
                        </li>
                        <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center px-0 py-3 border-info">
                            <span class="text-muted text-uppercase fw-bold">Payment Fees</span>
                            <span class="fs-5 text-danger">{{ $payment->fees !== null ? '- ' . $payment->currency . ' ' . number_format($payment->fees, 2) : '0.00' }}</span>
                        </li>
                        <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center px-0 py-3 border-info border-bottom-0 mt-2 rounded">
                            <span class="text-uppercase fw-bold" style="color: var(--neon-green);">Net Amount</span>
                            <span class="fs-3 fw-bold" style="color: var(--neon-green); text-shadow: 0 0 10px rgba(57, 255, 20, 0.5);">
                                {{ $payment->net_amount !== null ? $payment->currency . ' ' . number_format($payment->net_amount, 2) : $payment->currency . ' ' . number_format($payment->amount - ($payment->fees ?? 0), 2) }}
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="card-footer border-info border-top p-3 bg-info bg-opacity-10">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="text-muted x-small text-uppercase fw-bold">Collection Status</div>
                            <div class="{{ $payment->collected_at ? 'text-success' : 'text-warning' }} fw-bold">
                                <i class="bi {{ $payment->collected_at ? 'bi-check-circle' : 'bi-hourglass' }}"></i>
                                {{ $payment->collected_at ? 'Collected' : 'Uncollected' }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted x-small text-uppercase fw-bold">Collected By</div>
                            <div class="text-white fw-bold">{{ $payment->collector->username ?? ($payment->collected_at ? 'System' : '-') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Info Sidebar -->
        <div class="col-md-4">
            <div class="card bg-dark border-info rounded-4 overflow-hidden h-100 shadow-lg">
                <div class="card-header border-info p-3 bg-transparent">
                    <h5 class="mb-0 text-uppercase fw-bold text-white" style="font-family: 'Orbitron', sans-serif;">Customer Info</h5>
                </div>
                <div class="card-body p-4 text-center">
                    <img src="{{ $payment->user->avatar_url }}" alt="{{ $payment->user->username }}" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid var(--neon-cyan); box-shadow: 0 0 15px rgba(0, 240, 255, 0.5);">
                    <h4 class="text-white fw-bold mb-1">{{ $payment->user->username }}</h4>
                    <p class="text-muted mb-4 small">{{ $payment->user->email }}</p>
                    
                    <hr class="border-info">
                    
                    <div class="text-start mt-4">
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-muted small text-uppercase">Account ID:</span>
                            <span class="text-white small">#{{ $payment->user->id }}</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-muted small text-uppercase">Joined:</span>
                            <span class="text-white small">{{ $payment->user->created_at->format('M j, Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small text-uppercase">Balance:</span>
                            <span class="text-white fw-bold" style="color: var(--neon-magenta);">
                                <i class="bi bi-gem"></i> {{ number_format($payment->user->diamonds_balance) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-footer border-info border-top p-3 bg-transparent text-center">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.edit', $payment->user->id) }}" class="btn btn-outline-info">
                            <i class="bi bi-person-gear"></i> Manage User
                        </a>
                        <a href="{{ route('profile.show', $payment->user->username) }}" class="btn btn-sm btn-link text-info">View Public Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Approval/Rejection Forms -->
<form id="approveForm" action="{{ route('admin.payments.approve', $payment->id) }}" method="POST" class="d-none">@csrf</form>
<form id="rejectForm" action="{{ route('admin.payments.reject', $payment->id) }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="reason" id="rejectReasonInput">
</form>
<form id="revertForm" action="{{ route('admin.payments.revert', $payment->id) }}" method="POST" class="d-none">@csrf</form>
<form id="flagForm" action="{{ route('admin.payments.flag', $payment->id) }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="reason" id="flagReasonInput">
</form>

<script>
function confirmApproval() {
    window.neonConfirm('Are you sure you want to APPROVE this payment? This will credit {{ $payment->diamonds_amount }} diamonds to {{ $payment->user->username }}.').then(confirmed => {
        if (confirmed) {
            document.getElementById('approveForm').submit();
        }
    });
}

function confirmRevert() {
    window.neonConfirm('Are you sure you want to REVERT this payment to FLAGGED? This will not subtract any diamonds if already credited, you must handle that separately.').then(confirmed => {
        if (confirmed) {
            document.getElementById('revertForm').submit();
        }
    });
}

function promptFlagging() {
    window.neonPrompt(
        'Why are you flagging this transaction? (e.g., Image too blurry, Wrong amount visible). This will be sent to the user.',
        '',
        'Flag for Review'
    ).then(reason => {
        if (reason !== null) {
            if (reason.trim().length > 0) {
                document.getElementById('flagReasonInput').value = reason;
                document.getElementById('flagForm').submit();
            } else {
                window.neonAlert('A reason is required to flag a transaction.');
            }
        }
    });
}

function promptRejection() {
    window.neonPrompt(
        'Please provide a reason for rejection (optional):',
        '',
        'Transaction Rejection'
    ).then(reason => {
        if (reason !== null) {
            document.getElementById('rejectReasonInput').value = reason;
            document.getElementById('rejectForm').submit();
        }
    });
}
</script>

<style>
    .x-small { font-size: 0.7rem; }
</style>
@endsection
