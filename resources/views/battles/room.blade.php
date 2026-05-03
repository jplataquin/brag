@extends('layouts.app')
@section('title', 'Battle Room #' . $battle->id)
@section('content')
<div>


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
         <div class="col-5">
            <div class="text-center mb-2">
                <h2 class="orbitron neon-text mb-0 w-100">
                   A
                </h4>
            </div>
             <div class="text-center mb-4">
              
                <h4 class="orbitron text-cyan mb-0 text-truncate w-100" title="{{ $battle->team_name_a }}">
                    <span x-ref="nakedA">{{ $battle->team_name_a }}</span>
                </h4>
            </div>

        </div>
        <div class="col-2">
             <div class="text-center mb-2">
                <h6 class="orbitron neon-text-magenta mb-0 w-100">
                   VS
                </h6>
            </div>
        </div>
        <div class="col-5">
            <div class="text-center mb-2">
                <h2 class="orbitron neon-text mb-0 w-100">
                    B
                </h2>
            </div>
            <div class="text-center mb-4">
                
                <h4 class="orbitron text-magenta mb-0 text-truncate w-100" title="{{ $battle->team_name_b }}">
                    <span x-ref="nakedB">{{ $battle->team_name_b }}</span>
                </h4>
            </div>
        </div>
    </div>
    <div class="row g-4">
        <!-- Team A Column -->
        <div class="col-6" style="min-width: 0;">
           
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
                    <div class="w-100 slot-container" id="slot-container-A-{{ $i }}" style="max-width: 350px;">
                        @include('battles.partials.single-slot', ['team' => 'A', 'slot' => $i, 'u' => $u, 'c' => $c, 'isFinal' => $battle->status == 'completed', 'isMe' => $u && $u->id == Auth::id(), 'snapshot' => ($battle->status == 'completed' && is_array($battle->team_a_card_data) && isset($battle->team_a_card_data[$i])) ? $battle->team_a_card_data[$i] : null])
                    </div>
                @endfor
            </div>
        </div>

        <!-- Team B Column -->
        <div class="col-6" style="min-width: 0;">
            

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
                    <div class="w-100 slot-container" id="slot-container-B-{{ $i }}" style="max-width: 350px;">
                        @include('battles.partials.single-slot', ['team' => 'B', 'slot' => $i, 'u' => $u, 'c' => $c, 'isFinal' => $battle->status == 'completed', 'isMe' => $u && $u->id == Auth::id(), 'snapshot' => ($battle->status == 'completed' && is_array($battle->team_b_card_data) && isset($battle->team_b_card_data[$i])) ? $battle->team_b_card_data[$i] : null])
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Battle Controls / Actions & Activity Log -->
    <div class="row justify-content-center mt-4">
        <div class="col-lg-7">
            @include('battles.status', ['battle' => $battle])

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
                                        
                                        <form action="{{ route('battles.action.declare_win', $battle) }}" method="POST" class="d-inline" id="declareWin{{ $winTeam }}Form">@csrf <input type="hidden" name="team" value="{{ $winTeam }}"><button type="button" class="btn {{ $winClass }} btn-sm" onclick="window.neonConfirm('Are you sure you want to declare WIN?').then(c => { if(c) handleActionSubmit('declareWin{{ $winTeam }}Form'); })" style="{{ $winStyle }}">
                                            <i class="bi {{ $winIcon }}"></i> {{ $winText }}
                                        </button></form>
                                        <form action="{{ route('battles.action.declare_win', $battle) }}" method="POST" class="d-inline" id="declareWin{{ $lostTeam }}Form">@csrf <input type="hidden" name="team" value="{{ $lostTeam }}"><button type="button" class="btn {{ $lostClass }} btn-sm" onclick="window.neonConfirm('Are you sure you want to declare LOST?').then(c => { if(c) handleActionSubmit('declareWin{{ $lostTeam }}Form'); })" style="{{ $lostStyle }}">
                                            <i class="bi {{ $lostIcon }}"></i> {{ $lostText }}
                                        </button></form>
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
                                        <form action="{{ route('battles.action.cancel', $battle) }}" method="POST" class="d-inline" id="requestCancelForm" onsubmit="event.preventDefault(); handleActionSubmit('requestCancelForm');">@csrf <button type="submit" class="btn btn-outline-danger btn-sm" onclick="window.neonConfirm('Are you sure you want to request to CANCEL this active match?').then(c => { if(c) handleActionSubmit('requestCancelForm'); }); return false;">
                                            <i class="bi bi-x-circle"></i> REQUEST CANCEL
                                        </button></form>
                                    @endif
                                @endif

                                @if($battle->status == 'pending')
                                    @if($isLeaderA || $isLeaderB)
                                        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#renameTeamModal" onclick="document.getElementById('renameTeamInput').value='{{ addslashes($isLeaderA ? $battle->team_name_a : $battle->team_name_b) }}'; document.getElementById('renameTeamVal').value='{{ $isLeaderA ? "A" : "B" }}'; document.getElementById('rename_team_name').innerText='{{ $isLeaderA ? "A" : "B" }}';">
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
                                        <form action="{{ route('battles.action.ready', $battle) }}" method="POST" class="d-inline w-100" id="readyForm">@csrf <button type="button" class="btn btn-neon-lime w-100" style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);" onclick="window.neonConfirm('Are you sure your team is ready? You will not be able to stand up once ready.').then(c => { if(c) handleActionSubmit('readyForm'); })"><i class="bi bi-check2-all"></i> READY</button></form>
                                    @endif
                                    @if(!$battle->is_full)
                                        <button type="button" class="btn btn-neon" data-bs-toggle="modal" data-bs-target="#invitePlayerModal">
                                            <i class="bi bi-person-plus-fill"></i> INVITE PLAYERS
                                        </button>
                                    @endif
                                @endif
                            @endif

                            @if($battle->status == 'pending' && Auth::id() != $battle->team_a_user_1 && !($battle->team_b_ready))
                                <form action="{{ route('battles.action.standup', $battle) }}" method="POST" class="d-inline w-100" id="standUpForm">@csrf <button type="button" class="btn btn-outline-warning w-100" onclick="window.neonConfirm('Are you sure you want to stand up and leave your slot?').then(c => { if(c) handleActionSubmit('standUpForm'); })"><i class="bi bi-box-arrow-right"></i> STAND UP</button></form>
                            @endif

                        </div>
                    </div>
                    
                    <!-- Share QR (Mobile Only) - Moved outside actions-container so it survives AJAX replacements -->
                    <div class="mt-3 text-center d-md-none">
                        <button type="button" class="btn btn-neon" style="border-color: #39ff14; color: #39ff14;" data-bs-toggle="modal" data-bs-target="#shareQRModal">
                            <i class="bi bi-qr-code"></i> SHARE QR
                        </button>
                    </div>
            @elseif(Auth::id() == $battle->marshall_id && $battle->status == 'active')
                @if(Auth::id())
                    <div class="mt-4 mb-5 pt-4" style="border-top: 1px solid rgba(255, 221, 0, 0.1);">
                        <h5 class="section-header mb-3">
                            <i class="bi bi-gear-wide-connected section-icon" style="color: #ffdd00;"></i> MARSHALL ACTIONS
                        </h5>
                        <div id="actions-container" class="d-flex gap-3 flex-wrap align-items-center">
                            <form action="{{ route('battles.action.declare_win', $battle) }}" method="POST" class="d-inline" id="marshallDeclareWinAForm">@csrf <input type="hidden" name="team" value="A"><button type="button" class="btn btn-neon btn-sm" onclick="window.neonConfirm('As Marshall, are you sure you want to officially declare TEAM A as the winner?').then(c => { if(c) handleActionSubmit('marshallDeclareWinAForm'); })">TEAM A WON</button></form>
                            <form action="{{ route('battles.action.declare_win', $battle) }}" method="POST" class="d-inline" id="marshallDeclareWinBForm">@csrf <input type="hidden" name="team" value="B"><button type="button" class="btn btn-neon-magenta btn-sm" onclick="window.neonConfirm('As Marshall, are you sure you want to officially declare TEAM B as the winner?').then(c => { if(c) handleActionSubmit('marshallDeclareWinBForm'); })">TEAM B WON</button></form>
                            <form action="{{ route('battles.action.cancel', $battle) }}" method="POST" class="d-inline" id="marshallCancelForm">@csrf <button type="button" class="btn btn-neon-danger btn-sm" onclick="window.neonConfirm('Are you sure you want to CANCEL this match? No cards will be transferred.').then(c => { if(c) document.getElementById('marshallCancelForm').submit(); })">CANCEL MATCH</button></form>
                        </div>
                    </div>
                @endif
            @endif
        </div>
        
        <div class="col-lg-3 mt-4 mt-lg-0"><livewire:battle-activity-log :battle="$battle" /></div>
    </div> <!-- Close row g-4 -->
