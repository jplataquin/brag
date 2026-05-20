@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="position-relative rounded-4 overflow-hidden mb-5 border border-info border-opacity-25" style="height: 350px;">
        @if($gameTitle->header_image)
            <img src="{{ asset('storage/' . $gameTitle->header_image) }}" class="w-100 h-100 object-fit-cover" alt="{{ $gameTitle->title }}">
        @else
            <div class="w-100 h-100 bg-dark d-flex align-items-center justify-content-center text-secondary">
                <i class="bi bi-controller" style="font-size: 6rem; opacity: 0.2;"></i>
            </div>
        @endif
        <div class="position-absolute bottom-0 start-0 w-100 p-4 p-md-5 bg-gradient-to-t from-dark d-flex align-items-end">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('game_titles.index') }}" class="text-info text-decoration-none">Games</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">{{ $gameTitle->title }}</li>
                    </ol>
                </nav>
                <h1 class="display-3 fw-bold text-uppercase mb-0 text-white" style="text-shadow: 0 0 20px rgba(0, 240, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                    {{ $gameTitle->title }}
                </h1>
            </div>
        </div>
    </div>

    <div class="row g-5">
        <!-- Sidebar / Stats -->
        <div class="col-lg-4 order-lg-2">
            <div class="card bg-dark bg-opacity-75 border-info border-opacity-25 rounded-4 p-4" style="backdrop-filter: blur(10px);">
                <h4 class="text-info fw-bold text-uppercase mb-4" style="font-family: 'Orbitron', sans-serif;">Game Info</h4>
                
                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Templates Available</label>
                    <div class="h4 text-white fw-bold mb-0">
                        @php $totalCount = $gameTitle->templates->count() + $gameTitle->premiumTemplates->count(); @endphp
                        <i class="bi bi-grid-3x3-gap text-info me-2"></i> {{ $totalCount }}
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Status</label>
                    <div class="text-white">
                        <span class="badge bg-success rounded-pill px-3">
                            <i class="bi bi-check-circle-fill me-1"></i> Active & Ready
                        </span>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <a href="{{ route('templates.create', ['game_title_id' => $gameTitle->id]) }}" class="btn btn-neon-cyan fw-bold">
                        <i class="bi bi-plus-lg me-1"></i> Forge New Template
                    </a>
                    <a href="{{ route('game_titles.leaderboard', $gameTitle->id) }}" class="btn btn-outline-warning fw-bold mt-2">
                        <i class="bi bi-trophy-fill me-1"></i> View Leaderboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8 order-lg-1">
            <div class="mb-5">
                <h2 class="h4 text-uppercase fw-bold mb-4" style="color: var(--neon-cyan); font-family: 'Orbitron', sans-serif; letter-spacing: 1px;">About the Game</h2>
                <div class="text-light opacity-75 lead" style="white-space: pre-line; line-height: 1.8;">
                    {{ $gameTitle->description ?: 'No detailed description provided for this game title yet.' }}
                </div>
            </div>

            <div>
                <h2 class="h4 text-uppercase fw-bold mb-4" style="color: var(--neon-magenta); font-family: 'Orbitron', sans-serif; letter-spacing: 1px;">
                    <i class="bi bi-trophy-fill text-warning me-2"></i> Hall of Fame: Top Cards
                </h2>
                
                @if($topCards->count() > 0)
                    <div class="row g-4">
                        @foreach($topCards as $card)
                            <div class="col-6 col-md-3">
                                <x-digital-card 
                                    :mode="'thumbnail'"
                                    :title="$card->template->card_title"
                                    :game="$gameTitle->title"
                                    :creator="$card->originalOwner->username ?? 'Unknown'"
                                    :isCreatorVerified="$card->originalOwner->is_verified ?? false"
                                    :isCreatorUntrustworthy="$card->originalOwner->is_untrustworthy ?? false"
                                    :image="$card->template->display_photo"
                                    :imagePositionX="$card->template->image_position_x ?? 50"
                                    :imagePositionY="$card->template->image_position_y ?? 50"
                                    :imageScale="$card->template->image_scale ?? 1.0"
                                    :imageStretchY="$card->template->image_stretch_y ?? 1.0"
                                    :wins="$card->wins"
                                    :losses="$card->losses"
                                    :integrityStat="$card->integrity_stat"
                                    :rankLevel="$card->level"
                                    :serialNumber="$card->id"
                                    :backgroundColor="$card->template->background_color"
                                    :borderColor="$card->template->border_color"
                                    :sectionColor="$card->template->section_color"
                                    :primaryTextColor="$card->template->primary_text_color"
                                    :secondaryTextColor="$card->template->secondary_text_color"
                                    :detailUrl="route('cards.show', $card->id)"
                                />
                                <div class="text-center mt-2">
                                    <div class="small text-muted">Owned by</div>
                                    <div class="fw-bold text-info"><x-username :user="$card->owner" /></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert bg-dark border-info border-opacity-25 text-secondary rounded-4">
                        <i class="bi bi-info-circle me-2"></i> No digital cards have been forged for this game yet.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-to-t {
    background: linear-gradient(to top, rgba(10, 10, 26, 0.95), transparent);
}

.shadow-hover:hover {
    border-color: var(--neon-magenta) !important;
    transform: translateY(-3px);
    transition: all 0.3s ease;
}

.breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255, 255, 255, 0.3);
}
</style>
@endsection
