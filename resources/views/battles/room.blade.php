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

<div class="team-battle-room" @if(!$joiningTeam) @endif style="overflow: visible;">
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
                        @if($u)
                            <div class="mb-2 text-center text-truncate">
                                <span class="fw-bold @if($isMe) text-cyan @endif">
                                    @if($i == 1)
                                    👑
                                    @endif
                                    {{ $isMe ? 'YOU' : $u->username }}</span>
                            </div>
                            <div class="{{ $cardClass }}" style="{{ $cardStyle }}">
                                <x-digital-card 
                                    id="card_a_{{ $i }}_{{ $c->id }}"
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
                                    <button type="button" class="btn btn-outline-cyan btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal">JOIN</button>
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
                        @if($u)
                            <div class="mb-2 text-center text-truncate">
                                <span class="fw-bold @if($isMe) text-magenta @endif">
                                    @if($i == 1)
                                    👑
                                    @endif
                                    {{ $isMe ? 'YOU' : $u->username }}</span>
                            </div>
                            <div class="{{ $cardClass }}" style="{{ $cardStyle }}">
                                <x-digital-card 
                                    id="card_b_{{ $i }}_{{ $c->id }}"
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
                                    <button type="button" class="btn btn-outline-magenta btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal">JOIN</button>
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
                    <button type="button" class="btn btn-neon w-50 py-2 orbitron" data-bs-dismiss="modal" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="confirmJoin">CONFIRM JOIN</span>
                        <span wire:loading wire:target="confirmJoin"><i class="bi bi-hourglass-split"></i> JOINING...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Rename Team Modal -->
    <div class="modal fade" id="renameTeamModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" style="background: rgba(0, 0, 0, 0.8);">
        <div class="modal-dialog modal-dialog-centered">
            @if(Auth::id() == $battle->team_a_user_1)
            <div class="modal-content p-4 neon-card" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #00f0ff; backdrop-filter: blur(20px);">
                <h5 class="orbitron text-cyan mb-4 text-center">RENAME TEAM A</h5>
                <div class="mb-4">
                    <input type="text" class="form-control bg-dark text-white border-cyan text-center orbitron" placeholder="Enter new team name">
                </div>
                <div class="d-flex gap-3">
                    <button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">CANCEL</button>
                    <button type="button" class="btn btn-neon w-50 orbitron" data-bs-dismiss="modal">SAVE</button>
                </div>
            </div>
            @elseif(Auth::id() == $battle->team_b_user_1)
            <div class="modal-content p-4 neon-card" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #ff00ff; box-shadow: 0 0 30px rgba(255, 0, 255, 0.2); backdrop-filter: blur(20px);">
                <h5 class="orbitron text-magenta mb-4 text-center">RENAME TEAM B</h5>
                <div class="mb-4">
                    <input type="text" class="form-control bg-dark text-white border-magenta text-center orbitron" placeholder="Enter new team name">
                </div>
                <div class="d-flex gap-3">
                    <button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">CANCEL</button>
                    <button type="button" class="btn btn-neon-magenta w-50 orbitron" data-bs-dismiss="modal">SAVE</button>
                </div>
            </div>
            @endif
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
                                    <i class="bi bi-x-circle-fill ms-2" style="cursor: pointer;"></i>
                                </span>
                            @else
                                <input type="text" class="border-0 bg-transparent text-white flex-grow-1 px-2" placeholder="{{ $existingUsername ? 'Currently: ' . $existingUsername : 'Search username...' }}" autocomplete="off" style="outline: none; box-shadow: none;">
                            @endif
                        </div>
                        
                        @if(count($marshallSearchResults) > 0 && !$marshallNomineeId)
                            <div class="position-absolute w-100 mt-1" style="z-index: 1050; max-height: 200px; overflow-y: auto; background: rgba(10, 10, 30, 0.95); border: 1px solid #ffdd00; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                                @foreach($marshallSearchResults as $user)
                                    <div class="p-2 d-flex align-items-center gap-2" style="cursor: pointer; border-bottom: 1px solid rgba(255, 221, 0, 0.1);">
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
                    <button type="button" class="btn btn-neon w-100" style="border-color: #ffdd00; color: #ffdd00;" data-bs-dismiss="modal">ELECT USER</button>
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
                                    <i class="bi bi-x-circle-fill ms-2" style="cursor: pointer;"></i>
                                </span>
                            @else
                                <input type="text" class="border-0 bg-transparent text-white flex-grow-1 px-2" placeholder="Search username..." autocomplete="off" style="outline: none; box-shadow: none;">
                            @endif
                        </div>
                        
                        @if(count($inviteSearchResults) > 0 && !$inviteNomineeId)
                            <div class="position-absolute w-100 mt-1" style="z-index: 1050; max-height: 200px; overflow-y: auto; background: rgba(10, 10, 30, 0.95); border: 1px solid #00f0ff; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                                @foreach($inviteSearchResults as $user)
                                    <div class="p-2 d-flex align-items-center gap-2" style="cursor: pointer; border-bottom: 1px solid rgba(0, 240, 255, 0.1);">
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
                    <button type="button" class="btn btn-neon w-100" data-bs-dismiss="modal" @if(!$inviteNomineeId) disabled @endif>SEND INVITE</button>
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
                        <button class="btn btn-neon-magenta w-100">
                            <i class="bi bi-check-lg"></i> AGREE & CANCEL
                        </button>
                        <button class="btn btn-outline-secondary w-100" style="border-color: #555;">
                            <i class="bi bi-x-lg"></i> REJECT
                        </button>
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