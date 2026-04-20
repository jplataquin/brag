@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Quick Actions -->
<div class="mb-4 d-md-none">
    <h5 class="section-header">
        <i class="bi bi-lightning-fill section-icon" style="color: #ffdd00;"></i> QUICK ACTIONS
    </h5>
    <div class="row g-3">
        <div class="col-6 col-md-3">
            <a href="{{ route('battles.create') }}" class="quick-action-card">
                <i class="bi bi-plus-lg" style="color: #ff00ff;"></i>
                <span>New Battle</span>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <button type="button" class="quick-action-card border-0" id="btn-scan-qr">
                <i class="bi bi-qr-code-scan"></i>
                <span>Scan</span>
            </button>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('cards.index') }}" class="quick-action-card">
                <i class="bi bi-collection-fill" style="color: #00f0ff;"></i>
                <span>Inventory</span>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('templates.index') }}" class="quick-action-card">
                <i class="bi bi-palette-fill" style="color: #39ff14;"></i>
                <span>Template</span>
            </a>
        </div>
    </div>
</div>

<h1 class="page-title">
    <span class="page-title-accent"><i class="bi bi-grid-fill"></i></span> COMMAND CENTER
</h1>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md">
        <div class="stat-box">
            <div class="stat-value">{{ $stats['total_cards'] }}</div>
            <div class="stat-label">Cards</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-box">
            <div class="stat-value" style="color: #ffdd00;">{{ $stats['total_trophies'] }}</div>
            <div class="stat-label">Trophies</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-box">
            <div class="stat-value" style="color: #39ff14;">{{ $stats['total_wins'] }}</div>
            <div class="stat-label">Wins</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-box">
            <div class="stat-value" style="color: #ff00ff;">{{ $stats['total_templates'] }}</div>
            <div class="stat-label">Templates</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-box">
            <div class="stat-value" style="color: #ff6600;">{{ $stats['total_battles'] }}</div>
            <div class="stat-label">Battles</div>
        </div>
    </div>
</div>

<!-- Pending Invites -->
@if($pendingInvites->count() > 0)
<div class="neon-card p-3 mb-4" style="border-color: rgba(255,221,0,0.3);">
    <h5 class="section-header" style="color: #ffdd00; border-color: rgba(255,221,0,0.15);">
        <i class="bi bi-envelope-fill section-icon" style="color: #ffdd00;"></i> Pending Invites
    </h5>
    @foreach($pendingInvites as $invite)
    <div class="d-flex align-items-center justify-content-between mb-2 p-2" style="background: rgba(255,221,0,0.03); border-radius: 8px;">
        <div>
            <span style="color: #ffdd00; font-weight: 600;">{{ $invite->battle->challenger->username }}</span>
            <span class="text-muted"> invited you as {{ $invite->role }}</span>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('battles.invites.decline', $invite) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">Decline</button>
            </form>
            <a href="{{ route('battles.room', $invite->battle) }}" class="btn btn-neon btn-neon-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">View</a>
        </div>
    </div>
    @endforeach
</div>
@endif



<!-- Recent Battles -->
<div class="mt-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="section-header mb-0">
            <i class="bi bi-crosshair section-icon" style="color: #ff00ff;"></i> Recent Battles
        </h5>
        <a href="{{ route('battles.index') }}" class="btn btn-neon btn-neon-sm">View All</a>
    </div>

    @if($recentBattles->count() > 0)
        @foreach($recentBattles as $battle)
            <a href="{{ route('battles.room', $battle) }}" class="neon-card p-3 mb-2 text-decoration-none d-block" style="color: inherit; transition: all 0.2s ease;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <span class="status-badge status-{{ $battle->status }}">{{ $battle->status }}</span>
                        <span>
                            <strong style="color: #00f0ff;">{{ $battle->challenger->username }}</strong>
                            @if($battle->opponent)
                                <span class="text-muted">vs</span>
                                <strong style="color: #ff00ff;">{{ $battle->opponent->username }}</strong>
                            @else
                                <span class="text-muted">— waiting for opponent</span>
                            @endif
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($battle->winner_id)
                            <span style="color: #39ff14; font-size: 0.85rem;">🏆 {{ $battle->winner->username }}</span>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    @else
        <div class="empty-state">
            <div class="empty-icon">⚔️</div>
            <div class="empty-text">No battles yet</div>
            <a href="{{ route('battles.create') }}" class="btn btn-neon-magenta btn-neon-sm">Create a Battle</a>
        </div>
    @endif
</div>

@push('modals')
<div class="modal fade" id="qrScannerModal" tabindex="-1" aria-labelledby="qrScannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #00f0ff; backdrop-filter: blur(20px);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title neon-text" id="qrScannerModalLabel">SCAN QR CODE</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="reader" style="width: 100%; overflow: hidden; border-radius: 8px; border: 1px solid rgba(0,240,255,0.2);"></div>
                <div id="scanner-status" class="text-center mt-3 text-muted" style="font-size: 0.85rem;">Initializing camera...</div>
            </div>
        </div>
    </div>
</div>
@endpush

@section('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scannerBtn = document.getElementById('btn-scan-qr');
        if (!scannerBtn) return;

        let html5QrCode = null;
        const qrModalEl = document.getElementById('qrScannerModal');
        const qrModal = new bootstrap.Modal(qrModalEl);

        scannerBtn.addEventListener('click', function() {
            qrModal.show();
        });

        qrModalEl.addEventListener('shown.bs.modal', function () {
            html5QrCode = new Html5Qrcode("reader");
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            html5QrCode.start(
                { facingMode: "environment" }, 
                config,
                (decodedText, decodedResult) => {
                    document.getElementById('scanner-status').innerText = "Redirecting...";
                    document.getElementById('scanner-status').style.color = "#39ff14";
                    
                    html5QrCode.stop().then(() => {
                        window.location.href = decodedText;
                    }).catch(err => {
                        window.location.href = decodedText;
                    });
                },
                (errorMessage) => {
                    // silent
                }
            ).catch(err => {
                document.getElementById('scanner-status').innerText = "Camera error: " + err;
                document.getElementById('scanner-status').style.color = "#ff4444";
            });
        });

        qrModalEl.addEventListener('hidden.bs.modal', function () {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().catch(err => console.error(err));
            }
        });
    });
</script>
@endsection
@endsection
