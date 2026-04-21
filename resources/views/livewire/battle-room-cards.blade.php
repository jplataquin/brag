<div x-data="{
    redrawCards() {
        Object.keys(window).forEach(key => {
            if (key.startsWith('initCard_') && typeof window[key] === 'function') {
                try { window[key](); } catch(e) {}
            }
        });
    }
}" @battle-cards-updated.window="setTimeout(() => redrawCards(), 100)">

@php
    $isFinal = in_array($battle->status, ['completed', 'failed', 'cancelled']);
    
    $cCard = $battle->challengerCard;
    $cSnapshot = $battle->challenger_card_data;
    $cRarity = ($isFinal && isset($cSnapshot['rarity'])) ? $cSnapshot['rarity'] : ($cCard ? $cCard->rarity_slug : 'common');
    $cWins = ($isFinal && isset($cSnapshot['wins'])) ? $cSnapshot['wins'] : ($cCard ? $cCard->wins : 0);
    $cLosses = ($isFinal && isset($cSnapshot['losses'])) ? $cSnapshot['losses'] : ($cCard ? $cCard->losses : 0);
    $cLifePoints = ($isFinal && isset($cSnapshot['life_points'])) ? $cSnapshot['life_points'] : ($cCard ? $cCard->life_points : 3);
    $cDistinctStat = ($isFinal && isset($cSnapshot['distinct_stat'])) ? $cSnapshot['distinct_stat'] : ($cCard ? $cCard->distinct_stat : 0);
    $cStatus = ($isFinal && isset($cSnapshot['status'])) ? $cSnapshot['status'] : ($cCard ? $cCard->status : 'Maintained');
    $cRankLevel = ($isFinal && isset($cSnapshot['level'])) ? $cSnapshot['level'] : ($cCard ? $cCard->level : 1);
    
    $oCard = $battle->opponentCard;
    $oSnapshot = $battle->opponent_card_data;
    $oRarity = ($isFinal && isset($oSnapshot['rarity'])) ? $oSnapshot['rarity'] : ($oCard ? $oCard->rarity_slug : 'common');
    $oWins = ($isFinal && isset($oSnapshot['wins'])) ? $oSnapshot['wins'] : ($oCard ? $oCard->wins : 0);
    $oLosses = ($isFinal && isset($oSnapshot['losses'])) ? $oSnapshot['losses'] : ($oCard ? $oCard->losses : 0);
    $oLifePoints = ($isFinal && isset($oSnapshot['life_points'])) ? $oSnapshot['life_points'] : ($oCard ? $oCard->life_points : 3);
    $oDistinctStat = ($isFinal && isset($oSnapshot['distinct_stat'])) ? $oSnapshot['distinct_stat'] : ($oCard ? $oCard->distinct_stat : 0);
    $oStatus = ($isFinal && isset($oSnapshot['status'])) ? $oSnapshot['status'] : ($oCard ? $oCard->status : 'Maintained');
    $oRankLevel = ($isFinal && isset($oSnapshot['level'])) ? $oSnapshot['level'] : ($oCard ? $oCard->level : 1);