</div> <!-- Close team-battle-room -->
</div> <!-- Close root div from livewire -->

<!-- Join Modal (Simulated) -->
    
        <div class="modal fade" id="joinModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" style="background: rgba(0, 0, 0, 0.8);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content custom-modal p-4 neon-card" style="max-width: 800px; width: 95%;">
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
                                                    <div class="selectable-card" onclick="document.querySelectorAll('.selectable-card').forEach(e=>e.classList.remove('selected')); this.classList.add('selected'); document.getElementById('selectedCardId').value='{{$card->id}}'; document.getElementById('confirmJoinBtn').disabled=false;" style="cursor: pointer;">
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
                    <?php if(isset($errors) && $errors->has("selectedCardId")): ?><div class="text-danger small mt-2 text-center">{{ $errors->first("selectedCardId") }}</div><?php endif; ?>

                <div class="d-flex gap-3 mt-4">
                    <button type="button" class="btn btn-outline-secondary w-50 py-2" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-neon w-50 py-2 orbitron" id="confirmJoinBtn" disabled>CONFIRM JOIN</button>
                </div>
                </form>
            </div>
        </div>
        </div>

        <!-- Rename Team Modal -->
    <div class="modal fade" id="renameTeamModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" style="background: rgba(0, 0, 0, 0.8);">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('battles.action.rename', $battle) }}" method="POST" class="w-100" id="renameTeamForm">
            @csrf
            <input type="hidden" name="team" id="renameTeamVal" value="">
            <div class="modal-content p-4 neon-card" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #00f0ff; backdrop-filter: blur(20px);">
                <h5 class="orbitron text-cyan mb-4 text-center">RENAME TEAM <span id="rename_team_name"></span></h5>
                <div class="mb-4">
                    <input type="text" name="name" id="renameTeamInput" class="form-control bg-dark text-white border-cyan text-center orbitron" placeholder="Enter new team name" required>
                    <div class="form-error-display d-none text-danger small mt-2 text-center"></div>
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
                <form action="{{ route('battles.action.elect_marshall', $battle) }}" method="POST" id="electMarshallForm">
                <div class="modal-body py-4">
                    <div class="mb-3 position-relative">
                        @csrf
                        <input type="hidden" name="marshall_id" id="marshall_nominee_id">
                        <label class="form-label">MARSHALL USERNAME</label>
                        <div class="form-control d-flex align-items-center p-1" style="min-height: 42px; position: relative;">
                            <span class="badge d-flex align-items-center gap-2 p-2 d-none" id="marshall_selected_badge" style="background: rgba(255,221,0,0.2); border: 1px solid #ffdd00; color: #ffdd00; font-size: 0.9rem;">
                                <i class="bi bi-person-fill"></i> 
                                <span id="marshall_selected_username"></span>
                                <i class="bi bi-x-circle-fill ms-2" style="cursor: pointer;" onclick="clearMarshall()"></i>
                            </span>
                            <input type="text" id="marshall_search_input" class="border-0 bg-transparent text-white flex-grow-1 px-2" placeholder="Search username..." autocomplete="off" style="outline: none; box-shadow: none;" oninput="searchUsers(this.value, 'marshall')">
                        </div>
                        <div class="position-absolute w-100 mt-1 d-none" id="marshall_search_results" style="z-index: 1050; max-height: 200px; overflow-y: auto; background: rgba(10, 10, 30, 0.95); border: 1px solid #ffdd00; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                        </div>
                    </div>
                    <p class="text-muted small">Both team leaders must elect the same user for them to be designated as the marshall.</p>
                    <div class="form-error-display d-none text-danger small mt-2"></div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-neon w-100" id="marshall_submit_btn" disabled style="border-color: #ffdd00; color: #ffdd00;">ELECT USER</button>
                </div>
                </form>
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
                <form action="{{ route('battles.action.invite', $battle) }}" method="POST" id="invitePlayerForm">
                <div class="modal-body py-4">
                    <div class="mb-3 position-relative">
                        @csrf
                        <input type="hidden" name="user_id" id="invite_nominee_id">
                        <label class="form-label">PLAYER USERNAME</label>
                        <div class="form-control d-flex align-items-center p-1" style="min-height: 42px; position: relative;">
                            <span class="badge d-flex align-items-center gap-2 p-2 d-none" id="invite_selected_badge" style="background: rgba(0,240,255,0.2); border: 1px solid #00f0ff; color: #00f0ff; font-size: 0.9rem;">
                                <i class="bi bi-person-fill"></i> 
                                <span id="invite_selected_username"></span>
                                <i class="bi bi-x-circle-fill ms-2" style="cursor: pointer;" onclick="clearInvite()"></i>
                            </span>
                            <input type="text" id="invite_search_input" class="border-0 bg-transparent text-white flex-grow-1 px-2" placeholder="Search username..." autocomplete="off" style="outline: none; box-shadow: none;" oninput="searchUsers(this.value, 'invite')">
                        </div>
                        <div class="position-absolute w-100 mt-1 d-none" id="invite_search_results" style="z-index: 1050; max-height: 200px; overflow-y: auto; background: rgba(10, 10, 30, 0.95); border: 1px solid #00f0ff; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                        </div>
                    </div>
                    <p class="text-muted small">Invited players will receive a notification to join this battle room.</p>
                    <div class="form-error-display d-none text-danger small mt-2"></div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-neon w-100" id="invite_submit_btn" disabled>SEND INVITE</button>
                </div>
                </form>
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
                        <form action="{{ route('battles.action.respond_cancel', $battle) }}" method="POST" class="w-100" id="agreeCancelForm" onsubmit="event.preventDefault(); handleActionSubmit('agreeCancelForm');">
                            @csrf <input type="hidden" name="agreed" value="1">
                            <button type="submit" class="btn btn-neon-magenta w-100"><i class="bi bi-check-lg"></i> AGREE & CANCEL</button>
                        </form>
                        <form action="{{ route('battles.action.respond_cancel', $battle) }}" method="POST" class="w-100" id="rejectCancelForm" onsubmit="event.preventDefault(); handleActionSubmit('rejectCancelForm');">
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
    
