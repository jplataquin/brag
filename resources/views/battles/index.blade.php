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

@php
    $currentRoomInfo = Auth::user()->currentBattleRoom();
@endphp

@if($currentRoomInfo)
<div class="neon-card p-4 mb-4" style="border-color: #00f0ff; background: rgba(0,240,255,0.05); box-shadow: 0 0 20px rgba(0,240,255,0.1);">
    <h5 class="section-header" style="color: #00f0ff; border-color: rgba(0,240,255,0.2);">
        <i class="bi bi-broadcast section-icon" style="color: #00f0ff;"></i> CURRENT ACTIVE ROOM
    </h5>
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            @if($currentRoomInfo['type'] === '1v1')
                <div style="font-family: 'Orbitron', sans-serif; font-size: 1.1rem; color: #fff;">
                    1 VS 1 BATTLE #{{ $currentRoomInfo['battle']->id }}
                </div>
                <div class="text-muted small mt-1">Status: <span class="badge" style="background: rgba(0,240,255,0.2); color: #00f0ff; border: 1px solid #00f0ff;">{{ strtoupper($currentRoomInfo['battle']->status) }}</span></div>
            @else
                <div style="font-family: 'Orbitron', sans-serif; font-size: 1.1rem; color: #fff;">
                    TEAM BATTLE #{{ $currentRoomInfo['battle']->id }}
                </div>
                <div style="color: #00f0ff; font-size: 0.9rem;" class="mt-1">
                    {{ $currentRoomInfo['battle']->team_name_a }} <span style="color:#ff00ff">VS</span> {{ $currentRoomInfo['battle']->team_name_b }}
                </div>
                <div class="text-muted small mt-1">Status: <span class="badge" style="background: rgba(0,240,255,0.2); color: #00f0ff; border: 1px solid #00f0ff;">{{ strtoupper($currentRoomInfo['battle']->status) }}</span></div>
            @endif
        </div>
        <a href="{{ $currentRoomInfo['type'] === '1v1' ? route('battles.room', $currentRoomInfo['battle']->id) : route('team-battles.room', $currentRoomInfo['battle']->id) }}" class="btn btn-neon px-4 py-2" style="box-shadow: 0 0 10px rgba(0,240,255,0.4);">
            REJOIN MATCH <i class="bi bi-arrow-right ms-2"></i>
        </a>
    </div>
</div>
@endif

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

<!-- My Battles -->
<h5 class="section-header">
    <i class="bi bi-list-ul section-icon"></i> MY BATTLES
</h5>

@if($myBattles->count() > 0)
    @foreach($myBattles as $battle)
    <a href="{{ route('battles.room', $battle) }}" class="neon-card p-3 mb-2 text-decoration-none d-block" style="color: inherit; transition: all 0.2s ease;">
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
                @if($battle->marshall)
                    <span style="color: #8888aa; font-size: 0.75rem;">
                        ⚖️ {{ $battle->marshall->username }}
                    </span>
                @endif
            </div>
        </div>
    </a>
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
