@extends('layouts.app')

@section('title', $user->username . "'s Profile")

@section('content')
<!-- Profile Header -->
<div class="profile-header mb-4">
    <div class="row align-items-center g-3">
        <div class="col-auto">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->username }}" class="profile-avatar">
        </div>
        <div class="col">
            <h1 style="font-family: 'Orbitron', sans-serif; font-size: 1.5rem; margin-bottom: 0.25rem; color: #00f0ff;">
                @<x-username :user="$user" />
            </h1>
            @if($user->bio)
                <p style="color: #8888aa; font-size: 0.9rem; margin-bottom: 0.5rem;">{{ $user->bio }}</p>
            @endif
            <div style="font-size: 0.75rem; color: #555577;">
                <i class="bi bi-calendar3"></i> Joined {{ $user->created_at->format('M Y') }}
            </div>
        </div>
        @if($isOwner)
        <div class="col-auto">
            <a href="{{ route('profile.edit') }}" class="btn btn-neon btn-neon-sm">
                <i class="bi bi-gear-fill"></i> Edit
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md">
        <div class="stat-box">
            <div class="stat-value">{{ $stats['total_cards'] }}</div>
            <div class="stat-label">Cards</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-box">
            <div class="stat-value" style="color: #ffdd00;">{{ $stats['total_trophies'] }}</div>
            <div class="stat-label">Trophies</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-box">
            <div class="stat-value" style="color: #ff00ff;">{{ $stats['failed_battles_pct'] }}%</div>
            <div class="stat-label">Failed</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-box">
            <div class="stat-value" style="color: #ff6600;">{{ $stats['completed_battles_pct'] }}%</div>
            <div class="stat-label">Completed</div>
        </div>
    </div>
</div>

<div class="row g-4">

    <!-- Filters -->
    @if($ownCards->count() > 0 || $trophies->count() > 0)
    <div class="col-12 mb-2 d-flex flex-wrap gap-2">
        <select id="filter-game" class="form-select bg-dark text-white border-info" style="max-width: 200px; background-color: #111122;">
            <option value="">All Games</option>
            @foreach($availableGames as $game)
                <option value="{{ $game->id }}">{{ $game->title }}</option>
            @endforeach
        </select>
        <select id="filter-level" class="form-select bg-dark text-white border-info" style="max-width: 150px; background-color: #111122;">
            <option value="">All Levels</option>
            @foreach($availableLevels as $lvl)
                <option value="{{ $lvl }}">Level {{ $lvl }}</option>
            @endforeach
        </select>
    </div>
    @endif

    <!-- Digital Cards -->
    <div class="col-lg-6">
        <h5 class="section-header">
            <i class="bi bi-suit-diamond-fill section-icon"></i> DIGITAL CARDS
        </h5>

        @if($ownCards->count() > 0)
            <!-- Desktop Grid -->
            <div class="card-grid d-none d-md-grid">
                @foreach($ownCards as $card)
                    <div class="filterable-card" data-game="{{ $card->template->gameTitle->id }}" data-level="{{ $card->level }}">
                        @include('partials.card-mini', ['card' => $card])
                    </div>
                @endforeach
            </div>
            
            <!-- Mobile Carousel -->
            <div class="d-md-none">
                <div id="ownCardsCarousel" class="carousel slide" data-bs-interval="false">
                    <div class="carousel-inner">
                        @foreach($ownCards as $index => $card)
                            <div class="carousel-item filterable-carousel-item {{ $index === 0 ? 'active' : '' }}" data-game="{{ $card->template->gameTitle->id }}" data-level="{{ $card->level }}">
                                <div class="px-4 d-flex flex-column align-items-center">
                                    @include('partials.card-mini', ['card' => $card])
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#ownCardsCarousel" data-bs-slide="prev" style="width: 10%;">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#ownCardsCarousel" data-bs-slide="next" style="width: 10%;">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">🃏</div>
                <div class="empty-text">No cards yet</div>
            </div>
        @endif
    </div>

    <!-- Trophies -->
    <div class="col-lg-6">
        <h5 class="section-header">
            <i class="bi bi-trophy-fill section-icon" style="color: #ffdd00;"></i> TROPHY COLLECTION
        </h5>

        @if($trophies->count() > 0)
            <!-- Desktop Grid -->
            <div class="card-grid d-none d-md-grid">
                @foreach($trophies as $card)
                    <div class="filterable-card" data-game="{{ $card->template->gameTitle->id }}" data-level="{{ $card->level }}">
                        @include('partials.card-mini', ['card' => $card, 'isTrophy' => true])
                    </div>
                @endforeach
            </div>
            
            <!-- Mobile Carousel -->
            <div class="d-md-none">
                <div id="trophiesCarousel" class="carousel slide" data-bs-interval="false">
                    <div class="carousel-inner">
                        @foreach($trophies as $index => $card)
                            <div class="carousel-item filterable-carousel-item {{ $index === 0 ? 'active' : '' }}" data-game="{{ $card->template->gameTitle->id }}" data-level="{{ $card->level }}">
                                <div class="px-4 d-flex flex-column align-items-center">
                                    @include('partials.card-mini', ['card' => $card, 'isTrophy' => true])
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#trophiesCarousel" data-bs-slide="prev" style="width: 10%;">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#trophiesCarousel" data-bs-slide="next" style="width: 10%;">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">🏆</div>
                <div class="empty-text">No trophies collected</div>
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterGame = document.getElementById('filter-game');
        const filterLevel = document.getElementById('filter-level');
        
        if (!filterGame || !filterLevel) return;

        function applyFilters() {
            const gameVal = filterGame.value;
            const levelVal = filterLevel.value;

            // Filter Desktop Grid Cards
            document.querySelectorAll('.filterable-card').forEach(card => {
                const matchGame = gameVal === '' || card.dataset.game === gameVal;
                const matchLevel = levelVal === '' || card.dataset.level === levelVal;
                card.style.display = (matchGame && matchLevel) ? 'block' : 'none';
            });

            // Filter Mobile Carousel Cards
            ['ownCardsCarousel', 'trophiesCarousel'].forEach(carouselId => {
                const carousel = document.getElementById(carouselId);
                if (!carousel) return;
                
                const items = carousel.querySelectorAll('.filterable-carousel-item');
                let firstVisible = null;
                
                items.forEach(item => {
                    item.classList.remove('active'); // Remove active from all first
                    const matchGame = gameVal === '' || item.dataset.game === gameVal;
                    const matchLevel = levelVal === '' || item.dataset.level === levelVal;
                    
                    if (matchGame && matchLevel) {
                        item.classList.add('d-block');
                        item.classList.remove('d-none');
                        if (!firstVisible) firstVisible = item;
                    } else {
                        item.classList.remove('d-block');
                        item.classList.add('d-none');
                    }
                });
                
                // Reassign active to the first visible item to keep the carousel working
                if (firstVisible) {
                    firstVisible.classList.add('active');
                }
            });
        }

        filterGame.addEventListener('change', applyFilters);
        filterLevel.addEventListener('change', applyFilters);
    });
</script>
@endsection
