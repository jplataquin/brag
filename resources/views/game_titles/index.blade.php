@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-uppercase mb-2" style="color: var(--neon-cyan); text-shadow: 0 0 15px rgba(0, 240, 255, 0.6); font-family: 'Orbitron', sans-serif;">
            <i class="bi bi-controller"></i> Supported Games
        </h1>
        <p class="text-secondary lead">Explore the game titles supported by Brag and start forging your legacy.</p>
        <div class="mx-auto bg-info" style="height: 3px; width: 80px; box-shadow: 0 0 10px var(--neon-cyan);"></div>
    </div>

    <div class="row g-4">
        @forelse($gameTitles as $game)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 bg-dark bg-opacity-50 border-info border-opacity-25 rounded-4 overflow-hidden shadow-hover" style="backdrop-filter: blur(5px); transition: transform 0.3s ease, border-color 0.3s ease;">
                    <div class="position-relative" style="height: 180px;">
                        @if($game->header_image)
                            <img src="{{ asset('storage/' . $game->header_image) }}" class="card-img-top h-100 w-100 object-fit-cover" alt="{{ $game->title }}">
                        @else
                            <div class="h-100 w-100 d-flex align-items-center justify-content-center bg-dark text-secondary">
                                <i class="bi bi-image" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-gradient-to-t from-dark">
                            <h3 class="h5 mb-0 fw-bold text-white shadow-sm">{{ $game->title }}</h3>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="mb-3 text-secondary">
                            @php $totalTemplates = $game->templates_count + $game->premium_templates_count; @endphp
                            <span class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2">
                                <i class="bi bi-grid-3x3-gap"></i> {{ $totalTemplates }} {{ Str::plural('Template', $totalTemplates) }}
                            </span>
                        </div>
                        <p class="card-text text-light opacity-75 mb-4 line-clamp-3">
                            {{ $game->description ?: 'No description available for this game title yet.' }}
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('game_titles.show', $game->id) }}" class="btn btn-outline-info w-100 rounded-pill fw-bold">
                                Read More <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="text-secondary opacity-50 mb-3">
                    <i class="bi bi-info-circle" style="font-size: 4rem;"></i>
                </div>
                <h3 class="text-secondary">No game titles available yet.</h3>
                <p class="text-muted">Check back later or contact an administrator.</p>
            </div>
        @endforelse
    </div>
</div>

<style>
.shadow-hover:hover {
    transform: translateY(-5px);
    border-color: var(--neon-cyan) !important;
    box-shadow: 0 10px 20px rgba(0, 240, 255, 0.15) !important;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.bg-gradient-to-t {
    background: linear-gradient(to top, rgba(10, 10, 26, 0.9), transparent);
}
</style>
@endsection
