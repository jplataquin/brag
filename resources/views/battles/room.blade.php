@extends('layouts.app')

@section('title', 'Battle Room #' . $battle->id)

@section('content')
<div class="mb-3">
    <a href="{{ route('battles.index') }}" style="color: #8888aa; font-size: 0.85rem; text-decoration: none;">
        <i class="bi bi-arrow-left"></i> Back to Arena
    </a>
</div>

<div class="row g-4">
    <!-- 1.) Battle Details -->
    <div class="col-lg-12">
        <div class="neon-card p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h1 class="page-title mb-1">
                        <span class="page-title-accent"><i class="bi bi-crosshair"></i></span> BATTLE #{{ $battle->id }}
                    </h1>
                    <div class="mb-2">
                        @if($battle->challengerCard && $battle->challengerCard->template && $battle->challengerCard->template->gameTitle)
                            <span style="color: #00f0ff; font-family: 'Orbitron', sans-serif; font-size: 1.1rem; letter-spacing: 2px; text-transform: uppercase; font-weight: 700;">
                                {{ $battle->challengerCard->template->gameTitle->title }}
                            </span>
                        @endif
                    </div>
                    <div class="d-flex gap-3 align-items-center flex-wrap">
                        <span id="battle-status-badge" class="status-badge status-{{ $battle->status }}">{{ strtoupper($battle->status) }}</span>
                        @if($battle->marshall)
                            <span style="color: #ffdd00; font-size: 0.9rem;">
                                <i class="bi bi-shield-shaded"></i> MARSHALL: <strong>{{ $battle->marshall->username }}</strong>
                            </span>
                        @else
                            <span class="text-muted" style="font-size: 0.9rem;">
                                <i class="bi bi-shield"></i> NO MARSHALL ELECTED
                            </span>
                        @endif
                    </div>
                    
                    @if(!$battle->marshall_id && ($battle->challenger_marshall_id || $battle->opponent_marshall_id))
                        <div class="mt-3 p-2 rounded" style="background: rgba(255, 221, 0, 0.05); border: 1px solid rgba(255, 221, 0, 0.1); display: inline-block;">
                            <div class="d-flex gap-3 align-items-center" style="font-size: 0.8rem;">
                                <div style="color: #8888aa;">ELECTION STATUS:</div>
                                <div style="color: #00f0ff;">
                                    CHALLENGER: <strong>{{ $battle->challengerMarshall ? $battle->challengerMarshall->username : 'NONE' }}</strong>
                                </div>
                                <div style="color: #ff00ff;">
                                    OPPONENT: <strong>{{ $battle->opponentMarshall ? $battle->opponentMarshall->username : 'NONE' }}</strong>
                                </div>
                                @if($battle->challenger_marshall_id && $battle->opponent_marshall_id && $battle->challenger_marshall_id === $battle->opponent_marshall_id)
                                    <div class="neon-text-yellow px-2" style="animation: pulse-yellow 2s infinite;">
                                        <i class="bi bi-hourglass-split"></i> AWAITING ACCEPTANCE
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
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

    <!-- 2.) Cards at Stake -->
    <div class="col-lg-8">
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
                            :rarity="$battle->challengerCard->rarity_slug"
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
                            :wins="$battle->challengerCard->wins"
                            :losses="$battle->challengerCard->losses"
                            :lifePoints="$battle->challengerCard->life_points"
                            :distinctStat="$battle->challengerCard->distinct_stat"
                            :status="$battle->challengerCard->status"
                            :rankLevel="$battle->challengerCard->level"
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
                            :rarity="$battle->opponentCard->rarity_slug"
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
                            :wins="$battle->opponentCard->wins"
                            :losses="$battle->opponentCard->losses"
                            :lifePoints="$battle->opponentCard->life_points"
                            :distinctStat="$battle->opponentCard->distinct_stat"
                            :status="$battle->opponentCard->status"
                            :rankLevel="$battle->opponentCard->level"
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
                                :rarity="$battle->challengerCard->rarity_slug"
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
                                :wins="$battle->challengerCard->wins"
                            :losses="$battle->challengerCard->losses"
                            :lifePoints="$battle->challengerCard->life_points"
                            :distinctStat="$battle->challengerCard->distinct_stat"
                            :status="$battle->challengerCard->status"
                                :rankLevel="$battle->challengerCard->level"
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
                                :rarity="$battle->opponentCard->rarity_slug"
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
                                :wins="$battle->opponentCard->wins"
                            :losses="$battle->opponentCard->losses"
                            :lifePoints="$battle->opponentCard->life_points"
                            :distinctStat="$battle->opponentCard->distinct_stat"
                            :status="$battle->opponentCard->status"
                                :rankLevel="$battle->opponentCard->level"
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

        <!-- Take Action (Below Cards) -->
        <div class="mt-4 mb-5 pt-4" style="border-top: 1px solid rgba(0, 240, 255, 0.1);">
            <h5 class="section-header">
                <i class="bi bi-gear-wide-connected section-icon" style="color: #00f0ff;"></i> BATTLE ACTIONS
            </h5>
            <div id="actions-container" class="d-flex gap-3 flex-wrap align-items-center">
                <!-- Ready (Join) -->
                @if($battle->status === 'pending' && !$battle->opponent_id && Auth::id() !== $battle->challenger_id)
                    <a href="{{ route('battles.join.ready', $battle) }}" class="btn btn-neon-lime">
                        <i class="bi bi-lightning-fill"></i> READY TO BATTLE
                    </a>
                @endif

                <!-- Start Match (Challenger Only, when Ready) -->
                @if($battle->status === 'ready' && Auth::id() === $battle->challenger_id)
                    <form method="POST" action="{{ route('battles.start', $battle) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-neon-lime" style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);">
                            <i class="bi bi-play-fill"></i> START MATCH
                        </button>
                    </form>

                    <form method="POST" action="{{ route('battles.rejectOpponent', $battle) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-neon-danger" data-confirm="Reject this opponent and open the room for new challengers?">
                            <i class="bi bi-person-x-fill"></i> REJECT OPPONENT
                        </button>
                    </form>
                @endif

                <!-- Invite (Only if one player) -->
                @if(!$battle->opponent_id && Auth::id() === $battle->challenger_id && $battle->status === 'pending')
                    <button type="button" id="btn-invite-opponent" class="btn btn-neon" data-bs-toggle="modal" data-bs-target="#inviteOpponentModal">
                        <i class="bi bi-person-plus-fill"></i> INVITE OPPONENT
                    </button>
                @endif

                <!-- Poke Player (If opponent is present) -->
                @if($battle->opponent_id && in_array(Auth::id(), [$battle->challenger_id, $battle->opponent_id]) && in_array($battle->status, ['pending', 'ready', 'active', 'failed']))
                    <button type="button" class="btn btn-neon" style="border-color: #00f0ff; color: #00f0ff;" onclick="pokePlayer(this)">
                        <i class="bi bi-hand-index-thumb-fill"></i> POKE
                    </button>
                @endif

                <!-- Elect Marshall (Visible if not elected yet) -->
                @if(!$battle->marshall_id && in_array(Auth::id(), [$battle->challenger_id, $battle->opponent_id]))
                    <button type="button" class="btn btn-neon" style="border-color: #ffdd00; color: #ffdd00;" data-bs-toggle="modal" data-bs-target="#electMarshallModal">
                        <i class="bi bi-shield-fill-check"></i> 
                        {{ (Auth::id() === $battle->challenger_id ? $battle->challenger_marshall_id : $battle->opponent_marshall_id) ? 'CHANGE ELECTION' : 'ELECT MARSHALL' }}
                    </button>
                @endif

                <!-- Marshall Leave -->
                @if($battle->marshall_id === Auth::id())
                    <form method="POST" action="{{ route('battles.leaveMarshall', $battle) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-neon-danger" data-confirm="Are you sure you want to leave this battle room?">
                            <i class="bi bi-box-arrow-right"></i> LEAVE BATTLE
                        </button>
                    </form>
                @endif

                <!-- Declare Winner -->
                @if(in_array($battle->status, ['active', 'failed']) && $battle->can_be_decided)
                    @php
                        $canDeclare = false;
                        if ($battle->marshall_id && Auth::id() === $battle->marshall_id) {
                            $canDeclare = true;
                        } elseif (!$battle->marshall_id && in_array(Auth::id(), [$battle->challenger_id, $battle->opponent_id])) {
                            $canDeclare = true;
                        } elseif ($battle->marshall_id && in_array(Auth::id(), [$battle->challenger_id, $battle->opponent_id])) {
                            // Players can still declare even if there is an marshall (for consensus)
                            $canDeclare = true;
                        }
                    @endphp

                    @if($canDeclare)
                        <button type="button" class="btn btn-neon-magenta" data-bs-toggle="modal" data-bs-target="#declareWinnerModal">
                            <i class="bi bi-trophy-fill"></i> DECLARE RESULT
                        </button>
                    @endif
                @endif

                <!-- Cancel Battle -->
                @php
                    $canCancel = false;
                    if ($battle->status === 'pending' && Auth::id() === $battle->challenger_id) {
                        $canCancel = true;
                    } elseif (in_array($battle->status, ['ready', 'active', 'failed']) && in_array(Auth::id(), [$battle->challenger_id, $battle->opponent_id])) {
                        $canCancel = true;
                    } elseif (in_array($battle->status, ['active', 'failed']) && Auth::id() === $battle->marshall_id) {
                        $canCancel = true;
                    }
                    
                    // Hide if already requested
                    if (Auth::id() === $battle->challenger_id && $battle->challenger_cancel) $canCancel = false;
                    if (Auth::id() === $battle->opponent_id && $battle->opponent_cancel) $canCancel = false;
                @endphp

                @if($canCancel)
                    <button type="button" class="btn btn-neon-danger" onclick="cancelBattle(this)" data-message="{{ in_array($battle->status, ['active', 'failed']) && Auth::id() !== $battle->marshall_id ? 'Request cancellation of this battle? The other player must agree.' : 'Cancel this battle?' }}">
                        <i class="bi bi-x-circle"></i> <span class="btn-text">{{ in_array($battle->status, ['ready', 'active', 'failed']) && Auth::id() !== $battle->marshall_id ? 'REQUEST CANCEL' : 'CANCEL BATTLE' }}</span>
                    </button>
                @endif

                <!-- Share QR (Mobile Only) -->
                <button type="button" class="btn btn-neon d-md-none" style="border-color: #39ff14; color: #39ff14;" data-bs-toggle="modal" data-bs-target="#shareQRModal">
                    <i class="bi bi-qr-code"></i> SHARE QR
                </button>
            </div>
        </div>
    </div>

    <!-- 3.) Activity Logs -->
    <div class="col-lg-4">
        <h5 class="section-header">
            <i class="bi bi-journal-text section-icon" style="color: #39ff14;"></i> ACTIVITY LOGS
        </h5>
        <div class="activity-log-container p-3" style="background: rgba(10, 10, 30, 0.6); border: 1px solid rgba(57, 255, 20, 0.1); border-radius: 12px; height: 500px; overflow-y: auto;">
            <div class="activity-list" id="activity-logs-list">
                @if($battle->activities->count() > 0)
                    @foreach($battle->activities as $activity)
                        <div class="activity-item mb-3 pb-3" style="border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                            <div class="d-flex gap-2 align-items-start">
                                <div class="activity-icon-sm mt-1">
                                    @switch($activity->type)
                                        @case('create') <i class="bi bi-plus-circle-fill text-info"></i> @break
                                        @case('join') <i class="bi bi-person-check-fill text-success"></i> @break
                                        @case('invite') <i class="bi bi-envelope-fill text-warning"></i> @break
                                        @case('elect_marshall') <i class="bi bi-shield-fill text-warning"></i> @break
                                        @case('marshall_election') <i class="bi bi-shield-check text-warning"></i> @break
                                        @case('marshall_accepted') <i class="bi bi-shield-lock-fill text-warning"></i> @break
                                        @case('marshall_rejected') <i class="bi bi-shield-x text-danger"></i> @break
                                        @case('marshall_leave') <i class="bi bi-box-arrow-right text-danger"></i> @break
                                        @case('declare') <i class="bi bi-megaphone-fill text-info"></i> @break
                                        @case('conflict') <i class="bi bi-exclamation-triangle-fill text-danger"></i> @break
                                        @case('marshall_decision') <i class="bi bi-shield-lock-fill text-warning"></i> @break
                                        @case('consensus') <i class="bi bi-people-fill text-success"></i> @break
                                        @case('winner') <i class="bi bi-trophy-fill text-success"></i> @break
                                        @case('cancel') <i class="bi bi-x-circle-fill text-danger"></i> @break
                                        @case('cancel_request') <i class="bi bi-exclamation-circle-fill text-warning"></i> @break
                                        @case('cancel_agree') <i class="bi bi-check-circle-fill text-success"></i> @break
                                        @case('cancel_reject') <i class="bi bi-x-circle text-danger"></i> @break
                                        @case('start') <i class="bi bi-play-circle-fill text-success"></i> @break
                                        @case('poke') <i class="bi bi-hand-index-thumb-fill text-info"></i> @break
                                        @default <i class="bi bi-dot text-muted"></i>
                                    @endswitch
                                </div>
                                <div>
                                    @php
                                        $formattedMessage = e($activity->message);
                                        $formattedMessage = preg_replace('/@([\w\-.]+)/', '<a href="/user/$1" target="_blank" style="color: #ffdd00; text-decoration: none; font-weight: bold;">@$1</a>', $formattedMessage);
                                    @endphp
                                    <div style="font-size: 0.85rem; color: #fff;">{!! $formattedMessage !!}</div>
                                    <div style="font-size: 0.7rem; color: #555577;">{{ $activity->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div id="empty-activity" class="text-center py-5 text-muted" style="font-size: 0.9rem;">
                        <i class="bi bi-chat-dots" style="font-size: 2rem; opacity: 0.3;"></i>
                        <p class="mt-2">Waiting for activity...</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<!-- Invite Opponent Modal -->
<div class="modal fade" id="inviteOpponentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #00f0ff; backdrop-filter: blur(20px);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title neon-text">INVITE OPPONENT</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('battles.invite', $battle) }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="opponent">
                <div class="modal-body py-4">
                    <div class="mb-3 position-relative">
                        <label class="form-label">PLAYER USERNAME</label>
                        <div id="invite-input-wrapper" class="form-control d-flex align-items-center p-1" style="min-height: 42px; cursor: text;">
                            <!-- Hidden input for form submission -->
                            <input type="hidden" name="username" id="invite-hidden-username" required>

                            <!-- Selected User Chip -->
                            <span id="invite-selected-chip" class="badge d-flex align-items-center gap-2 p-2 d-none" style="background: rgba(0,240,255,0.2); border: 1px solid #00f0ff; color: #00f0ff; font-size: 0.9rem;">
                                <i class="bi bi-person-fill"></i> <span id="invite-chip-text"></span>
                                <i class="bi bi-x-circle-fill ms-2" style="cursor: pointer;" onclick="clearInviteSelection()"></i>
                            </span>

                            <!-- Search Input -->
                            <input type="text" id="invite-player-input" class="border-0 bg-transparent text-white flex-grow-1 px-2" placeholder="Search username..." autocomplete="off" style="outline: none; box-shadow: none;">
                        </div>

                        <div id="invite-player-results" class="position-absolute w-100 mt-1 d-none" style="z-index: 1050; max-height: 200px; overflow-y: auto; background: rgba(10, 10, 30, 0.95); border: 1px solid #00f0ff; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);"></div>
                    </div>
                    <p class="text-muted small">The player must have a card of the same level to accept.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-neon w-100">SEND INVITE</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Elect Marshall Modal -->
<div class="modal fade" id="electMarshallModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #ffdd00; backdrop-filter: blur(20px);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" style="color: #ffdd00; font-family: 'Orbitron', sans-serif;">ELECT MARSHALL</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('battles.electMarshall', $battle) }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-3 position-relative">
                        <label class="form-label">MARSHALL USERNAME</label>
                        @php
                            $existingUsername = Auth::id() === $battle->challenger_id ? $battle->challengerMarshall?->username : $battle->opponentMarshall?->username;
                            $existingUserId = Auth::id() === $battle->challenger_id ? $battle->challenger_marshall_id : $battle->opponent_marshall_id;
                        @endphp
                        <div id="adj-input-wrapper" class="form-control d-flex align-items-center p-1" style="min-height: 42px; cursor: text;">
                            <!-- Hidden input for form submission -->
                            <input type="hidden" name="user_id" id="adj-hidden-user-id" value="{{ $existingUserId }}" required>
                            
                            <!-- Selected User Chip -->
                            <span id="adj-selected-chip" class="badge d-flex align-items-center gap-2 p-2 {{ $existingUsername ? '' : 'd-none' }}" style="background: rgba(255,221,0,0.2); border: 1px solid #ffdd00; color: #ffdd00; font-size: 0.9rem;">
                                <i class="bi bi-person-fill"></i> <span id="adj-chip-text">{{ $existingUsername }}</span>
                                <i class="bi bi-x-circle-fill ms-2" style="cursor: pointer;" onclick="clearMarshallSelection()"></i>
                            </span>
                            
                            <!-- Search Input -->
                            <input type="text" id="elect-marshall-input" class="border-0 bg-transparent text-white flex-grow-1 px-2 {{ $existingUsername ? 'd-none' : '' }}" placeholder="Search username..." autocomplete="off" style="outline: none; box-shadow: none;">
                        </div>

                        <div id="elect-marshall-results" class="position-absolute w-100 mt-1 d-none" style="z-index: 1050; max-height: 200px; overflow-y: auto; background: rgba(10, 10, 30, 0.95); border: 1px solid #ffdd00; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);"></div>
                    </div>
                    <p class="text-muted small">Both players must elect the same user for them to be invited as an marshall. Participants cannot be elected.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-neon w-100" style="border-color: #ffdd00; color: #ffdd00;">ELECT USER</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Marshall Response Options -->
