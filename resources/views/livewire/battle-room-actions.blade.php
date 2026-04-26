<div>
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
            @if($battle->marshall_id === Auth::id() && !in_array($battle->status, ['completed', 'cancelled']))
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

    <!-- Invite Opponent Modal -->
    <div class="modal fade" wire:ignore.self id="inviteOpponentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" style="background: rgba(0, 0, 0, 0.8);">
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
                                <input type="hidden" name="username" id="invite-hidden-username" required>
                                <span id="invite-selected-chip" class="badge d-flex align-items-center gap-2 p-2 d-none" style="background: rgba(0,240,255,0.2); border: 1px solid #00f0ff; color: #00f0ff; font-size: 0.9rem;">
                                    <i class="bi bi-person-fill"></i> <span id="invite-chip-text"></span>
                                    <i class="bi bi-x-circle-fill ms-2" style="cursor: pointer;" onclick="clearInviteSelection()"></i>
                                </span>
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
    <div class="modal fade" wire:ignore.self id="electMarshallModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" style="background: rgba(0, 0, 0, 0.8);">
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
                                <input type="hidden" name="user_id" id="adj-hidden-user-id" value="{{ $existingUserId }}" required>
                                <span id="adj-selected-chip" class="badge d-flex align-items-center gap-2 p-2 {{ $existingUsername ? '' : 'd-none' }}" style="background: rgba(255,221,0,0.2); border: 1px solid #ffdd00; color: #ffdd00; font-size: 0.9rem;">
                                    <i class="bi bi-person-fill"></i> <span id="adj-chip-text">{{ $existingUsername }}</span>
                                    <i class="bi bi-x-circle-fill ms-2" style="cursor: pointer;" onclick="clearMarshallSelection()"></i>
                                </span>
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

    <!-- Declare Result Modal -->
    <div class="modal fade" wire:ignore.self id="declareWinnerModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" style="background: rgba(0, 0, 0, 0.8);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #ff00ff; backdrop-filter: blur(20px);">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title neon-text-magenta">DECLARE RESULT</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    @php
                        $cDecl = 'PENDING...';
                        if ($battle->challenger_declared_user_win) {
                            if ($battle->challenger_declared_user_win == $battle->challenger_id) {
                                $cDecl = '<span class="text-success"><i class="bi bi-trophy-fill"></i> DECLARED WIN</span>';
                            } else {
                                $cDecl = '<span class="text-danger"><i class="bi bi-x-circle-fill"></i> DECLARED LOSS</span>';
                            }
                        }

                        $oDecl = 'PENDING...';
                        if ($battle->opponent_declared_user_win) {
                            if ($battle->opponent_declared_user_win == $battle->opponent_id) {
                                $oDecl = '<span class="text-success"><i class="bi bi-trophy-fill"></i> DECLARED WIN</span>';
                            } else {
                                $oDecl = '<span class="text-danger"><i class="bi bi-x-circle-fill"></i> DECLARED LOSS</span>';
                            }
                        }
                    @endphp

                    @if($battle->status === 'failed')
                        <div class="alert alert-danger mb-4" style="background: rgba(255,0,0,0.1); border-color: #ff0055; color: #ff0055; font-size: 0.85rem;">
                            <i class="bi bi-exclamation-octagon-fill"></i> <strong>CONFLICT DETECTED:</strong> Players have declared different winners. Please reach a consensus or wait for the marshall.
                        </div>
                    @endif

                    @if($battle->marshall_id === Auth::id())
                        <p class="text-center mb-4">Click on a player below to declare them as the winner of this match. As an marshall, your decision will finalize the battle immediately.</p>
                        <div class="d-flex flex-column gap-3 text-start mb-2">
                            <button type="button" onclick="declareWinnerAjax({{ $battle->challenger_id }}, this)" data-message="Declare {{ $battle->challenger->username }} as winner?" class="w-100 p-3 rounded d-flex flex-column gap-2 border-0" style="background: rgba(0, 240, 255, 0.05); border: 1px solid rgba(0, 240, 255, 0.5) !important; color: #fff; cursor: pointer; text-align: left; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(0, 240, 255, 0.15)'; this.style.boxShadow='0 0 15px rgba(0, 240, 255, 0.4)';" onmouseout="this.style.background='rgba(0, 240, 255, 0.05)'; this.style.boxShadow='none';">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <span style="font-size: 1.1rem;"><i class="bi bi-person-fill text-info"></i> {{ $battle->challenger->username }}</span>
                                    <span class="badge text-dark" style="background-color: #00f0ff;">CHALLENGER</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center w-100 small" style="color: #8888aa;">
                                    <span>Their Declaration:</span>
                                    <strong style="color: #fff;" id="challenger-declaration-text">{!! $cDecl !!}</strong>
                                </div>
                            </button>

                            @if($battle->opponent)
                            <button type="button" onclick="declareWinnerAjax({{ $battle->opponent_id }}, this)" data-message="Declare {{ $battle->opponent->username }} as winner?" class="w-100 p-3 rounded d-flex flex-column gap-2 border-0" style="background: rgba(255, 0, 255, 0.05); border: 1px solid rgba(255, 0, 255, 0.5) !important; color: #fff; cursor: pointer; text-align: left; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255, 0, 255, 0.15)'; this.style.boxShadow='0 0 15px rgba(255, 0, 255, 0.4)';" onmouseout="this.style.background='rgba(255, 0, 255, 0.05)'; this.style.boxShadow='none';">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <span style="font-size: 1.1rem;"><i class="bi bi-person-fill text-magenta" style="color: #ff00ff;"></i> {{ $battle->opponent->username }}</span>
                                    <span class="badge text-white" style="background-color: #ff00ff;">OPPONENT</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center w-100 small" style="color: #8888aa;">
                                    <span>Their Declaration:</span>
                                    <strong style="color: #fff;" id="opponent-declaration-text">{!! $oDecl !!}</strong>
                                </div>
                            </button>
                            @endif
                        </div>
                    @else
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
                                <div class="d-flex justify-content-between align-items-center">
                                    <span style="color: #00f0ff;">{{ mb_strtoupper($battle->challenger->username) }}:</span>
                                    <strong id="status-text-challenger" style="color: #fff; font-size: 0.95rem;">{!! $cDecl !!}</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span style="color: #ff00ff;">{{ $battle->opponent ? mb_strtoupper($battle->opponent->username) : 'OPPONENT' }}:</span>
                                    <strong id="status-text-opponent" style="color: #fff; font-size: 0.95rem;">{!! $oDecl !!}</strong>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Share QR Modal -->
    <div class="modal fade" wire:ignore.self id="shareQRModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" style="background: rgba(0, 0, 0, 0.8);">
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
    <div class="modal fade" wire:ignore.self id="rejectedOpponentModal" data-bs-backdrop="false" data-bs-keyboard="false" tabindex="-1" aria-hidden="true" style="background: rgba(0, 0, 0, 0.8);">
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

    <div class="modal fade" wire:ignore.self id="cancellationRequestModal" data-bs-backdrop="false" data-bs-keyboard="false" tabindex="-1" aria-hidden="true" style="background: rgba(0, 0, 0, 0.8);">
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
</div>