</script>

<script>
let searchTimeout = null;
function searchUsers(query, type) {
    clearTimeout(searchTimeout);
    let resultsDiv = document.getElementById(type + '_search_results');
    if(query.length < 2) {
        resultsDiv.classList.add('d-none');
        return;
    }
    searchTimeout = setTimeout(() => {
        fetch('/battles/' + {{ $battle->id }} + '/search?q=' + query)
            .then(res => res.json())
            .then(data => {
                resultsDiv.innerHTML = '';
                if(data.length === 0) {
                    resultsDiv.innerHTML = '<div class="p-2 text-center text-muted small">No players found</div>';
                } else {
                    data.forEach(user => {
                        let div = document.createElement('div');
                        div.className = 'p-2 d-flex align-items-center gap-2';
                        div.style.cssText = 'cursor: pointer; border-bottom: 1px solid rgba(0, 240, 255, 0.1);';
                        div.onclick = () => selectUser(user.id, user.username, type);
                        div.innerHTML = '<img src="' + (user.avatar_url || '') + '" style="width: 24px; height: 24px; border-radius: 50%;"> <span class="text-white">@' + user.username + '</span>';
                        resultsDiv.appendChild(div);
                    });
                }
                resultsDiv.classList.remove('d-none');
            });
    }, 300);
}

