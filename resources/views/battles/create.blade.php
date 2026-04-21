@extends('layouts.app')

@section('title', 'Create Battle')

@section('content')
<h1 class="page-title">
    <span class="page-title-accent"><i class="bi bi-crosshair"></i></span> CREATE BATTLE
</h1>

<form method="POST" action="{{ route('battles.store') }}" id="create-battle-form">
    @csrf
    <input type="hidden" name="card_id" id="selected_card_id">

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="neon-card p-4">
                
                @if($games->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon">🃏</div>
                        <div class="empty-text">You need at least one Digital Card to create a battle.</div>
                        <a href="{{ route('templates.index') }}" class="btn btn-neon btn-neon-sm">Forge a Card</a>
                    </div>
                @else

                <!-- Game Title Selection -->
                <div class="mb-4">
                    <label for="game_title_id" class="form-label">1. SELECT GAME</label>
                    <select class="form-select" id="game_title_id" name="game_title_id">
                        <option value="">-- Choose a Game --</option>
                        @foreach($games as $game)
                            <option value="{{ $game->id }}">{{ $game->title }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Terms & Conditions -->
                <div class="mb-4">
                    <label for="terms" class="form-label">2. DEFINE TERMS (OPTIONAL)</label>
                    <textarea name="terms" id="terms" class="form-control" rows="3" placeholder="e.g. Best of 3, winner takes card."></textarea>
                </div>

                <!-- Card Selection -->
                <div id="card-selection-section" style="display: none;">
                    <label class="form-label">3. CHOOSE YOUR CARD TO BET</label>
                    
                    <!-- Desktop Grid -->
                    <div id="card-selection-grid" class="card-grid d-none d-md-grid">
                        {{-- Cards will be injected here by JavaScript --}}
                    </div>

                    <!-- Mobile Carousel -->
                    <div id="card-selection-carousel" class="carousel slide d-md-none" data-bs-interval="false">
                        <div class="carousel-inner" id="carousel-inner-content">
                            {{-- Carousel items will be injected here by JavaScript --}}
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#card-selection-carousel" data-bs-slide="prev" style="width: 10%; background: linear-gradient(90deg, rgba(0,0,0,0.5), transparent);">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#card-selection-carousel" data-bs-slide="next" style="width: 10%; background: linear-gradient(-90deg, rgba(0,0,0,0.5), transparent);">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>

                    <div class="mt-4 pt-3 border-top" style="border-color: rgba(0, 240, 255, 0.1) !important;">
                        <button type="submit" id="submit-battle-btn" class="btn btn-neon w-100" style="font-size: 1.1rem; padding: 12px;" disabled>
                            <i class="bi bi-crosshair"></i> CREATE BATTLE ROOM
                        </button>
                    </div>
                </div>
                
                @endif

            </div>
        </div>
    </div>
</form>

<!-- Card data for JavaScript -->
<script>

    @php 
        $allCards = $cards->map(function($card) {
            return [
                'id' => $card->id,
                'game_title_id' => $card->template->game_title_id,
                'options' => [
                    'id' => 'mini_card_' . $card->id . '_' . uniqid(),
                    'mode' => 'thumbnail',
                    'rarity' => (string)($card->rarity ?? 'common'),
                    'detailUrl' => (string)route('cards.show', $card),
                    'title' => (string)$card->template->card_title,
                    'game' => (string)($card->template->gameTitle->title ?? 'GAME'),
                    'creator' => (string)($card->originalOwner->username ?? 'Creator'),
                    'quote' => (string)$card->template->quote,
                    'backgroundColor' => (string)$card->template->background_color,
                    'borderColor' => (string)$card->template->border_color,
                    'sectionColor' => (string)$card->template->section_color,
                    'primaryTextColor' => (string)$card->template->primary_text_color,
                    'secondaryTextColor' => (string)$card->template->secondary_text_color,
                    'image' => (string)$card->template->display_photo,
                    'wins' => (int)$card->wins,
                    'losses' => (int)$card->losses,
                    'lifePoints' => (int)$card->life_points,
                    'status' => (string)$card->status,
                    'rankLevel' => (int)$card->level,
                    'serialNumber' => (int)$card->serial_number,
                ]
            ];
        });
    @endphp
    
    const allCards = @json($allCards);

    function initializeCard(options) {
        const canvasId = options.id;
        if (!window.digitalCardRenderers) window.digitalCardRenderers = {};
        
        if (typeof DigitalCardRenderer === 'undefined') {
            console.error('DigitalCardRenderer class not found.');
            return;
        }

        const renderer = new DigitalCardRenderer(canvasId);
        renderer.draw(options);

        const modalEl = document.getElementById('modal_' + canvasId);
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            const fsCanvasId = 'fullscreen_' + canvasId;
            const fsRenderer = new DigitalCardRenderer(fsCanvasId);

            modalEl.addEventListener('shown.bs.modal', function () {
                fsRenderer.draw(options);
            });
        }
    }
</script>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof allCards === 'undefined') return;

    const gameSelect = document.getElementById('game_title_id');
    const cardSelectionSection = document.getElementById('card-selection-section');
    const cardSelectionGrid = document.getElementById('card-selection-grid');
    const carouselInner = document.getElementById('carousel-inner-content');
    const form = document.getElementById('create-battle-form');
    const selectedCardIdInput = document.getElementById('selected_card_id');
    const submitBtn = document.getElementById('submit-battle-btn');

    let allCardButtons = [];

    gameSelect.addEventListener('change', function() {
        const selectedGameId = this.value;
        cardSelectionGrid.innerHTML = '';
        carouselInner.innerHTML = '';
        allCardButtons = [];
        selectedCardIdInput.value = '';
        if (submitBtn) submitBtn.disabled = true;

        // Clear any old modals from the body
        document.querySelectorAll('.modal.fade').forEach(modal => modal.remove());

        if (!selectedGameId) {
            cardSelectionSection.style.display = 'none';
            return;
        }

        const filteredCards = allCards.filter(card => card.game_title_id == selectedGameId);

        if (filteredCards.length > 0) {
            filteredCards.forEach((cardData, index) => {
                const options = cardData.options;
                
                // Add to Desktop Grid
                const gridItemDiv = document.createElement('div');
                gridItemDiv.classList.add('d-flex', 'flex-column', 'align-items-center');

                const cardHtml = `
                    <div style="cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" data-bs-toggle="modal" data-bs-target="#modal_${options.id}">
                        <img id="img_${options.id}" src="" alt="${options.title}" style="width: 100%; height: auto; border-radius: 10px; box-shadow: 0 0 15px ${options.borderColor}40; display: block;" />
                        <canvas id="${options.id}" width="350" height="490" style="display: none;"></canvas>
                    </div>
                `;
                gridItemDiv.innerHTML = cardHtml;

                const selectButton = document.createElement('button');
                selectButton.type = 'button';
                selectButton.classList.add('btn', 'btn-outline-neon', 'btn-neon-sm', 'w-100', 'mt-2');
                selectButton.innerHTML = '<i class="bi bi-circle"></i> SELECT';
                selectButton.dataset.cardId = cardData.id;
                selectButton.addEventListener('click', () => selectCard(cardData.id, selectButton));
                allCardButtons.push(selectButton);
                
                gridItemDiv.appendChild(selectButton);
                cardSelectionGrid.appendChild(gridItemDiv);

                // Add to Mobile Carousel
                const carouselItem = document.createElement('div');
                carouselItem.classList.add('carousel-item');
                if (index === 0) carouselItem.classList.add('active');

                const carouselOptions = JSON.parse(JSON.stringify(options));
                carouselOptions.id = options.id + '_carousel';

                carouselItem.innerHTML = `
                    <div class="px-4 d-flex flex-column align-items-center">
                        <div style="cursor: pointer; width: 100%; max-width: 280px;" data-bs-toggle="modal" data-bs-target="#modal_${carouselOptions.id}">
                            <img id="img_${carouselOptions.id}" src="" alt="${options.title}" style="width: 100%; height: auto; border-radius: 10px; box-shadow: 0 0 15px ${options.borderColor}40; display: block;" />
                            <canvas id="${carouselOptions.id}" width="350" height="490" style="display: none;"></canvas>
                        </div>
                        <button type="button" class="btn btn-outline-neon btn-neon-sm w-100 mt-3 max-width-280" style="max-width: 280px;" data-card-id="${cardData.id}">
                            <i class="bi bi-circle"></i> SELECT THIS CARD
                        </button>
                    </div>
                `;

                const carouselSelectBtn = carouselItem.querySelector('button');
                carouselSelectBtn.addEventListener('click', () => selectCard(cardData.id, carouselSelectBtn));
                allCardButtons.push(carouselSelectBtn);
                
                carouselInner.appendChild(carouselItem);
                
                // Create Modal for Grid Card
                createModal(options);
                // Create Modal for Carousel Card
                createModal(carouselOptions);

                setTimeout(() => {
                    initializeCard(options);
                    initializeCard(carouselOptions);
                }, 0);
            });
            cardSelectionSection.style.display = 'block';

            // Auto-select if query parameter is present
            const preSelectedCardId = "{{ $preSelectedCardId ?? '' }}";
            if (preSelectedCardId) {
                const targetBtn = allCardButtons.find(b => b.dataset.cardId == preSelectedCardId);
                if (targetBtn) {
                    selectCard(preSelectedCardId, targetBtn);
                }
            }

        } else {
            cardSelectionSection.style.display = 'none';
        }
    });

    function selectCard(cardId, button) {
        selectedCardIdInput.value = cardId;
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-crosshair"></i> CREATE BATTLE ROOM';

        // Reset all buttons
        allCardButtons.forEach(btn => {
            btn.classList.remove('btn-neon-lime');
            btn.classList.add('btn-outline-neon');
            if (btn.innerText.includes('THIS')) {
                btn.innerHTML = '<i class="bi bi-circle"></i> SELECT THIS CARD';
            } else {
                btn.innerHTML = '<i class="bi bi-circle"></i> SELECT';
            }
        });

        // Highlight the matched buttons (both grid and carousel)
        const matchingButtons = allCardButtons.filter(b => b.dataset.cardId == cardId);
        matchingButtons.forEach(btn => {
            btn.classList.remove('btn-outline-neon');
            btn.classList.add('btn-neon-lime');
            if (btn.innerText.includes('THIS')) {
                btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> SELECTED';
            } else {
                btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> SELECTED';
            }
        });
    }

    form.addEventListener('submit', function() {
        if (!selectedCardIdInput.value) {
            alert('Please select a card first.');
            return false;
        }
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> CREATING...';
        submitBtn.disabled = true;
    });

    // Handle pre-selected game title
    const preSelectedGameId = "{{ $preSelectedGameId ?? '' }}";
    if (preSelectedGameId) {
        gameSelect.value = preSelectedGameId;
        gameSelect.dispatchEvent(new Event('change'));
    }

    function createModal(options) {
        const modalHtml = `
        <div class="modal fade" id="modal_${options.id}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg d-flex justify-content-center">
                <div class="modal-content" style="background: transparent; border: none; align-items: center; box-shadow: none;">
                    <div class="digital-card rarity-${options.rarity}" style="padding: 4px; border-radius: 16px; width: 100%; max-width: 500px; margin: 0 auto;">
                        <canvas id="fullscreen_${options.id}" width="500" height="700" class="digital-card-canvas" style="border-radius: 12px; display: block; position: relative; z-index: 1; max-width: 100%; height: auto;"></canvas>
                    </div>
                    <div class="mt-4 text-center d-flex gap-3 justify-content-center">
                        <a href="${options.detailUrl}" class="btn btn-neon"><i class="bi bi-info-circle"></i> DETAILS</a>
                        <button type="button" class="btn btn-neon-magenta" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i> CLOSE</button>
                    </div>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

});
</script>
@endsection
