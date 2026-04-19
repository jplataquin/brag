@extends('layouts.app')

@section('title', $digitalCard->template->card_title . ' #' . $digitalCard->serial_number)

@section('content')
<div class="mb-3">
    <a href="{{ route('cards.index') }}" style="color: #8888aa; font-size: 0.85rem; text-decoration: none;">
        <i class="bi bi-arrow-left"></i> Back to Inventory
    </a>
</div>

<div class="row g-4">
    <!-- Card Visual -->
    <div class="col-md-5">
        <div class="neon-card p-4 d-flex justify-content-center" style="background: rgba(0,0,0,0.5);">
            <x-digital-card 
                id="digital_card_{{ $digitalCard->id }}" 
                mode="display"
                :rarity="$digitalCard->rarity ?? 'common'"
                :title="$digitalCard->template->card_title" 
                :game="$digitalCard->template->gameTitle->title ?? 'GAME'" 
                :creator="$digitalCard->originalOwner->username ?? 'Creator'"
                :quote="$digitalCard->template->quote"                :backgroundColor="$digitalCard->template->background_color"
                :borderColor="$digitalCard->template->border_color"
                :sectionColor="$digitalCard->template->section_color"
                :primaryTextColor="$digitalCard->template->primary_text_color"
                :secondaryTextColor="$digitalCard->template->secondary_text_color"
                :image="$digitalCard->template->display_photo"
                :statsText="'LVL ' . $digitalCard->level . ' • W: ' . $digitalCard->wins . ' • L: ' . $digitalCard->losses . ' • COPIES: ' . $digitalCard->template->cards_in_circulation"
                :rankLevel="$digitalCard->level"
            />
        </div>
    </div>

    <!-- Card Details -->
    <div class="col-md-7">
        <h1 class="page-title mb-1">
            {{ $digitalCard->template->card_title }}
            <small style="font-size: 0.6em; color: #555577;">#{{ str_pad($digitalCard->serial_number, 4, '0', STR_PAD_LEFT) }}</small>
        </h1>

        <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
            <span class="rarity-badge rarity-{{ $digitalCard->rarity }}">
                {{ $digitalCard->rarity_icon }} {{ strtoupper($digitalCard->rarity) }}
            </span>
            @if($digitalCard->is_trophy)
                <span class="rarity-badge" style="color: #ffdd00; border-color: rgba(255,221,0,0.3); background: rgba(255,221,0,0.05);">
                    🏆 TROPHY
                </span>
            @endif
        </div>

        <!-- Stats Grid -->
        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="stat-box">
                    <div class="stat-value" style="color: #39ff14;">{{ $digitalCard->wins }}</div>
                    <div class="stat-label">Wins</div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-box">
                    <div class="stat-value" style="color: #ff4444;">{{ $digitalCard->losses }}</div>
                    <div class="stat-label">Losses</div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-box">
                    <div class="stat-value" style="color: #ffdd00;">{{ $digitalCard->win_rate }}%</div>
                    <div class="stat-label">Win Rate</div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="stat-box">
                    <div class="stat-value">LV.{{ $digitalCard->level }}</div>
                    <div class="stat-label">Level</div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-box">
                    <div class="stat-value" style="color: #ff00ff;">{{ $digitalCard->template->cards_in_circulation }}</div>
                    <div class="stat-label">In Circulation</div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-box">
                    <div class="stat-value" style="color: #ff6600;">{{ $digitalCard->forged_at->format('M j') }}</div>
                    <div class="stat-label">Forged</div>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="neon-card p-3 mb-3">
            <p style="color: #bbbbd0; font-size: 0.9rem; margin-bottom: 0;">{{ $digitalCard->template->quote }}</p>
        </div>

        <!-- Ownership Info -->
        <div class="neon-card p-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <span style="font-size: 0.75rem; color: #555577; text-transform: uppercase; letter-spacing: 1px;">Current Owner</span>
                    <br>
                    <a href="{{ route('profile.show', $digitalCard->owner->username) }}" class="neon-text" style="font-weight: 600; text-decoration: none;">
                        @<span>{{ $digitalCard->owner->username }}</span>
                    </a>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: #555577; text-transform: uppercase; letter-spacing: 1px;">Original Creator</span>
                    <br>
                    <a href="{{ route('profile.show', $digitalCard->originalOwner->username) }}" class="neon-text-magenta" style="font-weight: 600; text-decoration: none;">
                        @<span>{{ $digitalCard->originalOwner->username }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
