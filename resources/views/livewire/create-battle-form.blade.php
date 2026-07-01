
<div class="create-battle-workflow">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="neon-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="orbitron mb-0 text-cyan">
                        <i class="bi bi-people-fill"></i> CREATE NEW BATTLE
                    </h3>
                </div>

                @if(session()->has('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form wire:submit.prevent="createBattle">
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
                        <label class="form-label">4. PLAYER COUNT (PER TEAM)</label>
                        <select wire:model="noPlayersPerTeam" class="form-select">
                            @for($i=1; $i<=6; $i++)
                                <option value="{{ $i }}">
                                    @if($i == 1)
                                        {{ $i }} on {{ $i }}
                                    @else
                                        {{ $i }} vs {{ $i }}
                                    @endif
                                </option>
                            @endfor
                        </select>
                        @error('noPlayersPerTeam') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">5. BATTLE MODE</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check p-0">
                                    <input type="radio" class="btn-check" name="mode" id="mode_standard" value="standard" wire:model="mode">
                                    <label class="btn btn-outline-info w-100 p-3 text-start d-flex align-items-center h-100" for="mode_standard" style="border-width: 2px;">
                                        <div class="me-3">
                                            <i class="bi bi-shield-shaded" style="font-size: 2rem; color: #00f0ff;"></i>
                                        </div>
                                        <div>
                                            <strong class="d-block orbitron text-cyan" style="font-size: 0.9rem;">STANDARD MODE</strong>
                                            <span class="text-white-50 small" style="font-size: 0.75rem;">Losing card loses 1 heart. Safer for practice and casual matches.</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check p-0">
                                    <input type="radio" class="btn-check" name="mode" id="mode_no_quarter" value="no_quarter" wire:model="mode">
                                    <label class="btn btn-outline-danger w-100 p-3 text-start d-flex align-items-center h-100" for="mode_no_quarter" style="border-width: 2px;">
                                        <div class="me-3">
                                            <i class="bi bi-skull-fill" style="font-size: 2rem; color: #ff00ff;"></i>
                                        </div>
                                        <div>
                                            <strong class="d-block orbitron text-magenta" style="font-size: 0.9rem;">NO QUARTER MODE</strong>
                                            <span class="text-white-50 small" style="font-size: 0.75rem;">Winner takes all! Loser loses their card instantly. Winner's wins multiplier based on opponent's hearts.</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @error('mode') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">6. BATTLE TERMS</label>
                        <textarea wire:model="battleTerms" class="form-control" rows="3" placeholder="Define the rules of engagement..."></textarea>
                        @error('battleTerms') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">7. SELECT YOUR CARD TO BET</label>
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
                                                                                :title="$card->is_censored ? '[CENSORED]' : $card->template->card_title"
                                                                                :game="$card->template->gameTitle->title ?? 'GAME'"
                                                                                :creator="$card->originalOwner->username ?? 'Creator'"
                                                                                :isCreatorVerified="$card->originalOwner->is_verified ?? false"
                                                                                :isCreatorUntrustworthy="$card->originalOwner->is_untrustworthy ?? false"
                                                                                :quote="$card->is_censored ? '[Content hidden pending review]' : $card->template->quote"
                                                                                :image="$card->is_censored ? '' : $card->template->display_photo"
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
                                                                                :isCensored="$card->is_censored"
                                                                            />
                                                                        </div>
                                                                        @if($selectedCardId == $card->id)
                                                                            <div class="selection-overlay">
                                                                                <i class="bi bi-check-circle-fill" style="font-size: 1.5rem;"></i>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="card-info mt-2 text-center">
                                                                        <div class="small fw-bold text-truncate" style="font-size: 0.7rem;">{{ $card->is_censored ? '[CENSORED]' : $card->template->card_title }}</div>
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

                    <button type="submit" class="btn btn-neon w-100 mt-4 py-3 orbitron">
                        <i class="bi bi-crosshair"></i> CREATE BATTLE ROOM
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
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
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: #00f0ff; border-radius: 8px;
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

            const workflowContainer = document.querySelector('.create-battle-workflow');
            if (workflowContainer) {
                observer.observe(workflowContainer, { childList: true, subtree: true });
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
