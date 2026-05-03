@extends('layouts.app')
@section('title', 'Battle Room #' . $battle->id)
@section('content')


<style>
    .team-name-container {
        width: 100%;
        overflow: hidden;
        white-space: nowrap;
        position: relative;
    }
    .team-name-scroll {
        display: inline-block;
        white-space: nowrap;
        animation: team-marquee 15s linear infinite;
    }
    .team-name-scroll:hover {
        animation-play-state: paused;
    }
    @keyframes team-marquee {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
</style>

<div class="team-battle-room"  style="overflow: visible;">
    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 orbitron" role="alert" style="background: rgba(220, 53, 69, 0.1); border: 1px solid #dc3545; color: #ff8888;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 orbitron" role="alert" style="background: rgba(57, 255, 20, 0.1); border: 1px solid #39ff14; color: #39ff14;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Team A Column -->
        <div class="col-6" style="min-width: 0;">
            <div class="text-center mb-4 team-name-container"
                 x-data="{ 
                    overflowing: false,
                    checkOverflow() {
                        if(this.$refs.nakedA) {
                            this.overflowing = this.$refs.nakedA.scrollWidth > this.$el.clientWidth;
                        }
                    }
                 }" 
                 x-init="setTimeout(() => checkOverflow(), 200)"
                 @resize.window="checkOverflow()">
                <div :class="{ 'team-name-scroll': overflowing }">
                    <h4 class="orbitron text-cyan mb-0 d-inline-block" :class="{ 'pe-5': overflowing }" title="{{ $battle->team_name_a }}">
                        <span x-ref="nakedA">{{ $battle->team_name_a }}</span>
                    </h4>
                    <h4 x-show="overflowing" class="orbitron text-cyan mb-0 d-inline-block pe-5" title="{{ $battle->team_name_a }}">
                        {{ $battle->team_name_a }}
                    </h4>
                </div>
            </div>

            <div class="d-flex flex-column gap-4 align-items-center">
                @for($i = 1; $i <= $battle->no_players_per_team; $i++)
                    @php 
                        $u = \App\Models\User::find($battle->{"team_a_user_{$i}"});
                        $c = \App\Models\DigitalCard::find($battle->{"team_a_card_{$i}"});
                        $isMe = $u && $u->id == Auth::id();
                        $isFinal = $battle->status == 'completed';
                        $snapshot = $battle->team_a_card_data[$i] ?? null;
                        
                        $cardClass = "";
                        if ($isMe && $battle->status != 'completed') {
                            $cardClass = "current-player-slot-a";
                        }
                        
                        $cardStyle = "";
                        if ($isFinal) {
                            if ($battle->winner_team == 'A') {
                                $cardStyle = "box-shadow: 0 0 30px rgba(255, 221, 0, 0.6); border-radius: 16px; transform: scale(1.02); transition: all 0.3s ease;";
                            } else {
                                $cardStyle = "opacity: 0.5; filter: grayscale(80%); transition: all 0.3s ease;";
                            }
                        }
                    @endphp
                    <div class="w-100" style="max-width: 350px;">
                        @if($u && $c)
                            <div class="mb-2 text-center text-truncate">
                                <span class="fw-bold @if($isMe) text-cyan @endif">
                                    @if($i == 1)
                                    👑
                                    @endif
                                    {{ $isMe ? 'YOU' : $u->username }}</span>
                            </div>
                            <div class="{{ $cardClass }}" style="{{ $cardStyle }}">
                                <x-digital-card 
                                    id="card_a_{{ $i }}_{{ $c->id ?? 'none' }}"
                                    mode="thumbnail"
                                    fullscreen="true"
                                    :title="$c->template->card_title"
                                    :game="$c->template->gameTitle->title ?? 'GAME'"
                                    :creator="$c->originalOwner->username ?? 'Creator'"
                                    :quote="$c->template->quote"
                                    :image="$c->template->display_photo"
                                    :imagePositionY="$c->template->image_position_y ?? 50"
                                    :backgroundColor="$c->template->background_color"
                                    :borderColor="$c->template->border_color"
                                    :sectionColor="$c->template->section_color"
                                    :primaryTextColor="$c->template->primary_text_color"
                                    :secondaryTextColor="$c->template->secondary_text_color"
                                    :wins="$snapshot ? $snapshot['wins'] : $c->wins"
                                    :losses="$snapshot ? $snapshot['losses'] : $c->losses"
                                    :integrityStat="$snapshot ? $snapshot['integrity_stat'] : $c->integrity_stat"
                                    :lifePoints="$snapshot ? $snapshot['life_points'] : $c->life_points"
                                    :status="$snapshot ? $snapshot['status'] : $c->status"
                                    :rankLevel="$snapshot ? $snapshot['level'] : $c->level"
                                    :serialNumber="$c->serial_number"
                                    :rarity="$snapshot ? $snapshot['rarity'] : $c->rarity"
                                />
                            </div>
                        @else
                            <div class="mb-2 text-center text-truncate">
                                <span class="fw-bold text-muted">
                                    @if($i == 1)
                                    👑
                                    @endif
                                    ----</span>
                            </div>
                            <div class="empty-card-slot d-flex flex-column align-items-center justify-content-center p-3 rounded" style="border: 2px dashed rgba(0, 240, 255, 0.4); background: rgba(0, 240, 255, 0.05); aspect-ratio: 350 / 490; width: 100%;">
                                <div class="orbitron text-muted mb-3 fs-5">SLOT {{ $i }}</div>
                                @if($battle->status == 'pending' && Auth::id() != $battle->team_a_user_1)
                                    <button type="button" class="btn btn-outline-cyan btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal" wire:click.prevent="$set('joiningTeam', 'A'); $set('pairingSlot', {{ $i }})">JOIN</button>
                                @endif
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        <!-- Team B Column -->
        <div class="col-6" style="min-width: 0;">
            <div class="text-center mb-4 team-name-container"
                 x-data="{ 
                    overflowing: false,
                    checkOverflow() {
                        if(this.$refs.nakedB) {
                            this.overflowing = this.$refs.nakedB.scrollWidth > this.$el.clientWidth;
                        }
                    }
                 }" 
                 x-init="setTimeout(() => checkOverflow(), 200)"
                 @resize.window="checkOverflow()">
                <div :class="{ 'team-name-scroll': overflowing }">
                    <h4 class="orbitron text-magenta mb-0 d-inline-block" :class="{ 'pe-5': overflowing }" title="{{ $battle->team_name_b }}">
                        <span x-ref="nakedB">{{ $battle->team_name_b }}</span>
                    </h4>
                    <h4 x-show="overflowing" class="orbitron text-magenta mb-0 d-inline-block pe-5" title="{{ $battle->team_name_b }}">
                        {{ $battle->team_name_b }}
                    </h4>
                </div>
            </div>

            <div class="d-flex flex-column gap-4 align-items-center">
                @for($i = 1; $i <= $battle->no_players_per_team; $i++)
                    @php 
                        $u = \App\Models\User::find($battle->{"team_b_user_{$i}"});
                        $c = \App\Models\DigitalCard::find($battle->{"team_b_card_{$i}"});
                        $isMe = $u && $u->id == Auth::id();
                        $isFinal = $battle->status == 'completed';
                        $snapshot = $battle->team_b_card_data[$i] ?? null;
                        
                        $cardClass = "";
                        if ($isMe && $battle->status != 'completed') {
                            $cardClass = "current-player-slot-b";
                        }
                        
                        $cardStyle = "";
                        if ($isFinal) {
                            if ($battle->winner_team == 'B') {
                                $cardStyle = "box-shadow: 0 0 30px rgba(255, 221, 0, 0.6); border-radius: 16px; transform: scale(1.02); transition: all 0.3s ease;";
                            } else {
                                $cardStyle = "opacity: 0.5; filter: grayscale(80%); transition: all 0.3s ease;";
                            }
                        }
                    @endphp
                    <div class="w-100" style="max-width: 350px;">
                        @if($u && $c)
                            <div class="mb-2 text-center text-truncate">
                                <span class="fw-bold @if($isMe) text-magenta @endif">
                                    @if($i == 1)
                                    👑
                                    @endif
                                    {{ $isMe ? 'YOU' : $u->username }}</span>
                            </div>
                            <div class="{{ $cardClass }}" style="{{ $cardStyle }}">
                                <x-digital-card 
                                    id="card_b_{{ $i }}_{{ $c->id ?? 'none' }}"
                                    mode="thumbnail"
                                    fullscreen="true"
                                    :title="$c->template->card_title"
                                    :game="$c->template->gameTitle->title ?? 'GAME'"
                                    :creator="$c->originalOwner->username ?? 'Creator'"
                                    :quote="$c->template->quote"
                                    :image="$c->template->display_photo"
                                    :imagePositionY="$c->template->image_position_y ?? 50"
                                    :backgroundColor="$c->template->background_color"
                                    :borderColor="$c->template->border_color"
                                    :sectionColor="$c->template->section_color"
                                    :primaryTextColor="$c->template->primary_text_color"
                                    :secondaryTextColor="$c->template->secondary_text_color"
                                    :wins="$snapshot ? $snapshot['wins'] : $c->wins"
                                    :losses="$snapshot ? $snapshot['losses'] : $c->losses"
                                    :integrityStat="$snapshot ? $snapshot['integrity_stat'] : $c->integrity_stat"
                                    :lifePoints="$snapshot ? $snapshot['life_points'] : $c->life_points"
                                    :status="$snapshot ? $snapshot['status'] : $c->status"
                                    :rankLevel="$snapshot ? $snapshot['level'] : $c->level"
                                    :serialNumber="$c->serial_number"
                                    :rarity="$snapshot ? $snapshot['rarity'] : $c->rarity"
                                />
                            </div>
                        @else
                            <div class="mb-2 text-center text-truncate">
                                <span class="fw-bold text-muted">
                                    @if($i == 1)
                                    👑
                                    @endif
                                    ----</span>
                            </div>
                            <div class="empty-card-slot d-flex flex-column align-items-center justify-content-center p-3 rounded" style="border: 2px dashed rgba(255, 0, 255, 0.4); background: rgba(255, 0, 255, 0.05); aspect-ratio: 350 / 490; width: 100%;">
                                <div class="orbitron text-muted mb-3 fs-5">SLOT {{ $i }}</div>
                                @if($battle->status == 'pending' && Auth::id() != $battle->team_a_user_1)
                                    <button type="button" class="btn btn-outline-magenta btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal" wire:click.prevent="$set('joiningTeam', 'B'); $set('pairingSlot', {{ $i }})">JOIN</button>
                                @endif
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Battle Controls / Actions & Activity Log -->
    <div class="row justify-content-center mt-4">
        <div class="col-lg-7">
            <livewire:battle-status :battle="$battle" />