function selectUser(id, username, type) {
    document.getElementById(type + '_nominee_id').value = id;
    document.getElementById(type + '_search_input').classList.add('d-none');
    document.getElementById(type + '_search_results').classList.add('d-none');
    let badge = document.getElementById(type + '_selected_badge');
    badge.classList.remove('d-none');
    document.getElementById(type + '_selected_username').innerText = username;
    document.getElementById(type + '_submit_btn').disabled = false;
}

function clearInvite() {
    document.getElementById('invite_nominee_id').value = '';
    document.getElementById('invite_search_input').classList.remove('d-none');
    document.getElementById('invite_search_input').value = '';
    document.getElementById('invite_selected_badge').classList.add('d-none');
    document.getElementById('invite_submit_btn').disabled = true;
}

function clearMarshall() {
    document.getElementById('marshall_nominee_id').value = '';
    document.getElementById('marshall_search_input').classList.remove('d-none');
    document.getElementById('marshall_search_input').value = '';
    document.getElementById('marshall_selected_badge').classList.add('d-none');
    document.getElementById('marshall_submit_btn').disabled = true;
}
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const joinModalEl = document.getElementById('joinModal');
        if (joinModalEl) {
            joinModalEl.addEventListener('hidden.bs.modal', event => {
                document.querySelectorAll('.selectable-card').forEach(e => e.classList.remove('selected'));
                document.getElementById('selectedCardId').value = '';
                const btn = document.getElementById('confirmJoinBtn');
                if (btn) btn.disabled = true;
            });
        }
    });
