@extends('layouts.app')

@section('title', 'Join Team Battle #' . $teamBattle->id)

@section('content')
<div class="mb-3">
    <a href="{{ route('battles.index') }}" style="color: #8888aa; font-size: 0.85rem; text-decoration: none;">
        <i class="bi bi-arrow-left"></i> Back to Arena
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-12">
        <div class="neon-card p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h1 class="page-title mb-1">
                        <span class="page-title-accent"><i class="bi bi-person-fill-add"></i></span> JOIN TEAM BATTLE #{{ $teamBattle->id }}
                    </h1>
                    <div class="mb-2">
                        @if($teamBattle->gameTitle)
                            <span style="color: #00f0ff; font-family: 'Orbitron', sans-serif; font-size: 1.1rem; letter-spacing: 2px; text-transform: uppercase; font-weight: 700;">
                                {{ $teamBattle->gameTitle->title }}
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="battle-terms-box p-3" style="background: rgba(0, 240, 255, 0.05); border: 1px solid rgba(0, 240, 255, 0.1); border-radius: 12px; min-width: 250px;">
                    <div style="font-family: 'Orbitron', sans-serif; font-size: 0.7rem; color: #00f0ff; margin-bottom: 0.5rem; letter-spacing: 1px;">BATTLE TERMS</div>
                    <div style="font-size: 0.9rem; color: #8888aa; font-style: italic;">
                        {{ $teamBattle->battle_terms ?: 'No specific terms defined for this match.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-info py-3 mb-4" style="background: rgba(0, 240, 255, 0.05); border: 1px solid #00f0ff; color: #00f0ff;">
    <i class="bi bi-info-circle-fill me-2"></i> <strong>How to Join:</strong> Click the <strong>"JOIN"</strong> button on an empty slot in either Team A or Team B below. You will be prompted to select an eligible card to stake.
</div>

<livewire:team-battle-room :teamBattle="$teamBattle" />
@endsection
