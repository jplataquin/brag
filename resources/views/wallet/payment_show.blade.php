@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="display-6 fw-bold text-uppercase mb-1" style="color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                        <i class="bi bi-receipt"></i> Payment Details
                    </h1>
                    <p class="text-secondary mb-0">Ref: {{ $payment->reference }}</p>
                </div>
                <a href="{{ route('wallet.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Wallet
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success p-2 small mb-4"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
            @endif

            <!-- Payment Status Card -->
            <div class="card bg-dark border-info rounded-4 overflow-hidden mb-4 shadow-lg">
                <div class="card-header border-info p-3 bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-uppercase fw-bold text-white" style="font-family: 'Orbitron', sans-serif;">Request Summary</h5>
                    @if($payment->status === 'completed')
                        <span class="badge bg-success rounded-pill px-3">Completed</span>
                    @elseif($payment->status === 'pending')
                        <span class="badge bg-warning text-dark rounded-pill px-3">Pending Review</span>
                    @else
                        <span class="badge bg-danger rounded-pill px-3">{{ strtoupper($payment->status) }}</span>
                    @endif
                </div>
                <div class="card-body p-4 text-center">
                    <div class="row g-3">
                        <div class="col-sm-6 text-start">
                            <div class="text-muted small text-uppercase fw-bold">Package</div>
                            <div class="text-white fs-5 fw-bold">{{ $payment->package->name ?? 'Custom Package' }}</div>
                        </div>
                        <div class="col-sm-6 text-end">
                            <div class="text-muted small text-uppercase fw-bold">Amount Paid</div>
                            <div class="text-info fs-5 fw-bold">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</div>
                        </div>
                    </div>

                    @if($payment->proof_path)
                        <div class="mt-4 pt-4 border-top border-secondary">
                            <div class="text-muted small text-uppercase fw-bold mb-2">Your Uploaded Proof</div>
                            <img src="{{ asset('storage/' . $payment->proof_path) }}" alt="Proof" class="img-fluid rounded-3 border border-secondary shadow-sm" style="max-height: 300px;">
                        </div>
                    @endif
                </div>
            </div>

            <!-- Agreement Summary -->
            @if($payment->agreement)
                <div class="card bg-dark border-secondary rounded-4 overflow-hidden mb-4 opacity-75">
                    <div class="card-body p-3 small text-secondary">
                        <i class="bi bi-info-circle me-1"></i> You agreed to the manual payment terms on {{ $payment->created_at->format('M d, Y') }}.
                    </div>
                </div>
            @endif

            <!-- Discussion Thread -->
            <div class="card bg-dark border-info rounded-4 overflow-hidden mb-5 shadow-lg">
                <div class="card-header border-info p-3 bg-transparent">
                    <h5 class="mb-0 text-uppercase fw-bold text-white" style="font-family: 'Orbitron', sans-serif;">
                        <i class="bi bi-chat-dots me-2 text-info"></i> Discussion Thread
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="comments-list mb-4" style="max-height: 500px; overflow-y: auto;">
                        @forelse($payment->comments as $comment)
                            <div class="mb-3 p-3 rounded-3 {{ $comment->user->is_admin ? 'bg-info bg-opacity-10 border border-info me-5' : 'bg-secondary bg-opacity-10 border border-secondary ms-5' }}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold {{ $comment->user->is_admin ? 'text-info' : 'text-white' }}">
                                        <i class="bi {{ $comment->user->is_admin ? 'bi-shield-check' : 'bi-person' }} me-1"></i>
                                        {{ $comment->user->is_admin ? 'Brag Administrator' : 'You' }}
                                    </span>
                                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="text-white">{{ $comment->comment }}</div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">No messages yet. Our team will comment here if there are issues with your proof.</div>
                        @endforelse
                    </div>

                    @if($payment->status !== 'completed')
                        <form action="{{ route('payments.comments.store', $payment->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="comment" class="form-label text-muted small text-uppercase fw-bold">Post a Reply</label>
                                <textarea name="comment" id="comment" rows="3" class="form-control bg-dark text-white border-info" required placeholder="Write your message to the administrator..."></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-neon-cyan fw-bold">
                                    <i class="bi bi-send me-1"></i> Send Reply
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
