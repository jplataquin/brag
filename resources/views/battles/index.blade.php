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
    $battle = Auth::user()->currentBattleRoom();
@endphp

@if($battle)
<div class="neon-card p-4 mb-4" style="border-color: #00f0ff; background: rgba(0,240,255,0.05); box-shadow: 0 0 20px rgba(0,240,255,0.1);">
    <h5 class="section-header" style="color: #00f0ff; border-color: rgba(0,240,255,0.2);">
        <i class="bi bi-broadcast section-icon" style="color: #00f0ff;"></i> CURRENT ACTIVE ROOM
    </h5>
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div style="font-family: 'Orbitron', sans-serif; font-size: 1.1rem; color: #fff;">
                BATTLE #{{ $battle->id }}
            </div>
            <div style="color: #00f0ff; font-size: 0.9rem;" class="mt-1">
                {{ $battle->team_name_a }} <span style="color:#ff00ff">VS</span> {{ $battle->team_name_b }}
            </div>
            <div class="text-muted small mt-1">Status: <span class="badge" style="background: rgba(0,240,255,0.2); color: #00f0ff; border: 1px solid #00f0ff;">{{ strtoupper($battle->status) }}</span></div>
        </div>
        <a href="{{ route('battles.room', $battle) }}" class="btn btn-neon px-4 py-2" style="box-shadow: 0 0 10px rgba(0,240,255,0.4);">
            REJOIN MATCH <i class="bi bi-arrow-right ms-2"></i>
        </a>
    </div>
</div>
@endif

<!-- My Battles -->
<h5 class="section-header">
    <i class="bi bi-list-ul section-icon"></i> MY BATTLES
</h5>

@if($myBattles->count() > 0)
    @foreach($myBattles as $b)
    <a href="{{ route('battles.room', $b) }}" class="neon-card p-3 mb-2 text-decoration-none d-block" style="color: inherit; transition: all 0.2s ease;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <span class="status-badge status-{{ $b->status }}">{{ $b->status }}</span>
                <div>
                    <strong style="color: #00f0ff;">{{ $b->team_name_a }}</strong>
                    <span style="color: #ff00ff; font-family: 'Orbitron', sans-serif; font-size: 0.8rem;"> VS </span>
                    <strong style="color: #ff00ff;">{{ $b->team_name_b }}</strong>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($b->status === 'completed' && $b->winner_team)
                    <span style="color: #39ff14; font-size: 0.8rem; font-weight: 600;">
                        🏆 {{ $b->winner_team === 'team_a' ? $b->team_name_a : $b->team_name_b }} WON
                    </span>
                @endif
                @if($b->marshall)
                    <span style="color: #8888aa; font-size: 0.75rem;">
                        ⚖️ {{ $b->marshall->username }}
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
