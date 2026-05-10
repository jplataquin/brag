@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Quick Actions -->
<div class="mb-4 d-md-none">
    <h5 class="section-header">
        <i class="bi bi-lightning-fill section-icon" style="color: #ffdd00;"></i> QUICK ACTIONS
    </h5>
    <div class="row g-3">
        @if($platformSettings->allow_battle_creation)
        <div class="col-6 col-md-3">
            <a href="{{ route('battles.create') }}" class="quick-action-card">
                <i class="bi bi-plus-lg" style="color: #ff00ff;"></i>
                <span>New Battle</span>
            </a>
        </div>
        @endif
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
        <a href="{{ route('cards.index') }}" class="stat-box d-block text-decoration-none">
            <div class="stat-value">{{ $stats['total_cards'] }}</div>
            <div class="stat-label">Cards</div>
        </a>
    </div>
    <div class="col-6 col-md">
        <a href="{{ route('cards.trophies') }}" class="stat-box d-block text-decoration-none">
            <div class="stat-value" style="color: #ffdd00;">{{ $stats['total_trophies'] }}</div>
            <div class="stat-label">Trophies</div>
        </a>
    </div>
    <div class="col-6 col-md">
        <a href="{{ route('templates.index') }}" class="stat-box d-block text-decoration-none">
            <div class="stat-value" style="color: #ff00ff;">{{ $stats['total_templates'] }}</div>
            <div class="stat-label">Templates</div>
        </a>
    </div>
    <div class="col-6 col-md">
        <a href="{{ route('battles.index', ['filter' => 'wins']) }}" class="stat-box d-block text-decoration-none">
            <div class="stat-value" style="color: #39ff14;">{{ $stats['total_wins'] }}</div>
            <div class="stat-label">Wins</div>
        </a>
    </div>
    <div class="col-6 col-md">
        <a href="{{ route('battles.index', ['filter' => 'losses']) }}" class="stat-box d-block text-decoration-none">
            <div class="stat-value" style="color: #ff6600;">{{ $stats['total_losses'] }}</div>
            <div class="stat-label">Losses</div>
        </a>
    </div>
</div>





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
            @php
                $myId = Auth::id();
                $isMarshall = $battle->marshall_id == $myId;
                $myTeam = '';
                $mySlot = 0;
                
                if (!$isMarshall) {
                    for ($i = 1; $i <= $battle->no_players_per_team; $i++) {
                        if ($battle->{"team_a_user_{$i}"} == $myId) { $myTeam = 'A'; $mySlot = $i; break; }
                        if ($battle->{"team_b_user_{$i}"} == $myId) { $myTeam = 'B'; $mySlot = $i; break; }
                    }
                }

                $opponentUsername = 'Unknown';
                if (!$isMarshall && $myTeam && $mySlot) {
                    $oppTeam = $myTeam == 'A' ? 'b' : 'a';
                    $oppId = $battle->{"team_{$oppTeam}_user_{$mySlot}"};
                    $oppUser = $oppId ? \App\Models\User::find($oppId) : null;
                    $opponentUsername = $oppUser ? $oppUser->username : 'waiting for opponent';
                }
            @endphp
            <a href="{{ route('battles.room', $battle) }}" class="neon-card p-3 mb-2 text-decoration-none d-block" style="color: inherit; transition: all 0.2s ease;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <span class="status-badge status-{{ $battle->status }}">{{ strtoupper($battle->status) }}</span>
                        <span>
                            @if($isMarshall)
                                <strong style="color: #00f0ff;">{{ $battle->team_name_a }}</strong>
                                <span class="text-muted">vs</span>
                                <strong style="color: #ff00ff;">{{ $battle->team_name_b }}</strong>
                                <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;">MARSHALLED</span>
                            @else
                                <strong style="color: #00f0ff;"><x-username :user="Auth::user()" /></strong>
                                <span class="text-muted">vs</span>
                                <strong style="color: #ff00ff;">
                                    @if($oppUser)
                                        <x-username :user="$oppUser" />
                                    @else
                                        {{ $opponentUsername }}
                                    @endif
                                </strong>
                            @endif
                            <span class="text-muted small ms-1">({{ $battle->no_players_per_team }} on {{ $battle->no_players_per_team }})</span>
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($battle->status === 'completed' && $battle->winner_team)
                            @if($battle->winner_team === 'T')
                                <span style="color: #ffdd00; font-size: 0.85rem;">🤝 TIE</span>
                            @elseif($isMarshall)
                                <span style="color: #39ff14; font-size: 0.85rem;">🏆 {{ $battle->winner_team == 'A' ? $battle->team_name_a : $battle->team_name_b }} WON</span>
                            @else
                                @if($battle->winner_team == $myTeam)
                                    <span style="color: #39ff14; font-size: 0.85rem;">🏆 YOU WON</span>
                                @else
                                    <span style="color: #ff4444; font-size: 0.85rem;">❌ YOU LOST</span>
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    @else
        <div class="empty-state">
            <div class="empty-icon">⚔️</div>
            <div class="empty-text">No battles yet</div>
            @if($platformSettings->allow_battle_creation)
            <a href="{{ route('battles.create') }}" class="btn btn-neon-magenta btn-neon-sm">Create a Battle</a>
            @endif
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