</script>
<script>
    // Fix for Bootstrap 5 multiple modals: keep body class 'modal-open' if joinModal is still open
    document.addEventListener('hidden.bs.modal', function (event) {
        if (event.target.id !== 'joinModal') {
            const joinModalEl = document.getElementById('joinModal');
            if (joinModalEl && joinModalEl.classList.contains('show')) {
                document.body.classList.add('modal-open');
            }
        }
    });
</script>
<script>
    // Universal AJAX Form Handler for Modals
        // Action Button Submitter
    function handleActionSubmit(formId) {
        const form = document.getElementById(formId);
        if (!form) return;
        
        const submitBtn = form.querySelector('button');
        if (!submitBtn) return;
        
        const originalBtnHTML = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> WAIT...';
        
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Action failed.');
            return data;
        })
        .then(data => {
            if (data.status === 'success') {
                if (data.reload === true || data.consensus === true || data.conflict === true) {
                    window.isReloading = true;
                    window.location.reload();
                } else if (data.message && data.message.includes('ready')) {
                    form.outerHTML = '<span class="badge bg-success w-100 p-2"><i class="bi bi-check-circle"></i> YOU ARE READY</span>';
                    const standUpBtn = document.getElementById('standUpForm');
                    if (standUpBtn) standUpBtn.style.display = 'none';
                    setTimeout(() => window.location.reload(), 800);
                } else if (data.message && data.message.includes('stood up')) {
                    window.location.reload();
                } else {
                    const container = form.closest('.d-flex.gap-3.justify-content-center') || form.closest('#actions-container');
                    if (container) {
                        container.innerHTML = '<div class="alert alert-info py-2 small mb-0 text-center" style="border: 1px solid #00f0ff; background: rgba(0, 240, 255, 0.1); color: #00f0ff; width: 100%; max-width: 400px;"><i class="bi bi-hourglass-split"></i> You declared a vote. Waiting for opponent...</div>';
                    }
                }
            }
        })
        .catch(error => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHTML;
            if (typeof window.neonAlert === 'function') {
                window.neonAlert(error.message, "ERROR");
            } else {
                alert(error.message);
            }
        });
    }

    function setupAjaxForm(formId, modalId, successCallback = null) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"]');
            if (!submitBtn) return;
            const originalBtnHTML = submitBtn.innerHTML;
            const errorDiv = form.querySelector('.form-error-display');
            const modalEl = document.getElementById(modalId);
            
            // Loading State
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> PROCESSING...';
            if (errorDiv) errorDiv.classList.add('d-none');
            
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Something went wrong. Please try again.');
                }
                return data;
            })
            .then(data => {
                // Success
                const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                modalInstance.hide();
                
                if (typeof window.neonAlert === 'function') {
                    window.neonAlert(data.message, "SUCCESS");
                }
                
                if (successCallback) successCallback(data);
                form.reset();
            })
            .catch(error => {
                // Error
                if (errorDiv) {
                    errorDiv.innerText = error.message;
                    errorDiv.classList.remove('d-none');
                } else {
                    alert(error.message);
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHTML;
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Setup Rename Form
        setupAjaxForm('renameTeamForm', 'renameTeamModal', (data) => {
            // Local update for immediate feedback
            const teamId = data.team === 'A' ? 'nakedA' : 'nakedB';
            const els = document.querySelectorAll('[x-ref="' + teamId + '"]');
            els.forEach(el => el.innerText = data.newName);
        });

        // Setup Elect Marshall Form
        setupAjaxForm('electMarshallForm', 'electMarshallModal', (data) => {
            clearMarshall(); // Reset the search state
        });

        // Setup Invite Player Form
        setupAjaxForm('invitePlayerForm', 'invitePlayerModal', (data) => {
            clearInvite(); // Reset the search state
        });
    });
</script>
<script>
    // Real-time Room Updates via Hybrid Fetch & AJAX
    document.addEventListener('DOMContentLoaded', () => {
        if(window.Echo) {
            window.Echo.channel('battle.{{ $battle->id }}')
                .listen('BattleUpdated', (e) => {
                    if (e.type === 'update') {
                        // 1. Update Team Names
                        if (e.team_name_a) {
                            const nameA = document.querySelectorAll('[x-ref="nakedA"]');
                            nameA.forEach(el => el.innerText = e.team_name_a);
                        }
                        if (e.team_name_b) {
                            const nameB = document.querySelectorAll('[x-ref="nakedB"]');
                            nameB.forEach(el => el.innerText = e.team_name_b);
                        }

                        // Major state changes check FIRST so we can flag isReloading
                        if (e.message.includes('ready')) {
                            const standUpBtn = document.getElementById('standUpForm');
                            if (standUpBtn) standUpBtn.style.display = 'none';
                        }
                        if (e.message.includes('started') || e.message.includes('finalized') || e.message.includes('cancelled') || e.message.includes('ready') || e.message.includes('requested cancellation') || e.message.includes('rejected')) {
                            window.isReloading = true;
                            setTimeout(() => window.location.reload(), 1000);
                            return; // Stop processing updates!
                        }

                        // 2. Handle Real-time Slot Updates ONLY if not reloading
                        const slots = document.querySelectorAll('.slot-container');
                        slots.forEach(slotEl => {
                            const idParts = slotEl.id.split('-'); 
                            const team = idParts[2];
                            const slotNum = idParts[3];
                            
                            // Visual cue that it is updating
                            slotEl.style.opacity = '0.5';
                            
                            fetch('/battles/{{ $battle->id }}/partial-slot/' + team + '/' + slotNum)
                                .then(res => res.text())
                                .then(html => {
                                    slotEl.innerHTML = html;
                                    slotEl.style.opacity = '1';
                                    
                                    const newCanvases = slotEl.querySelectorAll('canvas[data-card-options]:not([data-initialized="true"])');
                                    newCanvases.forEach(canvas => {
                                        if (typeof DigitalCardRenderer !== 'undefined') {
                                            try {
                                                const renderer = new DigitalCardRenderer(canvas.id);
                                                const options = JSON.parse(canvas.getAttribute('data-card-options'));
                                                renderer.draw(options);
                                                canvas.dataset.initialized = 'true';
                                            } catch (err) {
                                                console.error("Failed to re-render card in slot", err);
                                            }
                                        }
                                    });
                                })
                                .catch(err => {
                                    console.error("Failed to fetch slot update", err);
                                    slotEl.style.opacity = '1';
                                });
                        });
                        
                    }
                });
        }
    });
</script>
@endsection