@if(!$battle->marshall_id && $battle->challenger_marshall_id === Auth::id() && $battle->opponent_marshall_id === Auth::id())
<div class="col-lg-12 mt-3">
    <div class="neon-card p-4 border-warning text-center" style="background: rgba(255, 221, 0, 0.05);">
        <h4 class="neon-text-yellow mb-3">⚖️ MARSHALL ELECTION</h4>
        <p>Both players have elected you to adjudicate this battle. Do you accept this responsibility?</p>
        <div class="d-flex gap-3 justify-content-center mt-4">
            <form method="POST" action="{{ route('battles.acceptMarshall', $battle) }}">
                @csrf
                <button type="submit" class="btn btn-neon" style="background: rgba(255, 221, 0, 0.1); border-color: #ffdd00; color: #ffdd00;">
                    <i class="bi bi-check-lg"></i> ACCEPT ROLE
                </button>
            </form>
            <form method="POST" action="{{ route('battles.rejectMarshall', $battle) }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-x-lg"></i> REJECT
                </button>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Declare Result Modal -->
<div class="modal fade" id="declareWinnerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #ff00ff; backdrop-filter: blur(20px);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title neon-text-magenta">DECLARE RESULT</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                @if($battle->status === 'failed')
                    <div class="alert alert-danger mb-4" style="background: rgba(255,0,0,0.1); border-color: #ff0055; color: #ff0055; font-size: 0.85rem;">
                        <i class="bi bi-exclamation-octagon-fill"></i> <strong>CONFLICT DETECTED:</strong> Players have declared different winners. Please reach a consensus or wait for the marshall.
                    </div>
                @endif

                @if($battle->marshall_id === Auth::id())
                    {{-- MARSHALL VIEW: Full selection --}}
                    <p class="text-center mb-4">Click on a player below to declare them as the winner of this match. As an marshall, your decision will finalize the battle immediately.</p>
                    
                    <div class="d-flex flex-column gap-3 text-start mb-2">
                        <!-- Challenger Option -->
                        <button type="button" onclick="declareWinnerAjax({{ $battle->challenger_id }}, this)" data-message="Declare {{ $battle->challenger->username }} as winner?" class="w-100 p-3 rounded d-flex flex-column gap-2 border-0" style="background: rgba(0, 240, 255, 0.05); border: 1px solid rgba(0, 240, 255, 0.5) !important; color: #fff; cursor: pointer; text-align: left; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(0, 240, 255, 0.15)'; this.style.boxShadow='0 0 15px rgba(0, 240, 255, 0.4)';" onmouseout="this.style.background='rgba(0, 240, 255, 0.05)'; this.style.boxShadow='none';">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <span style="font-size: 1.1rem;"><i class="bi bi-person-fill text-info"></i> {{ $battle->challenger->username }}</span>
                                <span class="badge text-dark" style="background-color: #00f0ff;">CHALLENGER</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center w-100 small" style="color: #8888aa;">
                                <span>Their Declaration:</span>
                                <strong style="color: #fff;" id="challenger-declaration-text">{{ $battle->challenger_declared_user_win ? \App\Models\User::find($battle->challenger_declared_user_win)->username : 'NONE' }}</strong>
                            </div>
                        </button>

                        <!-- Opponent Option -->
                        @if($battle->opponent)
                        <button type="button" onclick="declareWinnerAjax({{ $battle->opponent_id }}, this)" data-message="Declare {{ $battle->opponent->username }} as winner?" class="w-100 p-3 rounded d-flex flex-column gap-2 border-0" style="background: rgba(255, 0, 255, 0.05); border: 1px solid rgba(255, 0, 255, 0.5) !important; color: #fff; cursor: pointer; text-align: left; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255, 0, 255, 0.15)'; this.style.boxShadow='0 0 15px rgba(255, 0, 255, 0.4)';" onmouseout="this.style.background='rgba(255, 0, 255, 0.05)'; this.style.boxShadow='none';">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <span style="font-size: 1.1rem;"><i class="bi bi-person-fill text-magenta" style="color: #ff00ff;"></i> {{ $battle->opponent->username }}</span>
                                <span class="badge text-white" style="background-color: #ff00ff;">OPPONENT</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center w-100 small" style="color: #8888aa;">
                                <span>Their Declaration:</span>
                                <strong style="color: #fff;" id="opponent-declaration-text">{{ $battle->opponent_declared_user_win ? \App\Models\User::find($battle->opponent_declared_user_win)->username : 'NONE' }}</strong>
                            </div>
                        </button>
                        @endif
                    </div>
                @else
                    {{-- PLAYER VIEW: Simple Won/Lost --}}
                    <h4 class="text-center mb-4 neon-text" style="font-family: 'Orbitron', sans-serif; letter-spacing: 1px;">WHAT'S YOUR RESULT?</h4>
                    
                    <div class="row g-3">
                        @php
                            $isChallenger = Auth::id() === $battle->challenger_id;
                            $myId = Auth::id();
                            $opponentId = $isChallenger ? $battle->opponent_id : $battle->challenger_id;
                        @endphp
                        <div class="col-6">
                            <button type="button" onclick="declareWinnerAjax({{ $opponentId }}, this)" data-message="Confirm that you LOST this match?" class="btn btn-outline-danger w-100 py-4 d-flex flex-column align-items-center gap-2" style="border-width: 2px; background: rgba(255,0,0,0.05);">
                                <i class="bi bi-hand-thumbs-down-fill fs-1"></i>
                                <span style="font-family: 'Orbitron', sans-serif; font-weight: bold; letter-spacing: 2px;">I LOST</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" onclick="declareWinnerAjax({{ $myId }}, this)" data-message="Confirm that you WON this match?" class="btn btn-neon-lime w-100 py-4 d-flex flex-column align-items-center gap-2" style="border-width: 2px;">
                                <i class="bi bi-trophy-fill fs-1"></i>
                                <span style="font-family: 'Orbitron', sans-serif; font-weight: bold; letter-spacing: 2px;">I WON</span>
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 p-3 rounded" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                        <div class="d-flex justify-content-between align-items-center mb-2 small text-muted">
                            <span>CURRENT STATUS:</span>
                        </div>
                        <div class="d-flex flex-column gap-2 small">
                            <div class="d-flex justify-content-between">
                                <span style="color: #00f0ff;">CHALLENGER:</span>
                                <strong id="status-text-challenger" style="color: #fff;">{{ $battle->challenger_declared_user_win ? \App\Models\User::find($battle->challenger_declared_user_win)->username : 'PENDING...' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span style="color: #ff00ff;">OPPONENT:</span>
                                <strong id="status-text-opponent" style="color: #fff;">{{ $battle->opponent_declared_user_win ? \App\Models\User::find($battle->opponent_declared_user_win)->username : 'PENDING...' }}</strong>
                            </div>
                        </div>                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Share QR Modal -->
<div class="modal fade" id="shareQRModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #39ff14; backdrop-filter: blur(20px);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" style="color: #39ff14; font-family: 'Orbitron', sans-serif;">BATTLE QR CODE</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div id="qrcode-container" class="d-inline-block p-3 bg-white rounded-3 mb-3">
                    <div id="qrcode"></div>
                </div>
                <p class="text-muted small">Show this QR code to your opponent to let them join this battle room.</p>
                <div class="mt-3">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control bg-dark border-secondary text-light" value="{{ url()->current() }}" id="battle-url" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyBattleUrl()">COPY</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Rejected Opponent Modal -->
<div class="modal fade" id="rejectedOpponentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #ff4444; backdrop-filter: blur(20px);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" style="color: #ff4444; font-family: 'Orbitron', sans-serif;">BET REJECTED</h5>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="bi bi-x-circle" style="font-size: 3rem; color: rgba(255, 68, 68, 0.5);"></i>
                </div>
                <p>The challenger has rejected your bet. You have been removed from the battle room.</p>
                <div class="mt-4">
                    <a href="{{ route('battles.index') }}" class="btn btn-neon-danger w-100">RETURN TO ARENA</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancellation Request Modal -->
@php
    $showCancelModal = false;
    $requesterName = '';
    if ($battle->status !== 'cancelled') {
        if ($battle->challenger_cancel && Auth::id() === $battle->opponent_id) {
            $showCancelModal = true;
            $requesterName = $battle->challenger?->username ?? 'Unknown Challenger';
        } elseif ($battle->opponent_cancel && Auth::id() === $battle->challenger_id) {
            $showCancelModal = true;
            $requesterName = $battle->opponent?->username ?? 'Unknown Opponent';
        }
    }
@endphp

<div class="modal fade" id="cancellationRequestModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #ff00ff; backdrop-filter: blur(20px); box-shadow: 0 0 30px rgba(255, 0, 255, 0.2);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title neon-text-magenta">CANCELLATION REQUEST</h5>
            </div>
            <div class="modal-body py-4 text-center">
                <div class="mb-4">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem; color: #ff00ff; opacity: 0.8;"></i>
                </div>
                <p class="mb-4" style="font-size: 1.1rem;">
                    <strong id="cancel-requester-name">{{ $requesterName }}</strong> has requested to cancel this battle. 
                    Do you agree to cancel the match?
                </p>
                <p class="text-muted small mb-4">
                    If you agree, the battle will be cancelled and no cards will be transferred.
                    If you reject, the battle will continue.
                </p>
                
                <div class="d-flex gap-3">
                    <form method="POST" action="{{ route('battles.respondToCancellation', $battle) }}" class="flex-fill">
                        @csrf
                        <input type="hidden" name="agreed" value="1">
                        <button type="submit" class="btn btn-neon-magenta w-100">
                            <i class="bi bi-check-lg"></i> AGREE & CANCEL
                        </button>
                    </form>
                    <form method="POST" action="{{ route('battles.respondToCancellation', $battle) }}" class="flex-fill">
                        @csrf
                        <input type="hidden" name="agreed" value="0">
                        <button type="submit" class="btn btn-outline-secondary w-100" style="border-color: #555;">
                            <i class="bi bi-x-lg"></i> REJECT
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endpush

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($showCancelModal)
            const initialCancelModal = new bootstrap.Modal(document.getElementById('cancellationRequestModal'));
            initialCancelModal.show();
        @endif

        const qrcodeContainer = document.getElementById('qrcode');
        if (qrcodeContainer) {
            new QRCode(qrcodeContainer, {
                text: "{{ url()->current() }}",
                width: 200,
                height: 200,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        }

        // Scroll activity log to bottom on load
        scrollLogToBottom();

        // Websocket Listener via Laravel Echo
        if (window.Echo) {
            window.Echo.channel('battle.{{ $battle->room_id }}')
                .listen('BattleUpdated', (e) => {
                    // Check for rejection
                    if (e.type && e.type.startsWith('reject_')) {
                        const rejectedId = e.type.split('_')[1];
                        if (rejectedId == {{ Auth::id() }}) {
                            // This user was rejected
                            const rejectModal = new bootstrap.Modal(document.getElementById('rejectedOpponentModal'));
                            rejectModal.show();
                            return; // Stop processing other updates for this user
                        } else {
                            // Challenger (or someone else), just reload to see empty slot
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                            return;
                        }
                    }

                    // Show Notification
                    if (e.message) {
                        showNeonNotification(e.message, e.type);
                    }

                    // Update Status Badge
                    const badge = document.getElementById('battle-status-badge');
                    if (badge && e.status) {
                        badge.innerText = e.status.toUpperCase();
                        badge.className = `status-badge status-${e.status}`;
                    }

                    // For major structural changes (like someone joining or the battle completing),
                    if (['join', 'start', 'winner', 'marshall_accepted', 'cancel', 'cancel_agree', 'cancel_reject', 'marshall_decision', 'consensus'].includes(e.type)) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500); // Give user a moment to see the notification
                        return;
                    }

                    // Handle cancel_request without reloading
                    if (e.type && e.type.startsWith('cancel_request_')) {
                        const requesterId = parseInt(e.type.split('_')[2]);
                        
                        // If this user is the one who requested it, don't show them the modal!
                        if (requesterId !== {{ Auth::id() }}) {
                            const messageText = e.message || '';
                            const requesterName = messageText.split(' ')[0] || 'Opponent';
                            const requesterNameEl = document.getElementById('cancel-requester-name');
                            if (requesterNameEl) {
                                requesterNameEl.innerText = requesterName;
                            }
                            const cancelModal = new bootstrap.Modal(document.getElementById('cancellationRequestModal'));
                            cancelModal.show();
                        }
                    }

                    // For lightweight changes, we just append to the activity log dynamically
                    // Exclude poke events from the activity log per requirement
                    if (!e.type.startsWith('poke') && !e.type.startsWith('cancel_request_')) {
                        appendActivity(e.message, e.type);

                        // If it's a declaration and doesn't trigger a reload (no consensus yet)
                        // update the UI text dynamically
                        if (e.type === 'declare' || e.type === 'conflict') {
                            // Update status text in simplified player modal if it exists
                            const challengerStatusText = document.getElementById('status-text-challenger');
                            const opponentStatusText = document.getElementById('status-text-opponent');
                            
                            if (challengerStatusText && e.challenger_declared_name) {
                                challengerStatusText.innerText = e.challenger_declared_name;
                            }
                            if (opponentStatusText && e.opponent_declared_name) {
                                opponentStatusText.innerText = e.opponent_declared_name;
                            }

                            // Update declaration names in marshall modal if it exists
                            const challengerDeclName = document.getElementById('challenger-declaration-text');
                            const opponentDeclName = document.getElementById('opponent-declaration-text');

                            if (challengerDeclName && e.challenger_declared_name) {
                                challengerDeclName.innerText = e.challenger_declared_name;
                            }
                            if (opponentDeclName && e.opponent_declared_name) {
                                opponentDeclName.innerText = e.opponent_declared_name;
                            }

                            if (e.type === 'conflict') {
                                // Add conflict banner dynamically
                                const modalBody = document.querySelector('#declareWinnerModal .modal-body');
                                if (modalBody && !modalBody.querySelector('.alert-danger')) {
                                    const alertHtml = `
                                    <div class="alert alert-danger mb-4" style="background: rgba(255,0,0,0.1); border-color: #ff0055; color: #ff0055; font-size: 0.85rem;">
                                        <i class="bi bi-exclamation-octagon-fill"></i> <strong>CONFLICT DETECTED:</strong> Players have declared different winners. Please reach a consensus or wait for the marshall.
                                    </div>`;
                                    modalBody.insertAdjacentHTML('afterbegin', alertHtml);
                                }
                            }
                        }

                    } else if (e.type.startsWith('cancel_request_')) {
                        appendActivity(e.message, 'cancel_request');
                    }
                });
        }
    });

    async function declareWinnerAjax(winnerId, btn) {
        const message = btn.getAttribute('data-message');
        const result = await window.neonConfirm(message);
        
        if (!result) return;
        
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<div class="text-center w-100 py-2"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> DECLARING...</div>';
        btn.disabled = true;

        fetch(`{{ route('battles.declareWinner', $battle) }}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ winner_id: winnerId })
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
                return null;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;
            if (data.success) {
                showNeonNotification(data.message, 'declare');
                
                // Reset button text and state
                btn.innerHTML = originalHtml;
                btn.disabled = false;

                // Update styling to add yellow highlight
                const btnLost = document.getElementById('btn-declare-lost');
                const btnWon = document.getElementById('btn-declare-won');
                
                if (btnLost && btnWon) {
                    btnLost.style.boxShadow = 'none';
                    btnLost.style.setProperty('border-color', '', 'important');
                    btnWon.style.boxShadow = 'none';
                    btnWon.style.setProperty('border-color', '', 'important');
                    
                    btn.style.boxShadow = '0 0 20px #ffdd00';
                    btn.style.setProperty('border-color', '#ffdd00', 'important');
                }

                const declareModal = bootstrap.Modal.getInstance(document.getElementById('declareWinnerModal'));
                if (declareModal) declareModal.hide();
            } else {
                showNeonNotification(data.message, 'conflict');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        })
        .catch(err => {
            showNeonNotification('An error occurred while declaring winner.', 'conflict');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }

    async function cancelBattle(btn) {
        const message = btn.getAttribute('data-message');
        const result = await window.neonConfirm(message);
        
        if (!result) return;
        
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <span class="btn-text">PROCESSING...</span>';
        btn.disabled = true;

        fetch(`{{ route('battles.cancel', $battle) }}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
                return null;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;
            if(data.success) {
                if (data.status === 'cancelled') {
                    showNeonNotification(data.message, 'cancel');
                    setTimeout(() => {
                        window.location.href = "{{ route('battles.index') }}";
                    }, 1000);
                } else {
                    showNeonNotification(data.message, 'cancel_request');
                    btn.style.display = 'none'; // hide the button since request was sent
                }
            } else {
                showNeonNotification(data.message, 'conflict');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        })
        .catch(err => {
            showNeonNotification('An error occurred while cancelling.', 'conflict');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }

    function pokePlayer(btn) {
        if(btn.disabled) return;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> POKING...';
        btn.disabled = true;

        fetch(`{{ route('battles.poke', $battle) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showNeonNotification(data.message, 'poke');
            } else {
                showNeonNotification(data.message, 'conflict');
            }
        })
        .catch(err => {
            showNeonNotification('An error occurred while poking.', 'conflict');
        })
        .finally(() => {
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }, 2000);
        });
    }

    function getIconForActivity(type) {
        if (type && type.startsWith('reject_')) return '<i class="bi bi-person-x-fill text-danger"></i>';
        switch(type) {
            case 'create': return '<i class="bi bi-plus-circle-fill text-info"></i>';
            case 'join': return '<i class="bi bi-person-check-fill text-success"></i>';
            case 'invite': return '<i class="bi bi-envelope-fill text-warning"></i>';
            case 'elect_marshall': return '<i class="bi bi-shield-fill text-warning"></i>';
            case 'marshall_election': return '<i class="bi bi-shield-check text-warning"></i>';
            case 'marshall_accepted': return '<i class="bi bi-shield-lock-fill text-warning"></i>';
            case 'marshall_rejected': return '<i class="bi bi-shield-x text-danger"></i>';
            case 'marshall_leave': return '<i class="bi bi-box-arrow-right text-danger"></i>';
            case 'declare': return '<i class="bi bi-megaphone-fill text-info"></i>';
            case 'conflict': return '<i class="bi bi-exclamation-triangle-fill text-danger"></i>';
            case 'marshall_decision': return '<i class="bi bi-shield-lock-fill text-warning"></i>';
            case 'consensus': return '<i class="bi bi-people-fill text-success"></i>';
            case 'winner': return '<i class="bi bi-trophy-fill text-success"></i>';
            case 'cancel': return '<i class="bi bi-x-circle-fill text-danger"></i>';
            case 'cancel_request': return '<i class="bi bi-exclamation-circle-fill text-warning"></i>';
            case 'cancel_agree': return '<i class="bi bi-check-circle-fill text-success"></i>';
            case 'cancel_reject': return '<i class="bi bi-x-circle text-danger"></i>';
            case 'start': return '<i class="bi bi-play-circle-fill text-success"></i>';
            case 'poke': return '<i class="bi bi-hand-index-thumb-fill text-info"></i>';
            default: return '<i class="bi bi-dot text-muted"></i>';
        }
    }

    function appendActivity(message, type) {
        const list = document.getElementById('activity-logs-list');
        const empty = document.getElementById('empty-activity');
        if (empty) empty.remove();

        const escapedMessage = message.replace(/</g, "&lt;").replace(/>/g, "&gt;");
        const formattedMessage = escapedMessage.replace(/@([\w\-.]+)/g, '<a href="/user/$1" target="_blank" style="color: #ffdd00; text-decoration: none; font-weight: bold;">@$1</a>');

        const item = document.createElement('div');
        item.className = 'activity-item mb-3 pb-3';
        item.style.borderBottom = '1px solid rgba(255, 255, 255, 0.05)';
        item.innerHTML = `
            <div class="d-flex gap-2 align-items-start">
                <div class="activity-icon-sm mt-1">
                    ${getIconForActivity(type)}
                </div>
                <div>
                    <div style="font-size: 0.85rem; color: #fff;">${formattedMessage}</div>
                    <div style="font-size: 0.7rem; color: #555577;">Just now</div>
                </div>
            </div>
        `;
        list.appendChild(item);
        scrollLogToBottom();
    }

    function scrollLogToBottom() {
        const container = document.querySelector('.activity-log-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    function showNeonNotification(message, type) {
        const toast = document.createElement('div');
        toast.className = `neon-notification neon-notification-${type}`;
        toast.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-lightning-charge-fill"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => toast.classList.add('show'), 100);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 500);
        }, 5000);
    }

    function copyBattleUrl() {
        const copyText = document.getElementById("battle-url");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);

        const btn = event.target;
        const originalText = btn.innerText;
        btn.innerText = "COPIED!";
        setTimeout(() => { btn.innerText = originalText; }, 2000);
    }

    // Elect Marshall Auto-Suggest
    const adjWrapper = document.getElementById('adj-input-wrapper');
    const adjInput = document.getElementById('elect-marshall-input');
    const adjHidden = document.getElementById('adj-hidden-user-id');
    const adjChip = document.getElementById('adj-selected-chip');
    const adjChipText = document.getElementById('adj-chip-text');
    const adjResults = document.getElementById('elect-marshall-results');
    let adjDebounce = null;

    if (adjInput && adjResults) {
        if (adjWrapper) {
            adjWrapper.addEventListener('click', () => {
                if (!adjHidden.value) adjInput.focus();
            });
        }

        adjInput.addEventListener('input', function() {
            clearTimeout(adjDebounce);
            const q = this.value.trim();

            if (q.length < 2) {
                adjResults.classList.add('d-none');
                adjResults.innerHTML = '';
                return;
            }

            adjDebounce = setTimeout(() => {
                fetch(`/search?q=${encodeURIComponent(q)}&battle_id={{$battle->room_id }}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(users => {
                    if (users.length === 0) {
                        adjInput.value = '';
                        adjResults.innerHTML = '<div class="p-2 text-center text-muted small">No players found</div>';
                    } else {
                        adjResults.innerHTML = users.map(u => `
                            <div class="adj-search-item p-2 d-flex align-items-center gap-2" onmousedown="selectMarshall(${u.id}, '${u.username}')" style="cursor: pointer; border-bottom: 1px solid rgba(255, 221, 0, 0.1);">
                                <img src="${u.avatar_url}" alt="${u.username}" style="width: 24px; height: 24px; border-radius: 50%; border: 1px solid #ffdd00;">
                                <span class="text-white">@${u.username}</span>
                            </div>
                        `).join('');
                    }
                    adjResults.classList.remove('d-none');
                });
            }, 300);
        });

        // Clear input on blur if no user is selected
        adjInput.addEventListener('blur', function() {
            setTimeout(() => {
                if (!adjHidden.value) {
                    adjInput.value = '';
                    adjResults.classList.add('d-none');
                }
            }, 150); // Delay to allow onmousedown selection to trigger first
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!adjWrapper.contains(e.target) && !adjResults.contains(e.target)) {
                adjResults.classList.add('d-none');
            }
        });
    }

    window.selectMarshall = function(userId, username) {
        adjHidden.value = userId;
        adjInput.value = '';
        adjInput.classList.add('d-none');
        adjChipText.innerText = username;
        adjChip.classList.remove('d-none');
        adjResults.classList.add('d-none');
    }

    window.clearMarshallSelection = function() {
        adjHidden.value = '';
        adjChip.classList.add('d-none');
        adjInput.classList.remove('d-none');
        adjInput.focus();
    }

    // Invite Player Auto-Suggest
    const inviteWrapper = document.getElementById('invite-input-wrapper');
    const inviteInput = document.getElementById('invite-player-input');
    const inviteHidden = document.getElementById('invite-hidden-username');
    const inviteChip = document.getElementById('invite-selected-chip');
    const inviteChipText = document.getElementById('invite-chip-text');
    const inviteResults = document.getElementById('invite-player-results');
    let inviteDebounce = null;

    if (inviteInput && inviteResults) {
        if (inviteWrapper) {
            inviteWrapper.addEventListener('click', () => {
                if (!inviteHidden.value) inviteInput.focus();
            });
        }

        inviteInput.addEventListener('input', function() {
            clearTimeout(inviteDebounce);
            const q = this.value.trim();

            if (q.length < 2) {
                inviteResults.classList.add('d-none');
                inviteResults.innerHTML = '';
                return;
            }

            inviteDebounce = setTimeout(() => {
                fetch(`/search?q=${encodeURIComponent(q)}&battle_id={{$battle->room_id }}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(users => {
                    if (users.length === 0) {
                        inviteInput.value = '';
                        inviteResults.innerHTML = '<div class="p-2 text-center text-muted small">No players found</div>';
                    } else {
                        inviteResults.innerHTML = users.map(u => `
                            <div class="adj-search-item p-2 d-flex align-items-center gap-2" onmousedown="selectInvitePlayer('${u.username}')" style="cursor: pointer; border-bottom: 1px solid rgba(0, 240, 255, 0.1);">
                                <img src="${u.avatar_url}" alt="${u.username}" style="width: 24px; height: 24px; border-radius: 50%; border: 1px solid #00f0ff;">
                                <span class="text-white">@${u.username}</span>
                            </div>
                        `).join('');
                    }
                    inviteResults.classList.remove('d-none');
                });
            }, 300);
        });

        // Clear input on blur if no user is selected
        inviteInput.addEventListener('blur', function() {
            setTimeout(() => {
                if (!inviteHidden.value) {
                    inviteInput.value = '';
                    inviteResults.classList.add('d-none');
                }
            }, 150); // Delay to allow onmousedown selection to trigger first
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!inviteWrapper.contains(e.target) && !inviteResults.contains(e.target)) {
                inviteResults.classList.add('d-none');
            }
        });
    }

    window.selectInvitePlayer = function(username) {
        inviteHidden.value = username;
        inviteInput.value = '';
        inviteInput.classList.add('d-none');
        inviteChipText.innerText = username;
        inviteChip.classList.remove('d-none');
        inviteResults.classList.add('d-none');
    }

    window.clearInviteSelection = function() {
        inviteHidden.value = '';
        inviteChip.classList.add('d-none');
        inviteInput.classList.remove('d-none');
        inviteInput.focus();
    }

</script>

<style>
    .neon-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: rgba(10, 10, 30, 0.95);
        border: 1px solid #00f0ff;
        border-radius: 12px;
        color: #fff;
        font-family: 'Orbitron', sans-serif;
        font-size: 0.85rem;
        z-index: 9999;
        box-shadow: 0 0 20px rgba(0, 240, 255, 0.3);
        transform: translateX(120%);
        transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        backdrop-filter: blur(10px);
    }
    .neon-notification.show {
        transform: translateX(0);
    }
    .neon-notification-join { border-color: #39ff14; box-shadow: 0 0 20px rgba(57, 255, 20, 0.3); }
    .neon-notification-start { border-color: #ff00ff; box-shadow: 0 0 20px rgba(255, 0, 255, 0.3); }
    .neon-notification-winner { border-color: #ffdd00; box-shadow: 0 0 20px rgba(255, 221, 0, 0.3); }
    .neon-notification-cancel { border-color: #ff0055; box-shadow: 0 0 20px rgba(255, 0, 85, 0.3); }
    .neon-notification-cancel_request { border-color: #ffdd00; box-shadow: 0 0 20px rgba(255, 221, 0, 0.3); }
    .neon-notification-cancel_reject { border-color: #ff0055; box-shadow: 0 0 20px rgba(255, 0, 85, 0.3); }
    .neon-notification-declare { border-color: #00f0ff; box-shadow: 0 0 20px rgba(0, 240, 255, 0.3); }
    .neon-notification-conflict { border-color: #ff0055; box-shadow: 0 0 20px rgba(255, 0, 85, 0.3); }
    .neon-notification-marshall_decision { border-color: #ffdd00; box-shadow: 0 0 20px rgba(255, 221, 0, 0.3); }
    .neon-notification-consensus { border-color: #39ff14; box-shadow: 0 0 20px rgba(57, 255, 20, 0.3); }
    .neon-notification-poke { border-color: #00f0ff; box-shadow: 0 0 20px rgba(0, 240, 255, 0.3); }

    @keyframes pulse-yellow {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.7; transform: scale(0.98); }
        100% { opacity: 1; transform: scale(1); }
    }

    .adj-search-item {
        transition: background 0.2s ease;
    }
    .adj-search-item:hover {
        background: rgba(255, 221, 0, 0.15) !important;
    }
</style>
@endsection