<!-- Participant Battle Actions -->
            @if($isParticipant && Auth::id() != $battle->marshall_id)
                    <div class="mt-4 mb-5 pt-4" style="border-top: 1px solid rgba(0, 240, 255, 0.1);">
                        <h5 class="section-header mb-3">
                            <i class="bi bi-gear-wide-connected section-icon" style="color: #00f0ff;"></i> BATTLE ACTIONS
                        </h5>
                        
                        <div id="actions-container" class="d-flex gap-3 flex-wrap align-items-center">
                            @php
                                $userTeam = '';
                                for ($i = 1; $i <= $battle->no_players_per_team; $i++) {
                                    if ($battle->{"team_a_user_{$i}"} == Auth::id()) { $userTeam = 'A'; break; }
                                    if ($battle->{"team_b_user_{$i}"} == Auth::id()) { $userTeam = 'B'; break; }
                                }
                                $isLeaderA = Auth::id() == $battle->team_a_user_1;
                                $isLeaderB = Auth::id() == $battle->team_b_user_1;
                            @endphp

                            @if($isLeaderA || $isLeaderB)
                                @if($battle->status == 'active' || $battle->status == 'failed')
                                    @php
                                        $winTeam = $userTeam;
                                        $lostTeam = $userTeam == 'A' ? 'B' : 'A';
                                        
                                        $myVote = null;
                                        if ($isLeaderA) $myVote = $battle->team_a_declare_win;
                                        if ($isLeaderB) $myVote = $battle->team_b_declare_win;
                                    @endphp
                                    
                                    @if(!$myVote || $battle->status == 'failed')
                                        @php
                                            // Determine styles based on what was voted
                                            $votedWin = ($myVote == $winTeam);
                                            $votedLost = ($myVote == $lostTeam);
                                            
                                            $winClass = $votedWin ? 'btn-neon' : ($myVote ? 'btn-outline-info text-muted' : 'btn-neon');
                                            $winStyle = $votedWin ? 'box-shadow: 0 0 20px #00f0ff; border: 2px solid white;' : '';
                                            $winIcon = $votedWin ? 'bi-check-circle-fill' : 'bi-trophy';
                                            $winText = $votedWin ? 'VOTED WIN' : 'DECLARE WIN';

                                            $lostClass = $votedLost ? 'btn-neon-danger' : ($myVote ? 'btn-outline-danger text-muted' : 'btn-neon-danger');
                                            $lostStyle = $votedLost ? 'box-shadow: 0 0 20px #ff0000; border: 2px solid white;' : '';
                                            $lostIcon = $votedLost ? 'bi-check-circle-fill' : 'bi-x-circle';
                                            $lostText = $votedLost ? 'VOTED LOST' : 'DECLARE LOST';
                                        @endphp
                                        
                                        <button class="btn {{ $winClass }} btn-sm" x-data x-on:click="window.neonConfirm('Are you sure you want to declare WIN?').then(c => { if(c) $wire.declareWin('{{ $winTeam }}') })" wire:loading.attr="disabled" style="{{ $winStyle }}">
                                            <i class="bi {{ $winIcon }}"></i> {{ $winText }}
                                        </button>
                                        <button class="btn {{ $lostClass }} btn-sm" x-data x-on:click="window.neonConfirm('Are you sure you want to declare LOST?').then(c => { if(c) $wire.declareWin('{{ $lostTeam }}') })" wire:loading.attr="disabled" style="{{ $lostStyle }}">
                                            <i class="bi {{ $lostIcon }}"></i> {{ $lostText }}
                                        </button>
                                    @else
                                        <div class="alert alert-info py-2 small mb-0 text-center" style="border: 1px solid #00f0ff; background: rgba(0, 240, 255, 0.1); color: #00f0ff; width: 100%; max-width: 400px;">
                                            <i class="bi bi-hourglass-split"></i> You declared {{ $myVote == $userTeam ? 'a win' : 'a loss' }}. Waiting for opponent...
                                        </div>
                                    @endif
                                    
                                    @php
                                        $hasRequestedCancel = ($isLeaderA && $battle->team_a_cancel_flag) || 
                                                            ($isLeaderB && $battle->team_b_cancel_flag);
                                    @endphp
                                    @if(!$hasRequestedCancel)
                                        <button class="btn btn-outline-danger btn-sm" wire:click="cancelBattle">
                                            <i class="bi bi-x-circle"></i> REQUEST CANCEL
                                        </button>
                                    @endif
                                @endif

                                @if($battle->status != 'completed' && $battle->status != 'cancelled')
                                    @if($isLeaderA || $isLeaderB)
                                        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#renameTeamModal" onclick="document.getElementById('renameTeamInput').value='{{ $isLeaderA ? $battle->team_name_a : $battle->team_name_b }}'; document.getElementById('renameTeamVal').value='{{ $isLeaderA ? "A" : "B" }}'; document.getElementById('rename_team_name').innerText='{{ $isLeaderA ? "A" : "B" }}';">
                                            <i class="bi bi-pencil-square"></i> RENAME TEAM
                                        </button>
                                    @endif
                                @endif
                                
                                @if(!$battle->marshall_id && in_array($battle->status, ['pending', 'ready', 'active', 'failed']))
                                    <button type="button" class="btn btn-neon btn-sm" style="border-color: #ffdd00; color: #ffdd00;" data-bs-toggle="modal" data-bs-target="#electMarshallModal">
                                        <i class="bi bi-shield-fill-check"></i> 
                                        {{ ($isLeaderA ? $battle->team_a_marshall_elect : $battle->team_b_marshall_elect) ? 'CHANGE ELECTION' : 'ELECT MARSHALL' }}
                                    </button>
                                @endif
                                
                                @if($battle->status == 'pending' && $isLeaderA)
                                    @if($battle->is_full && $battle->team_b_ready)
                                        <form action="{{ route('battles.action.start', $battle) }}" method="POST" class="d-inline w-100">@csrf <button type="submit" class="btn btn-neon-lime w-100" style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);"><i class="bi bi-play-fill"></i> START MATCH</button></form>
                                    @endif
                                    @if(!$battle->is_full)
                                        <button type="button" class="btn btn-neon" data-bs-toggle="modal" data-bs-target="#invitePlayerModal">
                                            <i class="bi bi-person-plus-fill"></i> INVITE PLAYERS
                                        </button>
                                    @endif
                                    <form action="{{ route('battles.action.cancel', $battle) }}" method="POST" class="d-inline w-100">@csrf <button type="submit" class="btn btn-neon-danger w-100"><i class="bi bi-x-circle"></i> CANCEL BATTLE</button></form>
                                @elseif($battle->status == 'pending' && $isLeaderB)
                                    @if($battle->is_team_b_full && !$battle->team_b_ready)
                                        <form action="{{ route('battles.action.ready', $battle) }}" method="POST" class="d-inline w-100">@csrf <button type="submit" class="btn btn-neon-lime w-100" style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);"><i class="bi bi-check2-all"></i> READY</button></form>
                                    @endif
                                    @if(!$battle->is_full)
                                        <button type="button" class="btn btn-neon" data-bs-toggle="modal" data-bs-target="#invitePlayerModal">
                                            <i class="bi bi-person-plus-fill"></i> INVITE PLAYERS
                                        </button>
                                    @endif
                                @endif
                            @endif

                            @if($battle->status == 'pending' && Auth::id() != $battle->team_a_user_1)
                                <form action="{{ route('battles.action.standup', $battle) }}" method="POST" class="d-inline w-100">@csrf <button type="submit" class="btn btn-outline-warning w-100"><i class="bi bi-box-arrow-right"></i> STAND UP</button></form>
                            @endif

                            <!-- Share QR (Mobile Only) -->
                            <button type="button" class="btn btn-neon d-md-none" style="border-color: #39ff14; color: #39ff14;" data-bs-toggle="modal" data-bs-target="#shareQRModal">
                                <i class="bi bi-qr-code"></i> SHARE QR
                            </button>
                        </div>
                    </div>
            @elseif(Auth::id() == $battle->marshall_id && $battle->status == 'active')
                @if(Auth::id())
                    <div class="mt-4 mb-5 pt-4" style="border-top: 1px solid rgba(255, 221, 0, 0.1);">
                        <h5 class="section-header mb-3">
                            <i class="bi bi-gear-wide-connected section-icon" style="color: #ffdd00;"></i> MARSHALL ACTIONS
                        </h5>
                        <div id="actions-container" class="d-flex gap-3 flex-wrap align-items-center">
                            <button type="button" class="btn btn-neon btn-sm" x-data x-on:click="window.neonConfirm('As Marshall, are you sure you want to officially declare TEAM A as the winner?').then(c => { if(c) $wire.declareWin('A') })">TEAM A WON</button>
                            <button type="button" class="btn btn-neon-magenta btn-sm" x-data x-on:click="window.neonConfirm('As Marshall, are you sure you want to officially declare TEAM B as the winner?').then(c => { if(c) $wire.declareWin('B') })">TEAM B WON</button>
                            <button type="button" class="btn btn-neon-danger btn-sm" x-data x-on:click="window.neonConfirm('Are you sure you want to CANCEL this match? No cards will be transferred.').then(c => { if(c) $wire.cancelBattle() })">CANCEL MATCH</button>
                        </div>
                    </div>
                @endif
            @endif
        </div>
        
        <div class="col-lg-3 mt-4 mt-lg-0"><livewire:battle-activity-log :battle="$battle" /></div>