@endphp

    <h5 class="section-header">
        <i class="bi bi-suit-diamond-fill section-icon"></i> CARDS AT STAKE
    </h5>

    <!-- Desktop View (Side by Side) --> 
    <div class="d-none d-md-block">
        <div class="row g-4">
            <!-- Challenger -->
            <div class="col-md-6">
                <div class="text-center mb-3">
                    @if($battle->status === 'completed' && $battle->winner_id === $battle->challenger_id)
                        <div class="mb-1">
                            <span class="badge bg-warning text-dark" style="font-size: 0.7rem; letter-spacing: 1px; box-shadow: 0 0 10px rgba(255, 221, 0, 0.5);"><i class="bi bi-trophy-fill"></i> WINNER</span>
                        </div>
                    @elseif($battle->status === 'completed' && $battle->winner_id !== null && $battle->winner_id !== $battle->challenger_id)
                        <div class="mb-1">
                            <span class="badge bg-danger text-light" style="font-size: 0.7rem; letter-spacing: 1px; box-shadow: 0 0 10px rgba(255, 0, 0, 0.5);"><i class="bi bi-x-circle-fill"></i> LOSER</span>
                        </div>
                    @endif
                    <div style="font-family: 'Orbitron', sans-serif; font-size: 0.8rem; color: #00f0ff;">
                        @if(Auth::id() === $battle->challenger_id) YOU @else CHALLENGER @endif
                    </div>
                    <h4 style="color: #fff;">{{ $battle->challenger->username }}</h4>
                </div>
                @if($battle->challengerCard)
                    <div style="{{ $battle->status === 'completed' ? ($battle->winner_id === $battle->challenger_id ? 'box-shadow: 0 0 30px rgba(255, 221, 0, 0.6); border-radius: 16px; transform: scale(1.02); transition: all 0.3s ease;' : 'opacity: 0.5; filter: grayscale(80%); transition: all 0.3s ease;') : '' }}">
                        <x-digital-card 
                        :id="'card_stake_challenger_' . $battle->challengerCard->id"
                        mode="thumbnail"
                        fullscreen="true"
                        :rarity="$cRarity"
                        :detailUrl="route('cards.show', $battle->challengerCard)"
                        :title="$battle->challengerCard->template->card_title" 
                        :game="$battle->challengerCard->template->gameTitle->title ?? 'GAME'" 
                        :creator="$battle->challengerCard->originalOwner->username ?? 'Creator'"
                        :quote="$battle->challengerCard->template->quote"
                        :backgroundColor="$battle->challengerCard->template->background_color"
                        :borderColor="$battle->challengerCard->template->border_color"
                        :sectionColor="$battle->challengerCard->template->section_color"
                        :primaryTextColor="$battle->challengerCard->template->primary_text_color"
                        :secondaryTextColor="$battle->challengerCard->template->secondary_text_color"
                        :image="$battle->challengerCard->template->display_photo"
                        :wins="$cWins"
                        :losses="$cLosses"
                        :lifePoints="$cLifePoints"
                        :distinctStat="$cDistinctStat"
                        :status="$cStatus"
                        :rankLevel="$cRankLevel"
                        :serialNumber="$battle->challengerCard->serial_number"
                        :year="$battle->challengerCard->forged_at->format('Y')"
                    />
                    </div>
                @endif
            </div>

            <!-- Opponent -->
            <div class="col-md-6">
                <div class="text-center mb-3">
                    @if($battle->status === 'completed' && $battle->winner_id === $battle->opponent_id)
                        <div class="mb-1">
                            <span class="badge bg-warning text-dark" style="font-size: 0.7rem; letter-spacing: 1px; box-shadow: 0 0 10px rgba(255, 221, 0, 0.5);"><i class="bi bi-trophy-fill"></i> WINNER</span>
                        </div>
                    @elseif($battle->status === 'completed' && $battle->winner_id !== null && $battle->winner_id !== $battle->opponent_id)
                        <div class="mb-1">
                            <span class="badge bg-danger text-light" style="font-size: 0.7rem; letter-spacing: 1px; box-shadow: 0 0 10px rgba(255, 0, 0, 0.5);"><i class="bi bi-x-circle-fill"></i> LOSER</span>
                        </div>
                    @endif
                    <div style="font-family: 'Orbitron', sans-serif; font-size: 0.8rem; color: #ff00ff;">
                        @if(Auth::id() === $battle->opponent_id) YOU @else OPPONENT @endif
                    </div>
                    <h4 style="color: #fff;" id="opponent-name-display">
                        {{ $battle->opponent ? $battle->opponent->username : 'AWAITING...' }}
                    </h4>
                </div>
                @if($battle->opponentCard)
                    <div style="{{ $battle->status === 'completed' ? ($battle->winner_id === $battle->opponent_id ? 'box-shadow: 0 0 30px rgba(255, 221, 0, 0.6); border-radius: 16px; transform: scale(1.02); transition: all 0.3s ease;' : 'opacity: 0.5; filter: grayscale(80%); transition: all 0.3s ease;') : '' }}">
                        <x-digital-card 
                        :id="'card_stake_opponent_' . $battle->opponentCard->id"
                        mode="thumbnail"
                        fullscreen="true"
                        :rarity="$oRarity"
                        :detailUrl="route('cards.show', $battle->opponentCard)"
                        :title="$battle->opponentCard->template->card_title" 
                        :game="$battle->opponentCard->template->gameTitle->title ?? 'GAME'" 
                        :creator="$battle->opponentCard->originalOwner->username ?? 'Creator'"
                        :quote="$battle->opponentCard->template->quote"
                        :backgroundColor="$battle->opponentCard->template->background_color"
                        :borderColor="$battle->opponentCard->template->border_color"
                        :sectionColor="$battle->opponentCard->template->section_color"
                        :primaryTextColor="$battle->opponentCard->template->primary_text_color"
                        :secondaryTextColor="$battle->opponentCard->template->secondary_text_color"
                        :image="$battle->opponentCard->template->display_photo"
                        :wins="$oWins"
                        :losses="$oLosses"
                        :lifePoints="$oLifePoints"
                        :distinctStat="$oDistinctStat"
                        :status="$oStatus"
                        :rankLevel="$oRankLevel"
                        :serialNumber="$battle->opponentCard->serial_number"
                        :year="$battle->opponentCard->forged_at->format('Y')"
                    />
                    </div>
                @else
                    <div id="opponent-slot" class="empty-stake-slot d-flex flex-column align-items-center justify-content-center" style="height: 350px; background: rgba(255,0,255,0.02); border: 2px dashed rgba(255,0,255,0.15); border-radius: 16px;">
                        <i class="bi bi-person-plus" style="font-size: 3rem; color: rgba(255,0,255,0.2);"></i>
                        <div class="mt-2 text-muted">Awaiting Opponent</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Mobile View (Carousel) -->
    <div class="d-md-none">
        <div id="cardsAtStakeCarousel" class="carousel slide" data-bs-interval="false">
            <div class="carousel-inner">
                <!-- Challenger Slide -->
                <div class="carousel-item active">
                    <div class="text-center mb-3">
                        @if($battle->status === 'completed' && $battle->winner_id === $battle->challenger_id)
                            <div class="mb-1">
                                <span class="badge bg-warning text-dark" style="font-size: 0.7rem; letter-spacing: 1px; box-shadow: 0 0 10px rgba(255, 221, 0, 0.5);"><i class="bi bi-trophy-fill"></i> WINNER</span>
                            </div>
                        @elseif($battle->status === 'completed' && $battle->winner_id !== null && $battle->winner_id !== $battle->challenger_id)
                            <div class="mb-1">
                                <span class="badge bg-danger text-light" style="font-size: 0.7rem; letter-spacing: 1px; box-shadow: 0 0 10px rgba(255, 0, 0, 0.5);"><i class="bi bi-x-circle-fill"></i> LOSER</span>
                            </div>
                        @endif
                        <div style="font-family: 'Orbitron', sans-serif; font-size: 0.8rem; color: #00f0ff;">
                            @if(Auth::id() === $battle->challenger_id) YOU @else CHALLENGER @endif
                        </div>
                        <h4 style="color: #fff;">{{ $battle->challenger->username }}</h4>
                    </div>
                    @if($battle->challengerCard)
                        <div class="mx-auto" style="{{ $battle->status === 'completed' ? ($battle->winner_id === $battle->challenger_id ? 'box-shadow: 0 0 30px rgba(255, 221, 0, 0.6); border-radius: 16px; transform: scale(1.02); transition: all 0.3s ease;' : 'opacity: 0.5; filter: grayscale(80%); transition: all 0.3s ease;') : '' }}">
                            <x-digital-card 
                            :id="'card_stake_challenger_mob_' . $battle->challengerCard->id"
                            mode="thumbnail"
                            fullscreen="true"
                            :rarity="$cRarity"
                            :detailUrl="route('cards.show', $battle->challengerCard)"
                            :title="$battle->challengerCard->template->card_title" 
                            :game="$battle->challengerCard->template->gameTitle->title ?? 'GAME'" 
                            :creator="$battle->challengerCard->originalOwner->username ?? 'Creator'"
                            :quote="$battle->challengerCard->template->quote"
                            :backgroundColor="$battle->challengerCard->template->background_color"
                            :borderColor="$battle->challengerCard->template->border_color"
                            :sectionColor="$battle->challengerCard->template->section_color"
                            :primaryTextColor="$battle->challengerCard->template->primary_text_color"
                            :secondaryTextColor="$battle->challengerCard->template->secondary_text_color"
                            :image="$battle->challengerCard->template->display_photo"
                            :wins="$cWins"
                        :losses="$cLosses"
                        :lifePoints="$cLifePoints"
                        :distinctStat="$cDistinctStat"
                        :status="$cStatus"
                            :rankLevel="$cRankLevel"
                            :serialNumber="$battle->challengerCard->serial_number"
                            :year="$battle->challengerCard->forged_at->format('Y')"
                            />                            </div>
                    @endif
                </div>
                <!-- Opponent Slide -->
                <div class="carousel-item">
                    <div class="text-center mb-3">
                        @if($battle->status === 'completed' && $battle->winner_id === $battle->opponent_id)
                            <div class="mb-1">
                                <span class="badge bg-warning text-dark" style="font-size: 0.7rem; letter-spacing: 1px; box-shadow: 0 0 10px rgba(255, 221, 0, 0.5);"><i class="bi bi-trophy-fill"></i> WINNER</span>
                            </div>
                        @elseif($battle->status === 'completed' && $battle->winner_id !== null && $battle->winner_id !== $battle->opponent_id)
                            <div class="mb-1">
                                <span class="badge bg-danger text-light" style="font-size: 0.7rem; letter-spacing: 1px; box-shadow: 0 0 10px rgba(255, 0, 0, 0.5);"><i class="bi bi-x-circle-fill"></i> LOSER</span>
                            </div>
                        @endif
                        <div style="font-family: 'Orbitron', sans-serif; font-size: 0.8rem; color: #ff00ff;">
                            @if(Auth::id() === $battle->opponent_id) YOU @else OPPONENT @endif
                        </div>
                        <h4 style="color: #fff;" id="opponent-name-display-mob">
                            {{ $battle->opponent ? $battle->opponent->username : 'AWAITING...' }}
                        </h4>
                    </div>
                    @if($battle->opponentCard)
                        <div class="mx-auto" style="{{ $battle->status === 'completed' ? ($battle->winner_id === $battle->opponent_id ? 'box-shadow: 0 0 30px rgba(255, 221, 0, 0.6); border-radius: 16px; transform: scale(1.02); transition: all 0.3s ease;' : 'opacity: 0.5; filter: grayscale(80%); transition: all 0.3s ease;') : '' }}">
                            <x-digital-card 
                            :id="'card_stake_opponent_mob_' . $battle->opponentCard->id"
                            mode="thumbnail"
                            fullscreen="true"
                            :rarity="$oRarity"
                            :detailUrl="route('cards.show', $battle->opponentCard)"
                            :title="$battle->opponentCard->template->card_title" 
                            :game="$battle->opponentCard->template->gameTitle->title ?? 'GAME'" 
                            :creator="$battle->opponentCard->originalOwner->username ?? 'Creator'"
                            :quote="$battle->opponentCard->template->quote"
                            :backgroundColor="$battle->opponentCard->template->background_color"
                            :borderColor="$battle->opponentCard->template->border_color"
                            :sectionColor="$battle->opponentCard->template->section_color"
                            :primaryTextColor="$battle->opponentCard->template->primary_text_color"
                            :secondaryTextColor="$battle->opponentCard->template->secondary_text_color"
                            :image="$battle->opponentCard->template->display_photo"
                            :wins="$oWins"
                        :losses="$oLosses"
                        :lifePoints="$oLifePoints"
                        :distinctStat="$oDistinctStat"
                        :status="$oStatus"
                            :rankLevel="$oRankLevel"
                            :serialNumber="$battle->opponentCard->serial_number"
                            :year="$battle->opponentCard->forged_at->format('Y')"
                            />                            </div>
                    @else
                        <div class="empty-stake-slot d-flex flex-column align-items-center justify-content-center" style="height: 350px; background: rgba(255,0,255,0.02); border: 2px dashed rgba(255,0,255,0.15); border-radius: 16px;">
                            <i class="bi bi-person-plus" style="font-size: 3rem; color: rgba(255,0,255,0.2);"></i>
                            <div class="mt-2 text-muted">Awaiting Opponent</div>
                        </div>
                    @endif
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#cardsAtStakeCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#cardsAtStakeCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
        </div>
    </div>
</div>
