
<div class="team-battle-room" wire:poll.10s>
    <div class="row">
        <!-- Team A -->
        <div class="col-md-5">
            <div class="neon-card p-3 mb-4" style="border-color: #00f0ff !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    @if($showEditTeamA)
                        <div class="input-group input-group-sm">
                            <input type="text" wire:model="newTeamNameA" class="form-control bg-dark text-white border-cyan">
                            <button class="btn btn-neon btn-sm" wire:click="updateTeamName('A')">SAVE</button>
                        </div>
                    @else
                        <h4 class="orbitron text-cyan mb-0">
                            {{ $teamBattle->team_name_a }}
                            @if(Auth::id() == $teamBattle->team_a_user_1)
                                <i class="bi bi-pencil-square cursor-pointer ms-2" style="font-size: 0.8rem;" wire:click="$set('showEditTeamA', true)"></i>
                            @endif
                        </h4>
                    @endif
                    <span class="badge bg-cyan text-dark">TEAM A</span>
                </div>

                <div class="team-slots">
                    @for($i = 1; $i <= $teamBattle->no_players_per_team; $i++)
                        @php 
                            $u = \App\Models\User::find($teamBattle->{"team_a_user_{$i}"});
                            $c = \App\Models\DigitalCard::find($teamBattle->{"team_a_card_{$i}"});
                        @endphp
                        <div class="slot-item p-2 border-bottom border-secondary d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="slot-number me-3 orbitron text-muted">{{ $i }}</div>
                                @if($u)
                                    <div>
                                        <div class="fw-bold">{{ $u->username }}</div>
                                        <div class="small text-cyan">{{ $c->template->card_title }} (LVL {{ $c->level }})</div>
                                    </div>
                                @else
                                    <div class="text-muted italic">Empty Slot</div>
                                @endif
                            </div>
                            @if(!$u && $teamBattle->status == 'pending')
                                <button class="btn btn-outline-cyan btn-sm" wire:click="joinTeam('A', {{ $i }})">JOIN</button>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- VS Divider -->
        <div class="col-md-2 d-flex align-items-center justify-content-center my-3">
            <div class="orbitron text-white h2">VS</div>
        </div>

        <!-- Team B -->
        <div class="col-md-5">
            <div class="neon-card p-3 mb-4" style="border-color: #ff00ff !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    @if($showEditTeamB)
                        <div class="input-group input-group-sm">
                            <input type="text" wire:model="newTeamNameB" class="form-control bg-dark text-white border-magenta">
                            <button class="btn btn-neon-magenta btn-sm" wire:click="updateTeamName('B')">SAVE</button>
                        </div>
                    @else
                        <h4 class="orbitron text-magenta mb-0">
                            {{ $teamBattle->team_name_b }}
                            @if(Auth::id() == $teamBattle->team_b_user_1)
                                <i class="bi bi-pencil-square cursor-pointer ms-2" style="font-size: 0.8rem;" wire:click="$set('showEditTeamB', true)"></i>
                            @endif
                        </h4>
                    @endif
                    <span class="badge bg-magenta text-white">TEAM B</span>
                </div>

                <div class="team-slots">
                    @for($i = 1; $i <= $teamBattle->no_players_per_team; $i++)
                        @php 
                            $u = \App\Models\User::find($teamBattle->{"team_b_user_{$i}"});
                            $c = \App\Models\DigitalCard::find($teamBattle->{"team_b_card_{$i}"});
                        @endphp
                        <div class="slot-item p-2 border-bottom border-secondary d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="slot-number me-3 orbitron text-muted">{{ $i }}</div>
                                @if($u)
                                    <div>
                                        <div class="fw-bold">{{ $u->username }}</div>
                                        <div class="small text-magenta">{{ $c->template->card_title }} (LVL {{ $c->level }})</div>
                                    </div>
                                @else
                                    <div class="text-muted italic">Empty Slot</div>
                                @endif
                            </div>
                            @if(!$u && $teamBattle->status == 'pending')
                                <button class="btn btn-outline-magenta btn-sm" wire:click="joinTeam('B', {{ $i }})">JOIN</button>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Battle Controls / Actions -->
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
                            @if(Auth::id() == $teamBattle->team_a_user_1)
                                <button class="btn btn-neon btn-lg orbitron" wire:click="startBattle">START BATTLE</button>
                            @else
                                <div class="alert alert-info py-2 small">Waiting for Team A Leader to start...</div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Leader Battle Actions -->
            @if(Auth::id() == $teamBattle->team_a_user_1 || Auth::id() == $teamBattle->team_b_user_1)
                <div class="neon-card p-4">
                    <h5 class="section-header mb-3">
                        <i class="bi bi-gear-wide-connected section-icon" style="color: #00f0ff;"></i> BATTLE ACTIONS
                    </h5>
                    
                    <div class="d-flex gap-3 flex-wrap align-items-center">
                        @if($teamBattle->status == 'active')
                            <button class="btn btn-neon btn-sm" wire:click="declareWin('A')">
                                <i class="bi bi-trophy"></i> TEAM A WON
                            </button>
                            <button class="btn btn-neon-magenta btn-sm" wire:click="declareWin('B')">
                                <i class="bi bi-trophy"></i> TEAM B WON
                            </button>
                            <button class="btn btn-outline-danger btn-sm" wire:click="cancelBattle">
                                <i class="bi bi-x-circle"></i> CANCEL
                            </button>
                        @endif
                        
                        @if(!$teamBattle->marshall_id && in_array($teamBattle->status, ['pending', 'ready', 'active']))
                            <div class="input-group input-group-sm" style="max-width: 250px;">
                                <input type="number" wire:model="marshallNomineeId" class="form-control bg-dark text-white border-secondary" placeholder="Marshall User ID">
                                <button class="btn btn-outline-warning" wire:click="electMarshall()">ELECT</button>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif(Auth::id() == $teamBattle->marshall_id && $teamBattle->status == 'active')
                <div class="neon-card p-4">
                    <h5 class="section-header mb-3">
                        <i class="bi bi-gear-wide-connected section-icon" style="color: #ffdd00;"></i> MARSHALL ACTIONS
                    </h5>
                    <div class="d-flex gap-3 flex-wrap align-items-center">
                        <button class="btn btn-neon btn-sm" wire:click="declareWin('A')">TEAM A WON</button>
                        <button class="btn btn-neon-magenta btn-sm" wire:click="declareWin('B')">TEAM B WON</button>
                        <button class="btn btn-outline-danger btn-sm" wire:click="cancelBattle">CANCEL MATCH</button>
                    </div>
                </div>
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
            <div class="custom-modal p-4 neon-card" style="max-width: 600px; width: 90%;">
                <h4 class="orbitron text-cyan mb-4">JOIN TEAM {{ $joiningTeam }}</h4>
                
                <div class="mb-4">
                    <label class="form-label small">SELECT YOUR CARD TO BET</label>
                    <div class="row g-2" style="max-height: 300px; overflow-y: auto;">
                        @foreach($myEligibleCards as $card)
                            <div class="col-4">
                                <div class="selectable-card {{ $selectedCardId == $card->id ? 'selected' : '' }}" 
                                     wire:click="$set('selectedCardId', {{ $card->id }})">
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
                                    <div style="font-size: 0.6rem; line-height: 1;" class="text-center text-truncate">{{ $card->template->card_title }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('selectedCardId') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-neon w-100" wire:click="confirmJoin">CONFIRM JOIN</button>
                    <button class="btn btn-outline-secondary w-100" wire:click="$set('joiningTeam', '')">CANCEL</button>
                </div>
            </div>
        </div>
    @endif

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
