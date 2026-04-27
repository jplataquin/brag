
<div class="create-battle-workflow">
    @if($step == 1)
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="neon-card p-5 text-center">
                    <h3 class="mb-5 orbitron text-cyan">CHOOSE BATTLE MODE</h3>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="battle-mode-option p-4 border rounded cursor-pointer hover-glow" 
                                 wire:click="setBattleType('1on1')"
                                 style="border-color: rgba(0, 240, 255, 0.3) !important; background: rgba(0, 240, 255, 0.05);">
                                <div class="mb-3" style="font-size: 3rem;">⚔️</div>
                                <h4 class="orbitron">1 ON 1</h4>
                                <p class="text-muted small">The classic duel. Two players, one winner, winner takes all (or damage).</p>
                                <button class="btn btn-neon btn-sm mt-2">SELECT</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="battle-mode-option p-4 border rounded cursor-pointer hover-glow" 
                                 wire:click="setBattleType('team')"
                                 style="border-color: rgba(255, 0, 255, 0.3) !important; background: rgba(255, 0, 255, 0.05);">
                                <div class="mb-3" style="font-size: 3rem;">👥</div>
                                <h4 class="orbitron">TEAM BATTLE</h4>
                                <p class="text-muted small">Collaborate with allies. Multi-player teams battling for dominance.</p>
                                <button class="btn btn-neon-magenta btn-sm mt-2">SELECT</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif($step == 2)
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="neon-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="orbitron mb-0 text-cyan">
                            @if($battleType == '1on1')
                                <i class="bi bi-person-fill"></i> 1 ON 1 BATTLE
                            @else
                                <i class="bi bi-people-fill"></i> TEAM BATTLE
                            @endif
                        </h3>
                        <button class="btn btn-outline-secondary btn-sm" wire:click="back">
                            <i class="bi bi-arrow-left"></i> BACK
                        </button>
                    </div>

                    @if(session()->has('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form wire:submit.prevent="createBattle">
                        @if($battleType == '1on1')
                            <!-- 1 on 1 Form -->
                            <div class="mb-4">
                                <label class="form-label">1. CHOOSE YOUR CARD TO BET</label>
                                <div class="card-selection-area">
                                    <div class="row g-3">
                                        @foreach($cards as $card)
                                            <div class="col-md-4 col-6">
                                                <div class="selectable-card {{ $selectedCardId == $card->id ? 'selected' : '' }}" 
                                                     wire:click="$set('selectedCardId', {{ $card->id }})">
                                                    <div class="card-img-wrapper">
                                                        <div style="pointer-events: none;" wire:ignore>
                                                            <x-digital-card 
                                                                id="card_1on1_{{ $card->id }}"
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
                                                        @if($selectedCardId == $card->id)
                                                            <div class="selection-overlay">
                                                                <i class="bi bi-check-circle-fill"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="card-info mt-2 text-center">
                                                        <div class="small fw-bold text-truncate">{{ $card->template->card_title }}</div>
                                                        <div class="text-muted" style="font-size: 0.7rem;">LVL {{ $card->level }} | {{ $card->template->gameTitle->title }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('selectedCardId') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="terms" class="form-label">2. DEFINE TERMS (OPTIONAL)</label>
                                <textarea wire:model="battleTerms" id="terms" class="form-control" rows="3" placeholder="e.g. Best of 3, winner takes card."></textarea>
                                @error('battleTerms') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                        @else
                            <!-- Team Battle Form -->
                            <div class="mb-4">
                                <label class="form-label">1. SELECT GAME TITLE</label>
                                <select wire:model.live="gameTitleId" class="form-select">
                                    <option value="">-- Choose a Game --</option>
                                    @foreach($games as $game)
                                        <option value="{{ $game->id }}">{{ $game->title }}</option>
                                    @endforeach
                                </select>
                                @error('gameTitleId') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">2. TEAM A NAME</label>
                                    <input type="text" wire:model="teamNameA" class="form-control" placeholder="Enter Team A Name">
                                    @error('teamNameA') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">3. TEAM B NAME</label>
                                    <input type="text" wire:model="teamNameB" class="form-control" placeholder="Enter Team B Name">
                                    @error('teamNameB') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">4. PLAYERS PER TEAM</label>
                                <select wire:model="noPlayersPerTeam" class="form-select">
                                    @for($i=2; $i<=6; $i++)
                                        <option value="{{ $i }}">{{ $i }} Players</option>
                                    @endfor
                                </select>
                                @error('noPlayersPerTeam') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label">5. BATTLE TERMS</label>
                                <textarea wire:model="battleTerms" class="form-control" rows="3" placeholder="Define the rules of engagement..."></textarea>
                                @error('battleTerms') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label">6. SELECT YOUR CARD TO BET</label>
                                <div class="card-selection-area">
                                    @if($gameTitleId)
                                        @php
                                            $filteredCards = $cards->filter(fn($c) => $c->template->game_title_id == $gameTitleId);
                                        @endphp
                                        @if($filteredCards->isNotEmpty())
                                            <div id="teamCardCarousel" class="carousel slide" data-bs-ride="false">
                                                <div class="carousel-inner p-2">
                                                    @foreach($filteredCards->chunk(3) as $chunkIndex => $chunk)
                                                        <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
                                                            <div class="row g-3 justify-content-center">
                                                                @foreach($chunk as $card)
                                                                    <div class="col-4">
                                                                        <div class="selectable-card {{ $selectedCardId == $card->id ? 'selected' : '' }}" 
                                                                             wire:click="$set('selectedCardId', {{ $card->id }})">
                                                                            <div class="card-img-wrapper">
                                                                                <div style="pointer-events: none;" wire:ignore>
                                                                                    <x-digital-card 
                                                                                        id="card_team_{{ $card->id }}"
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
                                                                                @if($selectedCardId == $card->id)
                                                                                    <div class="selection-overlay">
                                                                                        <i class="bi bi-check-circle-fill" style="font-size: 1.5rem;"></i>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                            <div class="card-info mt-2 text-center">
                                                                                <div class="small fw-bold text-truncate" style="font-size: 0.7rem;">{{ $card->template->card_title }}</div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @if($filteredCards->count() > 3)
                                                    <button class="carousel-control-prev" type="button" data-bs-target="#teamCardCarousel" data-bs-slide="prev" style="width: 5%;">
                                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                        <span class="visually-hidden">Previous</span>
                                                    </button>
                                                    <button class="carousel-control-next" type="button" data-bs-target="#teamCardCarousel" data-bs-slide="next" style="width: 5%;">
                                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                        <span class="visually-hidden">Next</span>
                                                    </button>
                                                @endif
                                            </div>
                                        @else
                                            <div class="alert alert-warning small">You have no eligible cards for this game title.</div>
                                        @endif
                                    @else
                                        <div class="text-muted italic small text-center p-4 border border-secondary border-dashed rounded">Please select a game title first to see eligible cards.</div>
                                    @endif
                                    @error('selectedCardId') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif

                        <button type="submit" class="btn btn-neon w-100 mt-4 py-3 orbitron">
                            <i class="bi bi-crosshair"></i> CREATE BATTLE ROOM
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <style>
        .battle-mode-option {
            transition: all 0.3s ease;
        }
        .battle-mode-option:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 240, 255, 0.2);
            border-color: rgba(0, 240, 255, 0.8) !important;
        }
        .selectable-card {
            cursor: pointer;
            border: 2px solid transparent;
            border-radius: 12px;
            padding: 5px;
            transition: all 0.2s ease;
        }
        .selectable-card:hover {
            background: rgba(0, 240, 255, 0.1);
        }
        .selectable-card.selected {
            border-color: #00f0ff;
            background: rgba(0, 240, 255, 0.1);
        }
        .card-img-wrapper {
            position: relative;
        }
        .selection-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 240, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #00f0ff;
            border-radius: 8px;
        }
        .cursor-pointer { cursor: pointer; }
        .hover-glow:hover { box-shadow: 0 0 15px currentColor; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            function initializeVisibleCards() {
                if (typeof DigitalCardRenderer === 'undefined') return;
                if (!window.digitalCardRenderers) window.digitalCardRenderers = {};
                
                document.querySelectorAll('canvas[data-card-options]').forEach(canvas => {
                    // Check if the canvas hasn't been rendered yet
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

            // Observe the DOM for new canvases (Livewire updates)
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

            // Start observing
            const workflowContainer = document.querySelector('.create-battle-workflow');
            if (workflowContainer) {
                observer.observe(workflowContainer, { childList: true, subtree: true });
            }

            // Fallback for initial load
            const interval = setInterval(() => {
                if (typeof DigitalCardRenderer !== 'undefined') {
                    initializeVisibleCards();
                    clearInterval(interval);
                }
            }, 100);
            
            // Safety timeout for the interval
            setTimeout(() => clearInterval(interval), 5000);
        });
    </script>
</div>
