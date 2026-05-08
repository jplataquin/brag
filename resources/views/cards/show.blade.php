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
        <div class="neon-card p-4 text-center" style="background: rgba(0,0,0,0.5); position: relative;">
            @if($digitalCard->is_censored)
                <div class="censored-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(10, 10, 26, 0.85); z-index: 10; display: flex; flex-direction: column; align-items: center; justify-content: center; border-radius: 12px; border: 1px solid #ff4444; padding: 20px; backdrop-filter: blur(5px);">
                    <i class="bi bi-eye-slash-fill text-danger mb-2" style="font-size: 2.5rem;"></i>
                    <div class="text-white small text-center orbitron">CONTENT HIDDEN</div>
                    <div class="text-danger x-small text-center mt-1" style="font-size: 0.65rem;">PENDING MODERATION</div>
                </div>
            @endif

            <div style="{{ $digitalCard->is_censored ? 'filter: blur(20px); pointer-events: none;' : '' }}">
                <x-digital-card 
                    id="digital_card_{{ $digitalCard->id }}" 
                    mode="display"
                    :rarity="$digitalCard->rarity_slug"
                    :title="$digitalCard->is_censored ? '[CENSORED]' : $digitalCard->template->card_title" 
                    :game="$digitalCard->template->gameTitle->title ?? 'GAME'" 
                    :creator="$digitalCard->originalOwner->username ?? 'Creator'"
                    :quote="$digitalCard->is_censored ? '[Content hidden pending review]' : $digitalCard->template->quote"
                    :backgroundColor="$digitalCard->template->background_color"
                    :borderColor="$digitalCard->template->border_color"
                    :sectionColor="$digitalCard->template->section_color"
                    :primaryTextColor="$digitalCard->template->primary_text_color"
                    :secondaryTextColor="$digitalCard->template->secondary_text_color"
                    :image="$digitalCard->is_censored ? '' : $digitalCard->template->display_photo"
                    :imagePositionY="$digitalCard->template->image_position_y ?? 50"
                    :wins="$digitalCard->wins"
                    :losses="$digitalCard->losses"
                    :lifePoints="$digitalCard->life_points"
                    :integrityStat="$digitalCard->integrity_stat"
                    :status="$digitalCard->status"
                    :rankLevel="$digitalCard->level"
                    :serialNumber="$digitalCard->serial_number"
                    :year="$digitalCard->forged_at->format('Y')"
                    :burned="$digitalCard->trashed()"
                    :cardId="$digitalCard->id"
                    :ownerId="$digitalCard->owner_id"
                    :isCensored="$digitalCard->is_censored"
                />
            </div>

            @if(Auth::check() && $digitalCard->owner_id != Auth::id())
                <div class="mt-3">
                    <button type="button" class="btn btn-link text-muted btn-sm text-decoration-none" onclick="window.openReportModal({{ $digitalCard->id }})">
                        <i class="bi bi-flag-fill me-1"></i> Report this card
                    </button>
                </div>
            @endif


            
            <!-- Actions -->
            @if(Auth::check() && $digitalCard->owner_id === Auth::id() && !$digitalCard->trashed())
                <div class="mt-4">
                    <a href="{{ route('battles.create', ['game_id' => $digitalCard->template->game_title_id, 'card_id' => $digitalCard->id]) }}" class="btn btn-neon-lime w-100 py-3 mb-2" style="font-size: 1.1rem; letter-spacing: 1px; font-weight: bold; box-shadow: 0 0 15px rgba(57, 255, 20, 0.3);">
                        <i class="bi bi-crosshair"></i> BATTLE WITH THIS CARD
                    </a>
                </div>
                
                @if($digitalCard->life_points < 3)
                <div class="mt-2 mb-4">
                    <form id="heal-form-{{ $digitalCard->id }}" action="{{ route('cards.heal', $digitalCard->id) }}" method="POST">
                        @csrf
                        <button type="button" class="btn btn-outline-danger w-100 py-2 fw-bold" style="border-color: #ff4444; color: #ff4444; box-shadow: 0 0 10px rgba(255, 68, 68, 0.2);" onclick="window.neonConfirm('Healing this card will cost 5 Diamonds. Proceed?').then(confirmed => { if(confirmed) { document.getElementById('heal-form-{{ $digitalCard->id }}').submit(); } });">
                            <i class="bi bi-heart-fill"></i> Heal 💖 (-5 Diamonds)
                        </button>
                    </form>
                </div>
                @endif

                @php
                    $nextLevel = $digitalCard->level + 1;
                    $reqWins = 0;
                    $reqWinRate = 0;
                    $reqIntegrity = 0;
                    
                    if ($nextLevel == 2) { $reqWins = 5; $reqWinRate = 51; $reqIntegrity = 40; }
                    elseif ($nextLevel == 3) { $reqWins = 10; $reqWinRate = 60; $reqIntegrity = 50; }
                    elseif ($nextLevel == 4) { $reqWins = 15; $reqWinRate = 80; $reqIntegrity = 80; }
                    elseif ($nextLevel == 5) { $reqWins = 25; $reqWinRate = 95; $reqIntegrity = 90; }
                @endphp


                @if($nextLevel <= 5)
                    <div class="mt-3 p-3 rounded text-start" style="background: rgba(0, 240, 255, 0.05); border: 1px solid rgba(0, 240, 255, 0.2);">
                        <div style="font-family: 'Orbitron', sans-serif; font-size: 0.8rem; color: #00f0ff; margin-bottom: 0.5rem; letter-spacing: 1px;">
                            <i class="bi bi-arrow-up-circle-fill"></i> NEXT PROMOTION: LEVEL {{ $nextLevel }}
                        </div>
                        <p style="font-size: 0.85rem; color: #bbbbd0; margin-bottom: 0;">
                            Keep battling! To level up to <strong>Level {{ $nextLevel }}</strong>, this card needs to achieve at least <strong style="color: #39ff14;">{{ $reqWins }} wins</strong> while maintaining a win rate of <strong style="color: #00f0ff;">{{ $reqWinRate }}%</strong> or higher, and an integrity of <strong style="color: #ffdd00;">{{ $reqIntegrity }}%</strong> or higher.
                        </p>
                    </div>
                @else
                    <div class="mt-3 p-3 rounded text-start" style="background: rgba(255, 221, 0, 0.05); border: 1px solid rgba(255, 221, 0, 0.3);">
                        <div style="font-family: 'Orbitron', sans-serif; font-size: 0.8rem; color: #ffdd00; margin-bottom: 0.5rem; letter-spacing: 1px;">
                            <i class="bi bi-star-fill"></i> MAX LEVEL REACHED
                        </div>
                        <p style="font-size: 0.85rem; color: #bbbbd0; margin-bottom: 0;">
                            Incredible! Your card has attained the ultimate GOAT status. Continue dominating the arena to cement its legendary legacy!
                        </p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Card Details -->
    <div class="col-md-7">
        <h1 class="page-title mb-1">
            @if($digitalCard->is_censored)
                <span class="text-danger">[CENSORED]</span>
            @else
                {{ $digitalCard->template->card_title }}
            @endif
            <small style="font-size: 0.6em; color: #555577;">#{{ str_pad($digitalCard->serial_number, 4, '0', STR_PAD_LEFT) }}</small>
        </h1>

        <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
            <span class="rarity-badge rarity-{{ $digitalCard->rarity_slug }}">
                {{ strtoupper($digitalCard->rarity) }}
            </span>
            @if($digitalCard->is_trophy)
                <span class="rarity-badge" style="color: #ffdd00; border-color: rgba(255,221,0,0.3); background: rgba(255,221,0,0.05);">
                    🏆 TROPHY
                </span>
            @endif
        </div>

        <!-- Stats Grid -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-box h-100">
                    <div class="stat-value" style="color: #39ff14;">{{ $digitalCard->wins }}</div>
                    <div class="stat-label">Wins</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-box h-100">
                    <div class="stat-value" style="color: #ff4444;">{{ $digitalCard->losses }}</div>
                    <div class="stat-label">Losses</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-box h-100">
                    <div class="stat-value" style="color: #00f0ff;">{{ round($digitalCard->win_rate) }}%</div>
                    <div class="stat-label">Win Rate</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-box h-100">
                    <div class="stat-value" style="color: #ffdd00;">{{ round($digitalCard->integrity_stat) }}%</div>
                    <div class="stat-label">Integrity</div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="stat-box h-100">
                    <div class="stat-value" style="color: #ff00ff;">{{ $digitalCard->life_points > 0 ? str_repeat('❤️', $digitalCard->life_points) : '💀' }}</div>
                    <div class="stat-label">Life Points</div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="stat-box h-100">
                    <div class="stat-value" style="font-size: 1.2rem;">{{ $digitalCard->level }}</div>
                    <div class="stat-label">Level</div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-box h-100">
                    <div class="stat-value" style="color: #ff00ff;">{{ $digitalCard->template->cards_in_circulation > 99 ? '99+' : $digitalCard->template->cards_in_circulation }}</div>
                    <div class="stat-label">In Circulation</div>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="neon-card p-3 mb-3">
            <p style="color: #bbbbd0; font-size: 0.9rem; margin-bottom: 0;">
                @if($digitalCard->is_censored)
                    <span class="text-muted italic">[Content hidden pending review]</span>
                @else
                    "{{ $digitalCard->template->quote }}"
                @endif
                — {{ $digitalCard->template->user->username }} ({{ $digitalCard->template->created_at->format('Y') }})
            </p>
        </div>

        @if(Auth::check() && $digitalCard->owner_id === Auth::id() && !$digitalCard->trashed())
        <!-- Burn Card Action -->
        <div class="neon-card p-3 mb-3" style="border-color: rgba(255, 68, 68, 0.3); background: rgba(255, 68, 68, 0.05);">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <span style="font-size: 0.85rem; color: #ff4444; font-family: 'Orbitron', sans-serif; letter-spacing: 1px;"><i class="bi bi-fire"></i> BURN CARD</span>
                    <p style="color: #bbbbd0; font-size: 0.75rem; margin-bottom: 0; max-width: 300px;">
                        Permanently destroy this card to receive <strong>{{ $digitalCard->level }} Diamond(s)</strong>. This action cannot be undone.
                    </p>
                </div>
                <div>
                    <form id="burn-form-{{ $digitalCard->id }}" action="{{ route('cards.burn', $digitalCard->id) }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <button type="button" class="btn btn-outline-danger btn-sm px-4" style="border-color: #ff4444; color: #ff4444; font-family: 'Orbitron', sans-serif;" onclick="window.neonConfirm('Are you sure you want to BURN this card? It will be permanently removed from your inventory and circulation, but you will receive {{ $digitalCard->level }} Diamond(s).').then(confirmed => { if(confirmed) { document.getElementById('burn-form-{{ $digitalCard->id }}').submit(); } });">
                        BURN
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Ownership Info -->
        <div class="neon-card p-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <span style="font-size: 0.75rem; color: #555577; text-transform: uppercase; letter-spacing: 1px;">Current Owner</span>
                    <br>
                    @if($digitalCard->owner)
                        <a href="{{ route('profile.show', $digitalCard->owner->username) }}" class="neon-text" style="font-weight: 600; text-decoration: none;">
                            @<span>{{ $digitalCard->owner->username }}</span>
                        </a>
                    @else
                        <span class="text-danger" style="font-weight: 600; font-family: 'Orbitron', sans-serif;"><i class="bi bi-fire"></i> BURNED</span>
                    @endif
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

        <!-- Battle History -->
        <div class="mt-4">
            <h5 class="section-header">
                <i class="bi bi-clock-history section-icon" style="color: #00f0ff;"></i> BATTLE HISTORY
            </h5>
            <div id="battle-history-container" class="neon-card p-0" style="overflow: hidden;">
                <!-- Battles will be injected here via JS -->
            </div>
            <div class="text-center mt-3">
                <button id="load-more-battles" class="btn btn-neon btn-neon-sm d-none">SHOW MORE</button>
            </div>
            <div id="battle-history-empty" class="empty-state d-none">
                <div class="empty-icon">⚔️</div>
                <div class="empty-text">No battles recorded yet</div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('battle-history-container');
    const loadMoreBtn = document.getElementById('load-more-battles');
    const emptyState = document.getElementById('battle-history-empty');
    let offset = 0;
    const cardId = {{ $digitalCard->id }};

    function loadBattles() {
        fetch(`/cards/${cardId}/history?offset=${offset}`)
            .then(response => response.json())
            .then(data => {
                if (offset === 0 && data.battles.length === 0) {
                    emptyState.classList.remove('d-none');
                    container.classList.add('d-none');
                    return;
                }

                data.battles.forEach((battle, index) => {
                    const isLast = (index === data.battles.length - 1) && !data.has_more;
                    const borderStyle = isLast ? '' : 'border-bottom: 1px solid rgba(0, 240, 255, 0.1);';
                    
                    const battleEl = document.createElement('div');
                    battleEl.className = 'p-3 d-flex align-items-center justify-content-between';
                    battleEl.style = borderStyle + ' transition: background-color 0.2s;';
                    battleEl.innerHTML = `
                        <div>
                            <div style="font-size: 0.8rem; color: #8888aa; margin-bottom: 0.25rem;">${battle.date}</div>
                            <div style="font-size: 0.95rem; font-weight: 600; color: #fff;">
                                vs <span style="color: #ff00ff;">@${battle.opponent_name}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div style="font-family: 'Orbitron', sans-serif; font-weight: 700; color: ${battle.result_color}; font-size: 1.1rem; letter-spacing: 1px;">
                                ${battle.result}
                            </div>
                            <a href="/battles/${battle.room_id}/room" class="btn btn-outline-neon btn-sm mt-1" style="font-size: 0.7rem; padding: 0.1rem 0.4rem;">View Match</a>
                        </div>
                    `;
                    container.appendChild(battleEl);
                });

                if (data.has_more) {
                    loadMoreBtn.classList.remove('d-none');
                    offset += data.battles.length;
                } else {
                    loadMoreBtn.classList.add('d-none');
                }
            })
            .catch(error => {
                console.error("Error loading battle history:", error);
            });
    }

    loadMoreBtn.addEventListener('click', loadBattles);

    // Initial load
    loadBattles();
});
</script>
@endsection