<!-- Join Modal (Simulated) -->
    
        <div class="modal fade" id="joinModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" style="background: rgba(0, 0, 0, 0.8);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="custom-modal p-4 neon-card" style="max-width: 800px; width: 95%;">
                <h4 class="orbitron text-cyan mb-4 text-center">JOIN TEAM <span id="join_team_name"></span></h4>
<form action="{{ route('battles.action.join', $battle) }}" method="POST" id="joinForm">@csrf <input type="hidden" name="joiningTeam" id="joiningTeam" value=""><input type="hidden" name="pairingSlot" id="pairingSlot" value=""><input type="hidden" name="selectedCardId" id="selectedCardId" value="">
<label class="form-label small text-center w-100 mb-3" style="color: #39ff14;"><i class="bi bi-suit-diamond-fill"></i> SELECT YOUR CARD TO BET</label>
                    
                    @if($myEligibleCards->isEmpty())
                        <div class="alert alert-warning text-center">
                            You don't have any eligible cards with life points for this game title.
                        </div>
                    @else
                        <div id="joinCardCarousel" class="carousel slide px-md-5" data-bs-ride="false">
                            <div class="carousel-inner p-2">
                                @foreach($myEligibleCards->chunk(3) as $chunkIndex => $chunk)
                                    <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
                                        <div class="row g-3 justify-content-center">
                                            @foreach($chunk as $card)
                                                <div class="col-md-4 col-6">
                                                    <div class="selectable-card" onclick="document.querySelectorAll('.selectable-card').forEach(e=>e.classList.remove('selected')); this.classList.add('selected'); document.getElementById('selectedCardId').value='{{$card->id}}';" style="cursor: pointer;">
                                                        <div class="card-img-wrapper" style="position: relative; cursor: pointer;">
                                                            <div style="pointer-events: none;">
                                                                <x-digital-card 
                                                                    id="card_join_{{ $card->id }}"
                                                                    mode="thumbnail"
                                                                    :title="$card->template->card_title"
                                                                    :game="$card->template->gameTitle->title ?? 'GAME'"
                                                                    :creator="$card->originalOwner->username ?? 'Creator'"
                                                                    :quote="$card->template->quote"
                                                                    :image="$card->template->display_photo"
                                                                    :imagePositionY="$card->template->image_position_y ?? 50"
                                                                    :backgroundColor="$card->template->background_color"
                                                                    :borderColor="$card->template->border_color"
                                                                    :sectionColor="$card->template->section_color"
                                                                    :primaryTextColor="$card->template->primary_text_color"
                                                                    :secondaryTextColor="$card->template->secondary_text_color"
                                                                    :wins="$card->wins"
                                                                    :losses="$card->losses"
                                                                    :integrityStat="$card->integrity_stat"
                                                                    :lifePoints="$card->life_points"
                                                                    :status="$card->status"
                                                                    :rankLevel="$card->level"
                                                                    :serialNumber="$card->serial_number"
                                                                    :rarity="$card->rarity"
                                                                />
                                                            </div>
                                                            <button type="button" class="btn btn-sm btn-dark position-absolute" style="top: 5px; right: 5px; z-index: 10; background: rgba(0,0,0,0.6); border-color: rgba(0, 240, 255, 0.5); color: #00f0ff;" data-bs-toggle="modal" data-bs-target="#modal_card_join_{{ $card->id }}" onclick="event.stopPropagation();">
                                                                <i class="bi bi-arrows-fullscreen"></i>
                                                            </button>
                                                            
                                                        </div>
                                                        <div class="mt-2 text-center text-truncate small fw-bold">{{ $card->template->card_title }}</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            @if($myEligibleCards->count() > 3)
                                <button class="carousel-control-prev" type="button" data-bs-target="#joinCardCarousel" data-bs-slide="prev" style="width: 5%;">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#joinCardCarousel" data-bs-slide="next" style="width: 5%;">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            @endif
                        </div>
                    @endif
                    @error('selectedCardId') <div class="text-danger small mt-2 text-center">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-3 mt-4">
                    <button type="button" class="btn btn-outline-secondary w-50 py-2" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-neon w-50 py-2 orbitron">CONFIRM JOIN</button></form>
                </div>
            </div>
        </div>

        <!-- Rename Team Modal -->
    <div class="modal fade" id="renameTeamModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" style="background: rgba(0, 0, 0, 0.8);">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('battles.action.rename', $battle) }}" method="POST" class="w-100">
            @csrf
            <input type="hidden" name="team" id="renameTeamVal" value="">
            <div class="modal-content p-4 neon-card" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #00f0ff; backdrop-filter: blur(20px);">
                <h5 class="orbitron text-cyan mb-4 text-center">RENAME TEAM <span id="rename_team_name"></span></h5>
                <div class="mb-4">
                    <input type="text" name="name" id="renameTeamInput" class="form-control bg-dark text-white border-cyan text-center orbitron" placeholder="Enter new team name" required>
                </div>
                <div class="d-flex gap-3">
                    <button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-neon w-50 orbitron">SAVE</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    <!-- Elect Marshall Modal -->
    <div class="modal fade" id="electMarshallModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" style="background: rgba(0, 0, 0, 0.8);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #ffdd00; backdrop-filter: blur(20px);">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" style="color: #ffdd00; font-family: 'Orbitron', sans-serif;">ELECT MARSHALL</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3 position-relative">
                        <label class="form-label">MARSHALL USERNAME</label>
                        @php
                            $existingUsername = '';
                            if (Auth::id() === $battle->team_a_user_1 && $battle->team_a_marshall_elect) {
                                $existingUsername = \App\Models\User::find($battle->team_a_marshall_elect)?->username;
                            } elseif (Auth::id() === $battle->team_b_user_1 && $battle->team_b_marshall_elect) {
                                $existingUsername = \App\Models\User::find($battle->team_b_marshall_elect)?->username;
                            }
                        @endphp
                        <div class="form-control d-flex align-items-center p-1" style="min-height: 42px;">
                            @if($marshallNomineeId)
                                <span class="badge d-flex align-items-center gap-2 p-2" style="background: rgba(255,221,0,0.2); border: 1px solid #ffdd00; color: #ffdd00; font-size: 0.9rem;">
                                    <i class="bi bi-person-fill"></i> 
                                    <span>{{ \App\Models\User::find($marshallNomineeId)?->username }}</span>
                                    <i class="bi bi-x-circle-fill ms-2" style="cursor: pointer;" wire:click="clearMarshallSelection()"></i>
                                </span>
                            @else
                                <input type="text" class="border-0 bg-transparent text-white flex-grow-1 px-2" placeholder="{{ $existingUsername ? 'Currently: ' . $existingUsername : 'Search username...' }}" autocomplete="off" style="outline: none; box-shadow: none;">
                            @endif
                        </div>
                        
                        @if(count($marshallSearchResults) > 0 && !$marshallNomineeId)
                            <div class="position-absolute w-100 mt-1" style="z-index: 1050; max-height: 200px; overflow-y: auto; background: rgba(10, 10, 30, 0.95); border: 1px solid #ffdd00; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                                @foreach($marshallSearchResults as $user)
                                    <div class="p-2 d-flex align-items-center gap-2" wire:click="selectMarshallNominee({{ $user->id }}, '{{ $user->username }}')" style="cursor: pointer; border-bottom: 1px solid rgba(255, 221, 0, 0.1);">
                                        <img src="{{ $user->avatar_url ?? asset('img/default-avatar.png') }}" alt="{{ $user->username }}" style="width: 24px; height: 24px; border-radius: 50%; border: 1px solid #ffdd00;">
                                        <span class="text-white">{{ '@' . $user->username }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @elseif(strlen($marshallSearchQuery) >= 2 && !$marshallNomineeId)
                            <div class="position-absolute w-100 mt-1 p-2 text-center text-muted small" style="z-index: 1050; background: rgba(10, 10, 30, 0.95); border: 1px solid #ffdd00; border-radius: 4px;">
                                No players found
                            </div>
                        @endif
                    </div>
                    <p class="text-muted small">Both team leaders must elect the same user for them to be designated as the marshall.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-neon w-100" style="border-color: #ffdd00; color: #ffdd00;" wire:click="electMarshall()" data-bs-dismiss="modal">ELECT USER</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Share QR Modal -->
    <div class="modal fade" id="shareQRModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" style="background: rgba(0, 0, 0, 0.8);">
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
                    <p class="text-muted small">Show this QR code to your opponents or teammates to let them join this battle room.</p>
                    <div class="mt-3">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control bg-dark border-secondary text-light" value="{{ route('battles.room', $battle) }}" id="battle-url" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyBattleUrl()">COPY</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invite Player Modal -->
    <div class="modal fade" id="invitePlayerModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" style="background: rgba(0, 0, 0, 0.8);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #00f0ff; backdrop-filter: blur(20px);">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title neon-text">INVITE PLAYERS</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3 position-relative">
                        <label class="form-label">PLAYER USERNAME</label>
                        <div class="form-control d-flex align-items-center p-1" style="min-height: 42px;">
                            @if($inviteNomineeId)
                                <span class="badge d-flex align-items-center gap-2 p-2" style="background: rgba(0,240,255,0.2); border: 1px solid #00f0ff; color: #00f0ff; font-size: 0.9rem;">
                                    <i class="bi bi-person-fill"></i> 
                                    <span>{{ \App\Models\User::find($inviteNomineeId)?->username }}</span>
                                    <i class="bi bi-x-circle-fill ms-2" style="cursor: pointer;" wire:click="clearInviteSelection()"></i>
                                </span>
                            @else
                                <input type="text" class="border-0 bg-transparent text-white flex-grow-1 px-2" placeholder="Search username..." autocomplete="off" style="outline: none; box-shadow: none;">
                            @endif
                        </div>
                        
                        @if(count($inviteSearchResults) > 0 && !$inviteNomineeId)
                            <div class="position-absolute w-100 mt-1" style="z-index: 1050; max-height: 200px; overflow-y: auto; background: rgba(10, 10, 30, 0.95); border: 1px solid #00f0ff; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                                @foreach($inviteSearchResults as $user)
                                    <div class="p-2 d-flex align-items-center gap-2" wire:click="selectInviteNominee({{ $user['id'] }}, '{{ $user['username'] }}')" style="cursor: pointer; border-bottom: 1px solid rgba(0, 240, 255, 0.1);">
                                        <img src="{{ $user['avatar_url'] ?? asset('img/default-avatar.png') }}" alt="{{ $user['username'] }}" style="width: 24px; height: 24px; border-radius: 50%; border: 1px solid #00f0ff;">
                                        <span class="text-white">{{ '@' . $user['username'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @elseif(strlen($inviteSearchQuery) >= 2 && !$inviteNomineeId)
                            <div class="position-absolute w-100 mt-1 p-2 text-center text-muted small" style="z-index: 1050; background: rgba(10, 10, 30, 0.95); border: 1px solid #00f0ff; border-radius: 4px;">
                                No players found
                            </div>
                        @endif
                    </div>
                    <p class="text-muted small">Invited players will receive a notification to join this battle room.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-neon w-100" wire:click="sendInvite()" data-bs-dismiss="modal" @if(!$inviteNomineeId) disabled @endif>SEND INVITE</button>
                </div>
            </div>
        </div>
    </div>

        <!-- Cancellation Request Modal -->
    @php
        $showCancelModal = false;
        $requesterName = '';
        if ($battle->status !== 'cancelled' && $battle->status !== 'completed') {
            if ($battle->team_a_cancel_flag && Auth::id() == $battle->team_b_user_1) {
                $showCancelModal = true;
                $requesterName = \App\Models\User::find($battle->team_a_user_1)?->username ?? 'Team A Leader';
            } elseif ($battle->team_b_cancel_flag && Auth::id() == $battle->team_a_user_1) {
                $showCancelModal = true;
                $requesterName = \App\Models\User::find($battle->team_b_user_1)?->username ?? 'Team B Leader';
            }
        }
    @endphp

    @if($showCancelModal)
    <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0, 0, 0, 0.8);">
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
                        <form action="{{ route('battles.action.respond_cancel', $battle) }}" method="POST" class="w-100">
                            @csrf <input type="hidden" name="agreed" value="1">
                            <button type="submit" class="btn btn-neon-magenta w-100"><i class="bi bi-check-lg"></i> AGREE & CANCEL</button>
                        </form>
                        <form action="{{ route('battles.action.respond_cancel', $battle) }}" method="POST" class="w-100">
                            @csrf <input type="hidden" name="agreed" value="0">
                            <button type="submit" class="btn btn-outline-secondary w-100" style="border-color: #555;"><i class="bi bi-x-lg"></i> REJECT</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
        @keyframes pulse-cyan {
            0% { box-shadow: 0 0 10px rgba(0, 240, 255, 0.6), 0 0 20px rgba(0, 240, 255, 0.4); }
            50% { box-shadow: 0 0 25px rgba(0, 240, 255, 1), 0 0 40px rgba(0, 240, 255, 0.8); }
            100% { box-shadow: 0 0 10px rgba(0, 240, 255, 0.6), 0 0 20px rgba(0, 240, 255, 0.4); }
        }
        @keyframes pulse-magenta {
            0% { box-shadow: 0 0 10px rgba(255, 0, 255, 0.6), 0 0 20px rgba(255, 0, 255, 0.4); }
            50% { box-shadow: 0 0 25px rgba(255, 0, 255, 1), 0 0 40px rgba(255, 0, 255, 0.8); }
            100% { box-shadow: 0 0 10px rgba(255, 0, 255, 0.6), 0 0 20px rgba(255, 0, 255, 0.4); }
        }
        .current-player-slot-a {
            animation: pulse-cyan 2s infinite;
            border: 2px solid #00f0ff;
            border-radius: 12px;
            position: relative;
            z-index: 10;
            cursor: pointer;
        }
        .current-player-slot-b {
            animation: pulse-magenta 2s infinite;
            border: 2px solid #ff00ff;
            border-radius: 12px;
            position: relative;
            z-index: 10;
            cursor: pointer;
        }
        .custom-modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(5px);
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .custom-modal {
            background: #0a0a1a;
            border: 1px solid rgba(0,240,255,0.3);
            box-shadow: 0 0 30px rgba(0,240,255,0.2);
        }
        .selectable-card {
            cursor: pointer;
            border: 2px solid transparent;
            padding: 2px;
            border-radius: 5px;
        }
        .selectable-card.selected {
            border-color: #00f0ff;
            background: rgba(0,240,255,0.1);
        }
        .cursor-pointer { cursor: pointer; }
        .activity-log-container::-webkit-scrollbar {
            width: 4px;
        }
        .activity-log-container::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.2);
        }
        .activity-log-container::-webkit-scrollbar-thumb {
            background: rgba(0,240,255,0.3);
            border-radius: 2px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new MutationObserver((mutations) => {
                let shouldInit = false;
                for (let mutation of mutations) {
                    if (mutation.addedNodes.length > 0) {
                        mutation.addedNodes.forEach(node => {
                            if (node.querySelectorAll) {
                                node.querySelectorAll('canvas[data-card-options]').forEach(canvas => {
                                    if (typeof DigitalCardRenderer !== 'undefined' && !canvas.dataset.initialized) {
                                        try {
                                            const renderer = new DigitalCardRenderer(canvas.id);
                                            const options = JSON.parse(canvas.getAttribute('data-card-options'));
                                            renderer.draw(options);
                                            canvas.dataset.initialized = 'true';
                                        } catch (e) {
                                            console.error("Failed to init card via observer", e);
                                        }
                                    }
                                });
                            }
                        });
                    }
                }
            });

            const roomContainer = document.querySelector('.team-battle-room');
            if (roomContainer) {
                observer.observe(roomContainer, { childList: true, subtree: true });
            }
        });

        function copyBattleUrl() {
            const copyText = document.getElementById("battle-url");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value).then(() => {
                if (typeof window.neonAlert === 'function') {
                    window.neonAlert('Battle URL copied to clipboard!', 'LINK COPIED');
                } else {
                    alert('Battle URL copied!');
                }
            });
        }
    </script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if(window.Echo) {
            window.Echo.channel('battle.{{ $battle->id }}')
                .listen('BattleUpdated', (e) => {
                    window.location.reload();
                });
        }
    });
</script>
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('show-join-modal', () => {
            const el = document.getElementById('joinModal');
            if (el) {
                const modal = new bootstrap.Modal(el);
                modal.show();
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if(window.Echo) {
            window.Echo.channel('battle.{{ $battle->id }}')
                .listen('BattleUpdated', (e) => {
                    window.location.reload();
                });
        }
    });
</script>
</div>

@endsection
