
<div class="team-battle-room" wire:poll.10s>
    <div class="row g-4">
        <!-- Team A Column -->
        <div class="col-6">
            <div class="text-center mb-4">
                @if($showEditTeamA)
                    <div class="input-group input-group-sm w-100 w-md-75 mx-auto">
                        <input type="text" wire:model="newTeamNameA" class="form-control bg-dark text-white border-cyan text-center">
                        <button class="btn btn-neon btn-sm" wire:click="updateTeamName('A')">SAVE</button>
                    </div>
                @else
                    <h4 class="orbitron text-cyan mb-0 d-inline-block text-truncate w-100" title="{{ $teamBattle->team_name_a }}">
                        {{ $teamBattle->team_name_a }}
                        @if(Auth::id() == $teamBattle->team_a_user_1 && $this->isParticipant())
                            <i class="bi bi-pencil-square cursor-pointer ms-2" style="font-size: 0.8rem;" wire:click="$set('showEditTeamA', true)"></i>
                        @endif
                    </h4>
                @endif
            </div>

            <div class="d-flex flex-column gap-4 align-items-center">
                @for($i = 1; $i <= $teamBattle->no_players_per_team; $i++)
                    @php 
                        $u = \App\Models\User::find($teamBattle->{"team_a_user_{$i}"});
                        $c = \App\Models\DigitalCard::find($teamBattle->{"team_a_card_{$i}"});
                    @endphp
                    <div class="w-100" style="max-width: 350px;">
                        @if($u)
                            <div class="mb-2 text-center text-truncate">
                                <span class="fw-bold">{{ $u->username }}</span>
                            </div>
                            <div wire:ignore>
                                <x-digital-card 
                                    id="card_a_{{ $i }}_{{ $c->id }}"
                                    mode="thumbnail"
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
                                    :wins="$c->wins"
                                    :losses="$c->losses"
                                    :integrityStat="$c->integrity_stat"
                                    :lifePoints="$c->life_points"
                                    :status="$c->status"
                                    :rankLevel="$c->level"
                                    :serialNumber="$c->serial_number"
                                    :rarity="$c->rarity"
                                />
                            </div>
                        @else
                            <div class="mb-2 text-center text-truncate">
                                <span class="fw-bold text-muted">----</span>
                            </div>
                            <div class="empty-card-slot d-flex flex-column align-items-center justify-content-center p-3 rounded" style="border: 2px dashed rgba(0, 240, 255, 0.4); background: rgba(0, 240, 255, 0.05); aspect-ratio: 350 / 490; width: 100%;">
                                <div class="orbitron text-muted mb-3 fs-5">SLOT {{ $i }}</div>
                                @if($teamBattle->status == 'pending')
                                    <button class="btn btn-outline-cyan btn-sm w-100" style="max-width: 150px;" wire:click="joinTeam('A', {{ $i }})">JOIN TEAM A</button>
                                @endif
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        <!-- Team B Column -->
        <div class="col-6">
            <div class="text-center mb-4">
                @if($showEditTeamB)
                    <div class="input-group input-group-sm w-100 w-md-75 mx-auto">
                        <input type="text" wire:model="newTeamNameB" class="form-control bg-dark text-white border-magenta text-center">
                        <button class="btn btn-neon-magenta btn-sm" wire:click="updateTeamName('B')">SAVE</button>
                    </div>
                @else
                    <h4 class="orbitron text-magenta mb-0 d-inline-block text-truncate w-100" title="{{ $teamBattle->team_name_b }}">
                        {{ $teamBattle->team_name_b }}
                        @if(Auth::id() == $teamBattle->team_b_user_1 && $this->isParticipant())
                            <i class="bi bi-pencil-square cursor-pointer ms-2" style="font-size: 0.8rem;" wire:click="$set('showEditTeamB', true)"></i>
                        @endif
                    </h4>
                @endif
            </div>

            <div class="d-flex flex-column gap-4 align-items-center">
                @for($i = 1; $i <= $teamBattle->no_players_per_team; $i++)
                    @php 
                        $u = \App\Models\User::find($teamBattle->{"team_b_user_{$i}"});
                        $c = \App\Models\DigitalCard::find($teamBattle->{"team_b_card_{$i}"});
                    @endphp
                    <div class="w-100" style="max-width: 350px;">
                        @if($u)
                            <div class="mb-2 text-center text-truncate">
                                <span class="fw-bold">{{ $u->username }}</span>
                            </div>
                            <div wire:ignore>
                                <x-digital-card 
                                    id="card_b_{{ $i }}_{{ $c->id }}"
                                    mode="thumbnail"
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
                                    :wins="$c->wins"
                                    :losses="$c->losses"
                                    :integrityStat="$c->integrity_stat"
                                    :lifePoints="$c->life_points"
                                    :status="$c->status"
                                    :rankLevel="$c->level"
                                    :serialNumber="$c->serial_number"
                                    :rarity="$c->rarity"
                                />
                            </div>
                        @else
                            <div class="mb-2 text-center text-truncate">
                                <span class="fw-bold text-muted">----</span>
                            </div>
                            <div class="empty-card-slot d-flex flex-column align-items-center justify-content-center p-3 rounded" style="border: 2px dashed rgba(255, 0, 255, 0.4); background: rgba(255, 0, 255, 0.05); aspect-ratio: 350 / 490; width: 100%;">
                                <div class="orbitron text-muted mb-3 fs-5">SLOT {{ $i }}</div>
                                @if($teamBattle->status == 'pending')
                                    <button class="btn btn-outline-magenta btn-sm w-100" style="max-width: 150px;" wire:click="joinTeam('B', {{ $i }})">JOIN TEAM B</button>
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
            <div class="neon-card p-4 mb-4">
                <div class="row">
                    <div class="col-md-7">
                        <h5 class="orbitron text-cyan">BATTLE STATUS: <span class="text-white">{{ strtoupper($teamBattle->status) }}</span></h5>
                        <p class="small text-muted">{{ $teamBattle->battle_terms }}</p>
                        
                        @if($teamBattle->marshall_id)
                            <div class="mt-3">
                                <span class="badge bg-warning text-dark"><i class="bi bi-shield-check"></i> MARSHALL: {{ $teamBattle->marshall->username }}</span>
                            </div>
                        @elseif($teamBattle->team_a_marshall_elect || $teamBattle->team_b_marshall_elect)
                            <div class="mt-3 small text-warning">
                                <i class="bi bi-hourglass-split"></i> Waiting for Marshall Consensus...
                            </div>
                        @endif
                    </div>
                    <div class="col-md-5 text-md-end">
                        @if($teamBattle->status == 'pending')
                            @if(!$teamBattle->is_full)
                                <div class="alert alert-warning py-2 small">Waiting for players to join...</div>
                            @elseif(Auth::id() != $teamBattle->team_a_user_1)
                                <div class="alert alert-info py-2 small">Waiting for Team A Leader to start...</div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            here {{$this->isParticipant()}}
            <!-- Leader Battle Actions -->
            @if(Auth::id() == $teamBattle->team_a_user_1 || Auth::id() == $teamBattle->team_b_user_1)
                @if($this->isParticipant())
                    <div class="mt-4 mb-5 pt-4" style="border-top: 1px solid rgba(0, 240, 255, 0.1);">
                        <h5 class="section-header mb-3">
                            <i class="bi bi-gear-wide-connected section-icon" style="color: #00f0ff;"></i> BATTLE ACTIONS
                        </h5>
                        
                        <div id="actions-container" class="d-flex gap-3 flex-wrap align-items-center">
                            @if($teamBattle->status == 'active')
                                <button class="btn btn-neon btn-sm" wire:click="declareWin('A')">
                                    <i class="bi bi-trophy"></i> TEAM A WON
                                </button>
                                <button class="btn btn-neon-magenta btn-sm" wire:click="declareWin('B')">
                                    <i class="bi bi-trophy"></i> TEAM B WON
                                </button>
                                <button class="btn btn-neon-danger btn-sm" wire:click="cancelBattle">
                                    <i class="bi bi-x-circle"></i> CANCEL
                                </button>
                            @endif
                            
                            @if(!$teamBattle->marshall_id && in_array($teamBattle->status, ['pending', 'ready', 'active']))
                                <button type="button" class="btn btn-neon btn-sm" style="border-color: #ffdd00; color: #ffdd00;" data-bs-toggle="modal" data-bs-target="#electMarshallModal">
                                    <i class="bi bi-shield-fill-check"></i> 
                                    {{ (Auth::id() === $teamBattle->team_a_user_1 ? $teamBattle->team_a_marshall_elect : $teamBattle->team_b_marshall_elect) ? 'CHANGE ELECTION' : 'ELECT MARSHALL' }}
                                </button>
                            @endif
                            
                            @if($teamBattle->status == 'pending' && Auth::id() == $teamBattle->team_a_user_1)
                                @if($teamBattle->is_full)
                                    <button class="btn btn-neon-lime" wire:click="startBattle" style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);">
                                        <i class="bi bi-play-fill"></i> START MATCH
                                    </button>
                                @endif
                                <button class="btn btn-neon-danger" wire:click="cancelBattle">
                                    <i class="bi bi-x-circle"></i> CANCEL BATTLE
                                </button>
                            @elseif($teamBattle->status == 'pending' && Auth::id() == $teamBattle->team_b_user_1)
                                <button class="btn btn-neon-danger" wire:click="cancelBattle">
                                    <i class="bi bi-x-circle"></i> CANCEL BATTLE
                                </button>
                            @endif

                            <!-- Share QR (Mobile Only) -->
                            <button type="button" class="btn btn-neon d-md-none" style="border-color: #39ff14; color: #39ff14;" data-bs-toggle="modal" data-bs-target="#shareQRModal">
                                <i class="bi bi-qr-code"></i> SHARE QR
                            </button>
                        </div>
                    </div>
                @endif
            @elseif(Auth::id() == $teamBattle->marshall_id && $teamBattle->status == 'active')
                @if(Auth::id())
                    <div class="mt-4 mb-5 pt-4" style="border-top: 1px solid rgba(255, 221, 0, 0.1);">
                        <h5 class="section-header mb-3">
                            <i class="bi bi-gear-wide-connected section-icon" style="color: #ffdd00;"></i> MARSHALL ACTIONS
                        </h5>
                        <div id="actions-container" class="d-flex gap-3 flex-wrap align-items-center">
                            <button class="btn btn-neon btn-sm" wire:click="declareWin('A')">TEAM A WON</button>
                            <button class="btn btn-neon-magenta btn-sm" wire:click="declareWin('B')">TEAM B WON</button>
                            <button class="btn btn-neon-danger btn-sm" wire:click="cancelBattle">CANCEL MATCH</button>
                        </div>
                    </div>
                @endif
            @endif
        </div>
        
        <!-- Activity Log -->
        <div class="col-lg-3 mt-4 mt-lg-0">
            <div class="neon-card p-3 h-100">
                <h6 class="orbitron text-cyan mb-3 border-bottom border-secondary pb-2">ACTIVITY LOG</h6>
                <div class="activity-log-container" style="max-height: 300px; overflow-y: auto;">
                    @foreach($activities as $activity)
                        <div class="activity-item mb-2 border-bottom border-secondary border-opacity-10 pb-1">
                            <span class="text-muted small">[{{ $activity->created_at->format('H:i') }}]</span>
                            <span class="small">{{ $activity->message }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Join Modal (Simulated) -->
    @if($joiningTeam)
        <div class="custom-modal-backdrop">
            <div class="custom-modal p-4 neon-card" style="max-width: 800px; width: 95%;">
                <h4 class="orbitron text-cyan mb-4 text-center">JOIN TEAM {{ $joiningTeam }}</h4>
                
                <div class="mb-4">
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
                                                    <div class="selectable-card {{ $selectedCardId == $card->id ? 'selected' : '' }}" 
                                                         wire:click="$set('selectedCardId', {{ $card->id }})">
                                                        <div class="card-img-wrapper" style="position: relative;">
                                                            <div style="pointer-events: none;" wire:ignore>
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
                                                            @if($selectedCardId == $card->id)
                                                                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 240, 255, 0.3); display: flex; align-items: center; justify-content: center; border-radius: 8px; pointer-events: none;">
                                                                    <i class="bi bi-check-circle-fill" style="font-size: 2rem; color: #00f0ff;"></i>
                                                                </div>
                                                            @endif
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
                    <button class="btn btn-outline-secondary w-50 py-2" wire:click="$set('joiningTeam', '')">CANCEL</button>
                    <button class="btn btn-neon w-50 py-2 orbitron" wire:click="confirmJoin">CONFIRM JOIN</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Elect Marshall Modal -->
    <div class="modal fade" wire:ignore.self id="electMarshallModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" style="background: rgba(0, 0, 0, 0.8);">
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
                            if (Auth::id() === $teamBattle->team_a_user_1 && $teamBattle->team_a_marshall_elect) {
                                $existingUsername = \App\Models\User::find($teamBattle->team_a_marshall_elect)?->username;
                            } elseif (Auth::id() === $teamBattle->team_b_user_1 && $teamBattle->team_b_marshall_elect) {
                                $existingUsername = \App\Models\User::find($teamBattle->team_b_marshall_elect)?->username;
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
                                <input type="text" wire:model.live.debounce.300ms="marshallSearchQuery" class="border-0 bg-transparent text-white flex-grow-1 px-2" placeholder="{{ $existingUsername ? 'Currently: ' . $existingUsername : 'Search username...' }}" autocomplete="off" style="outline: none; box-shadow: none;">
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
    <div class="modal fade" wire:ignore.self id="shareQRModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" style="background: rgba(0, 0, 0, 0.8);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #39ff14; backdrop-filter: blur(20px);">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" style="color: #39ff14; font-family: 'Orbitron', sans-serif;">BATTLE QR CODE</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div id="qrcode-container" class="d-inline-block p-3 bg-white rounded-3 mb-3" wire:ignore>
                        <div id="qrcode"></div>
                    </div>
                    <p class="text-muted small">Show this QR code to your opponents or teammates to let them join this battle room.</p>
                    <div class="mt-3" wire:ignore>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control bg-dark border-secondary text-light" value="{{ route('team-battles.room', $teamBattle) }}" id="battle-url" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyBattleUrl()">COPY</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
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
            function initializeVisibleCards() {
                if (typeof DigitalCardRenderer === 'undefined') return;
                if (!window.digitalCardRenderers) window.digitalCardRenderers = {};
                
                document.querySelectorAll('canvas[data-card-options]').forEach(canvas => {
                    if (!window.digitalCardRenderers[canvas.id]) {
                        try {
                            window.digitalCardRenderers[canvas.id] = new DigitalCardRenderer(canvas.id);
                            const options = JSON.parse(canvas.getAttribute('data-card-options'));
                            window.digitalCardRenderers[canvas.id].draw(options);
                        } catch (e) {
                            console.error("Failed to init card", e);
                        }
                    }
                });
            }

            const observer = new MutationObserver((mutations) => {
                let shouldInit = false;
                for (let mutation of mutations) {
                    if (mutation.addedNodes.length > 0) {
                        shouldInit = true;
                        break;
                    }
                }
                if (shouldInit) {
                    setTimeout(initializeVisibleCards, 100);
                }
            });

            const roomContainer = document.querySelector('.team-battle-room');
            if (roomContainer) {
                observer.observe(roomContainer, { childList: true, subtree: true });
            }

            const interval = setInterval(() => {
                if (typeof DigitalCardRenderer !== 'undefined') {
                    initializeVisibleCards();
                    clearInterval(interval);
                }
            }, 100);
            
            setTimeout(() => clearInterval(interval), 5000);
        });
    </script>
</div>
