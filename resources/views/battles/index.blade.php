@extends('layouts.app')

@section('title', 'Battles')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <h1 class="page-title mb-0">
        <span class="page-title-accent"><i class="bi bi-crosshair"></i></span> ARENA
    </h1>
    <a href="{{ route('battles.create') }}" class="btn btn-neon-magenta" id="btn-create-battle">
        <i class="bi bi-plus-lg"></i> NEW BATTLE
    </a>
</div>

<!-- Pending Invites -->
@if($pendingInvites->count() > 0)
<div class="neon-card p-3 mb-4" style="border-color: rgba(255,221,0,0.3);">
    <h5 class="section-header" style="color: #ffdd00; border-color: rgba(255,221,0,0.15);">
        <i class="bi bi-envelope-fill section-icon" style="color: #ffdd00;"></i> INCOMING CHALLENGES
    </h5>
    @foreach($pendingInvites as $invite)
    <div class="d-flex align-items-center justify-content-between mb-2 p-2" style="background: rgba(255,221,0,0.03); border-radius: 8px;">
        <div>
            <strong style="color: #ffdd00;">{{ $invite->battle->challenger->username }}</strong>
            <span class="text-muted"> wants you as {{ $invite->role }}</span>
        </div>
        <a href="{{ route('battles.room', $invite->battle) }}" class="btn btn-neon btn-neon-sm">View</a>
    </div>
    @endforeach
</div>
@endif

<!-- My Battles -->
<h5 class="section-header">
    <i class="bi bi-list-ul section-icon"></i> MY BATTLES
</h5>

@if($myBattles->count() > 0)
    @foreach($myBattles as $battle)
    <div class="neon-card p-3 mb-2">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <span class="status-badge status-{{ $battle->status }}">{{ $battle->status }}</span>
                <div>
                    <strong style="color: #00f0ff;">{{ $battle->challenger->username }}</strong>
                    @if($battle->opponent)
                        <span style="color: #ff00ff; font-family: 'Orbitron', sans-serif; font-size: 0.8rem;"> VS </span>
                        <strong style="color: #ff00ff;">{{ $battle->opponent->username }}</strong>
                    @else
                        <span class="text-muted"> — awaiting opponent</span>
                    @endif
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($battle->winner_id)
                    <span style="color: #39ff14; font-size: 0.8rem; font-weight: 600;">
                        🏆 {{ $battle->winner->username }}
                    </span>
                @endif
                @if($battle->adjudicator)
                    <span style="color: #8888aa; font-size: 0.75rem;">
                        ⚖️ {{ $battle->adjudicator->username }}
                    </span>
                @endif
                <a href="{{ route('battles.room', $battle) }}" class="btn btn-neon btn-neon-sm">Details</a>
            </div>
        </div>
    </div>
    @endforeach

    <div class="mt-3">
        {{ $myBattles->links() }}
    </div>
@else
    <div class="empty-state">
        <div class="empty-icon">⚔️</div>
        <div class="empty-text">No battles yet</div>
        <a href="{{ route('battles.create') }}" class="btn btn-neon-magenta btn-neon-sm">Create Your First Battle</a>
    </div>
@endif
@endsection
