@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('admin.verifications.index') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left"></i> Back to Applications
        </a>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="neon-card p-4 text-center mb-4">
                <img src="{{ $verification->user->avatar_url }}" class="rounded-circle border border-info mb-3 shadow" style="width: 120px; height: 120px; object-fit: cover;">
                <h2 class="h4 text-uppercase fw-bold" style="font-family: 'Orbitron', sans-serif;">
                    <x-username :user="$verification->user" />
                </h2>
                <p class="text-secondary mb-0">{{ $verification->user->email }}</p>
                <div class="mt-2 text-info small fw-bold">
                    {{ $verification->user->firstname }} {{ $verification->user->lastname }}
                </div>
                @if($verification->user->birthdate)
                    <div class="text-secondary small">
                        Born: {{ \Carbon\Carbon::parse($verification->user->birthdate)->format('M d, Y') }}
                    </div>
                @endif
                <div class="mt-3">
                    <span class="badge bg-{{ $verification->status === 'pending' ? 'warning text-dark' : ($verification->status === 'approved' ? 'success' : 'danger') }} text-uppercase px-3 py-2">
                        {{ $verification->status }}
                    </span>
                </div>
                <hr class="border-secondary border-opacity-25 my-4">
                <div class="text-start">
                    <div class="small text-muted text-uppercase fw-bold mb-1">Submitted</div>
                    <div class="mb-3">{{ $verification->created_at->format('M d, Y H:i:s') }}</div>
                    
                    @if($verification->reviewed_at)
                        <div class="small text-muted text-uppercase fw-bold mb-1">Reviewed By</div>
                        <div class="mb-3"><x-username :user="$verification->reviewer" /></div>
                        <div class="small text-muted text-uppercase fw-bold mb-1">Reviewed At</div>
                        <div class="mb-3">{{ $verification->reviewed_at->format('M d, Y H:i:s') }}</div>
                    @endif
                </div>
            </div>

            @if($verification->status === 'pending')
                <div class="neon-card p-4">
                    <h3 class="h5 text-uppercase fw-bold mb-3" style="font-family: 'Orbitron', sans-serif;">Make Decision</h3>
                    <form action="{{ route('admin.verifications.approve', $verification->id) }}" method="POST" class="mb-2">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">ADMIN NOTES (OPTIONAL)</label>
                            <textarea name="admin_notes" class="form-control bg-dark text-white border-info" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100 fw-bold" onclick="return confirm('Are you sure you want to APPROVE this user?')">
                            <i class="bi bi-check-circle-fill"></i> APPROVE IDENTITY
                        </button>
                    </form>
                    <form action="{{ route('admin.verifications.reject', $verification->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 fw-bold" onclick="return confirm('Are you sure you want to REJECT this application?')">
                            <i class="bi bi-x-circle-fill"></i> REJECT APPLICATION
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="col-lg-8">
            <div class="neon-card p-4 mb-4">
                <h3 class="h5 text-uppercase fw-bold mb-4" style="font-family: 'Orbitron', sans-serif; color: var(--neon-cyan);">Verification Documents</h3>
                
                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold text-uppercase mb-3">1. Government Issued ID</label>
                    <div class="rounded-4 overflow-hidden border border-info border-opacity-25 bg-black text-center p-2">
                        <img src="{{ route('admin.verifications.photo', ['id' => $verification->id, 'type' => 'id']) }}" class="img-fluid rounded-3" style="max-height: 500px;" alt="ID Photo">
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label text-muted small fw-bold text-uppercase mb-3">2. Selfie Holding ID</label>
                    <div class="rounded-4 overflow-hidden border border-info border-opacity-25 bg-black text-center p-2">
                        <img src="{{ route('admin.verifications.photo', ['id' => $verification->id, 'type' => 'selfie']) }}" class="img-fluid rounded-3" style="max-height: 500px;" alt="Selfie Photo">
                    </div>
                </div>
            </div>

            @if($verification->admin_notes)
                <div class="neon-card p-4 border-warning">
                    <h3 class="h5 text-uppercase fw-bold mb-3 text-warning">Admin Review Notes</h3>
                    <div class="text-light opacity-75" style="white-space: pre-line;">{{ $verification->admin_notes }}</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
