@extends('layouts.app')

@section('title', $template->card_title)

@section('content')
<div class="mb-3">
    <a href="{{ route('templates.index') }}" style="color: #8888aa; font-size: 0.85rem; text-decoration: none;">
        <i class="bi bi-arrow-left"></i> Back to Templates
    </a>
</div>

<div class="row g-4">
    <!-- Template Rendered Card -->
    <div class="col-md-5">
        <div class="neon-card p-4 d-flex justify-content-center" style="background: rgba(0,0,0,0.5);">
            <x-digital-card 
                id="template_preview_card_{{ $template->id }}" 
                mode="template"
                :title="$template->card_title" 
                :game="$template->gameTitle->title ?? 'GAME'" 
                :creator="$template->user->username ?? 'Creator'"
                :isCreatorVerified="$template->user->is_verified"
                :isCreatorUntrustworthy="$template->user->is_untrustworthy"
                :quote="$template->quote"
                :backgroundColor="$template->background_color"
                :borderColor="$template->border_color"
                :sectionColor="$template->section_color"
                :primaryTextColor="$template->primary_text_color"
                :secondaryTextColor="$template->secondary_text_color"
                :image="$template->display_photo"
                :imagePositionY="$template->image_position_y ?? 50"
                :integrityStat="0"
                :year="$template->created_at->format('Y')"
            />
        </div>

        @if(Auth::check() && Auth::id() === $template->user_id)
        <div class="mt-3 d-flex gap-2 flex-wrap">
            <a href="{{ route('templates.edit', $template) }}" class="btn btn-neon btn-neon-sm">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <form action="{{ route('templates.destroy', $template) }}" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-neon-danger btn-neon-sm" data-confirm="Delete this template and all its cards?">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </form>
        </div>
        @endif
    </div>

    <!-- Template Info -->
    <div class="col-md-7">
        <h1 class="page-title mb-1">{{ $template->card_title }}</h1>
        <div style="font-size: 0.9rem; color: #00f0ff; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 1rem;">
            🎮 {{ $template->gameTitle->title }}
        </div>

        <div class="neon-card p-3 mb-3">
            <p style="color: #bbbbd0; font-size: 0.95rem; margin-bottom: 0;">"{{ $template->quote }}" — {{ $template->user->username }} ({{ $template->created_at->format('Y') }})</p>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="stat-box">
                    <div class="stat-value">{{ $template->cards_in_circulation }}</div>
                    <div class="stat-label">In Circulation</div>
                </div>
            </div>
        </div>

        <!-- Forge Section -->
        @if(Auth::check() && Auth::id() === $template->user_id)
        <div class="neon-card p-4" style="border-color: rgba(57,255,20,0.2);">
            <h5 class="section-header" style="border-color: rgba(57,255,20,0.1);">
                <i class="bi bi-fire section-icon" style="color: #ff6600;"></i> FORGE DIGITAL CARD
            </h5>

            @if($canForge)
                <p style="font-size: 0.85rem; color: #8888aa;">Forge a new Digital Card from this template. <strong class="text-neon-cyan">Cost: {{ config('diamonds.costs.forging') }} Diamonds</strong></p>
                <form action="{{ route('templates.forge', $template) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-neon-lime" id="btn-forge-card" data-confirm="Forge a new Digital Card for {{ config('diamonds.costs.forging') }} Diamonds? You can only keep up to 3 cards from the same template in your inventory.">
                        <i class="bi bi-lightning-charge-fill"></i> FORGE NOW
                    </button>
                </form>
            @else
                @if($forgeStatus && $forgeStatus['cooldown_ends'])
                    <div class="cooldown-timer">
                        <i class="bi bi-hourglass-split"></i>
                        <span>{{ $forgeStatus['reason'] }}</span>
                    </div>
                @else
                    <div class="cooldown-timer" style="color: #ff4444; border-color: rgba(255,68,68,0.2); background: rgba(255,68,68,0.05);">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>{{ $forgeStatus['reason'] ?? 'Cannot forge at this time.' }}</span>
                    </div>
                @endif
            @endif
        </div>
        @endif

        <!-- Cards forged from this template -->
        @if($template->digitalCards->count() > 0)
        <div class="mt-4">
            <h5 class="section-header">
                <i class="bi bi-suit-diamond-fill section-icon"></i> FORGED CARDS ({{ $template->digitalCards->count() }})
            </h5>
            <div class="card-grid">
                @foreach($template->digitalCards as $card)
                    @include('partials.card-mini', ['card' => $card])
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
