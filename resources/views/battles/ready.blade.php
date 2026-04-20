@extends('layouts.app')

@section('title', 'Ready for Battle #' . $battle->id)

@section('content')
<div class="mb-3">
    <a href="{{ route('battles.index') }}" style="color: #8888aa; font-size: 0.85rem; text-decoration: none;">
        <i class="bi bi-arrow-left"></i> Back to Arena
    </a>
</div>

<div class="row g-4">
    <!-- 1.) Battle Details Header Card -->
    <div class="col-lg-12">
        <div class="neon-card p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h1 class="page-title mb-1">
                        <span class="page-title-accent"><i class="bi bi-person-fill-add"></i></span> JOIN BATTLE #{{ $battle->id }}
                    </h1>
                    <div class="mb-2">
                        @if($challengerCard && $challengerCard->template && $challengerCard->template->gameTitle)
                            <span style="color: #00f0ff; font-family: 'Orbitron', sans-serif; font-size: 1.1rem; letter-spacing: 2px; text-transform: uppercase; font-weight: 700;">
                                {{ $challengerCard->template->gameTitle->title }}
                            </span>
                        @endif
                    </div>
                    <div class="d-flex gap-3 align-items-center flex-wrap">
                        <span style="color: #ff00ff; font-size: 0.95rem;">
                            <i class="bi bi-person-circle"></i> CHALLENGER: <strong>{{ $battle->challenger->username }}</strong>
                        </span>
                        <span style="color: #8888aa; font-size: 0.9rem;">
                            <i class="bi bi-bar-chart-fill"></i> CARD LEVEL: <strong>{{ strtoupper($challengerCard->level_name) }} (LEVEL {{ $challengerCard->level }})</strong>
                        </span>
                    </div>
                </div>
                
                <div class="battle-terms-box p-3" style="background: rgba(0, 240, 255, 0.05); border: 1px solid rgba(0, 240, 255, 0.1); border-radius: 12px; min-width: 250px;">
                    <div style="font-family: 'Orbitron', sans-serif; font-size: 0.7rem; color: #00f0ff; margin-bottom: 0.5rem; letter-spacing: 1px;">BATTLE TERMS</div>
                    <div style="font-size: 0.9rem; color: #8888aa; font-style: italic;">
                        {{ $battle->terms ?: 'No specific terms defined for this match.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2.) Challenger Card Showcase -->
    <div class="col-lg-5">
        <h5 class="section-header">
            <i class="bi bi-eye-fill section-icon"></i> CHALLENGER'S BET
        </h5>
        <div class="text-center">
            <x-digital-card 
                id="challenger_card_display_{{ $challengerCard->id }}" 
                mode="display"
                fullscreen="true"
                :rarity="$challengerCard->rarity_slug"
                :title="$challengerCard->template->card_title" 
                :game="$challengerCard->template->gameTitle->title ?? 'GAME'" 
                :creator="$challengerCard->originalOwner->username ?? 'Creator'"
                :quote="$challengerCard->template->quote"
                :backgroundColor="$challengerCard->template->background_color"
                :borderColor="$challengerCard->template->border_color"
                :sectionColor="$challengerCard->template->section_color"
                :primaryTextColor="$challengerCard->template->primary_text_color"
                :secondaryTextColor="$challengerCard->template->secondary_text_color"
                :image="$challengerCard->template->display_photo"
                :statsText="strtoupper($challengerCard->level_name) . ' • W: ' . $challengerCard->wins . ' • L: ' . $challengerCard->losses . ' • ' . strtoupper($challengerCard->status)"
                :rankLevel="$challengerCard->level"
                :serialNumber="$challengerCard->serial_number"
                :year="$challengerCard->forged_at->format('Y')"
            />        </div>
    </div>

    <!-- 3.) Eligible Cards Selection -->
    <div class="col-lg-7">
        <h5 class="section-header">
            <i class="bi bi-suit-diamond-fill section-icon" style="color: #39ff14;"></i> SELECT YOUR CARD TO BET
        </h5>

        @if($eligibleCards->isEmpty())
            <div class="empty-state neon-card p-5">
                <div class="empty-icon">🚫</div>
                <div class="empty-text">No eligible cards found.</div>
                <p style="color: #8888aa; font-size: 0.85rem;">
                    You need a <strong>Level {{ $challengerCard->level }}</strong> card from 
                    <strong>{{ $challengerCard->template->gameTitle->title }}</strong> to join.
                </p>
                <a href="{{ route('templates.index') }}" class="btn btn-neon btn-neon-sm">Forge Cards</a>
            </div>
        @else
            <!-- Desktop Grid -->
            <div class="d-none d-md-grid card-grid">
                @foreach($eligibleCards as $card)
                    <div class="d-flex flex-column align-items-center">
                        <x-digital-card 
                            :id="'join_card_grid_' . $card->id"
                            mode="thumbnail"
                            :rarity="$card->rarity_slug"
                            :detailUrl="route('cards.show', $card)"
                            :title="$card->template->card_title" 
                            :game="$card->template->gameTitle->title ?? 'GAME'" 
                            :creator="$card->originalOwner->username ?? 'Creator'"
                            :quote="$card->template->quote"
                            :backgroundColor="$card->template->background_color"
                            :borderColor="$card->template->border_color"
                            :sectionColor="$card->template->section_color"
                            :primaryTextColor="$card->template->primary_text_color"
                            :secondaryTextColor="$card->template->secondary_text_color"
                            :image="$card->template->display_photo"
                            :statsText="strtoupper($card->level_name) . ' • W: ' . $card->wins . ' • L: ' . $card->losses . ' • ' . strtoupper($card->status)"
                            :rankLevel="$card->level"
                            :serialNumber="$card->serial_number"
                            :year="$card->forged_at->format('Y')"
                            />                        <button type="button" class="btn btn-neon-lime btn-neon-sm w-100 mt-2 select-bet-card" data-card-id="{{ $card->id }}" data-card-name="{{ $card->template->card_title }}">
                            <i class="bi bi-check-lg"></i> SELECT CARD
                        </button>
                    </div>
                @endforeach
            </div>

            <!-- Mobile Carousel -->
            <div class="d-md-none">
                <div id="eligibleCardsCarousel" class="carousel slide" data-bs-interval="false">
                    <div class="carousel-inner">
                        @foreach($eligibleCards as $index => $card)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <div class="px-4 d-flex flex-column align-items-center">
                                    <x-digital-card 
                                        :id="'join_card_carousel_' . $card->id"
                                        mode="thumbnail"
                                        :rarity="$card->rarity_slug"
                                        :detailUrl="route('cards.show', $card)"
                                        :title="$card->template->card_title" 
                                        :game="$card->template->gameTitle->title ?? 'GAME'" 
                                        :creator="$card->originalOwner->username ?? 'Creator'"
                                        :quote="$card->template->quote"
                                        :backgroundColor="$card->template->background_color"
                                        :borderColor="$card->template->border_color"
                                        :sectionColor="$card->template->section_color"
                                        :primaryTextColor="$card->template->primary_text_color"
                                        :secondaryTextColor="$card->template->secondary_text_color"
                                        :image="$card->template->display_photo"
                                        :statsText="strtoupper($card->level_name) . ' • W: ' . $card->wins . ' • L: ' . $card->losses . ' • ' . strtoupper($card->status)"
                                        :rankLevel="$card->level"
                                        :serialNumber="$card->serial_number"
                                        :year="$card->forged_at->format('Y')"
                                        />                                    <button type="button" class="btn btn-neon-lime w-100 mt-3 select-bet-card" data-card-id="{{ $card->id }}" data-card-name="{{ $card->template->card_title }}">
                                        <i class="bi bi-check-lg"></i> SELECT THIS CARD
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#eligibleCardsCarousel" data-bs-slide="prev" style="width: 10%;">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#eligibleCardsCarousel" data-bs-slide="next" style="width: 10%;">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Hidden Form for submission -->
<form id="join-battle-form" action="{{ route('battles.confirmJoin', $battle) }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="card_id" id="selected_card_id">
</form>
@endsection

@push('modals')
<!-- Join Confirmation Modal -->
<div class="modal fade" id="joinConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #ff00ff; backdrop-filter: blur(20px);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title neon-text-magenta">CONFIRM BATTLE JOIN</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4 text-center">
                <p>Are you sure you want to join this battle and bet your card?</p>
                <div class="p-3 mb-3 rounded" style="background: rgba(255, 0, 255, 0.05); border: 1px solid rgba(255, 0, 255, 0.2);">
                    <div style="font-family: 'Orbitron', sans-serif; color: #ff00ff;" id="confirm-card-name">CARD NAME</div>
                    <div class="text-muted small">If you lose, this card will be transferred to the winner.</div>
                </div>
                <div class="d-flex gap-3">
                    <button type="button" class="btn btn-neon flex-fill" data-bs-dismiss="modal">CANCEL</button>
                    <button type="button" class="btn btn-neon-magenta flex-fill" id="btn-final-join">JOIN & BET</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const joinForm = document.getElementById('join-battle-form');
        const cardIdInput = document.getElementById('selected_card_id');
        const confirmModal = new bootstrap.Modal(document.getElementById('joinConfirmModal'));
        const cardNameDisplay = document.getElementById('confirm-card-name');
        const finalJoinBtn = document.getElementById('btn-final-join');

        document.querySelectorAll('.select-bet-card').forEach(btn => {
            btn.addEventListener('click', function() {
                const cardId = this.dataset.cardId;
                const cardName = this.dataset.cardName;

                cardIdInput.value = cardId;
                cardNameDisplay.innerText = cardName;
                confirmModal.show();
            });
        });

        finalJoinBtn.addEventListener('click', function() {
            this.innerHTML = '<i class="bi bi-hourglass-split"></i> JOINING...';
            this.disabled = true;
            joinForm.submit();
        });
    });
</script>
@endsection
