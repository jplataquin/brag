@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="position-relative rounded-4 overflow-hidden mb-5 border border-info border-opacity-25" style="height: 350px;">
        @if($gameTitle->header_image)
            <img src="{{ asset('storage/' . $gameTitle->header_image) }}" class="w-100 h-100 object-fit-cover" alt="{{ $gameTitle->title }}">
        @else
            <div class="w-100 h-100 bg-dark d-flex align-items-center justify-content-center text-secondary">
                <i class="bi bi-controller" style="font-size: 6rem; opacity: 0.2;"></i>
            </div>
        @endif
        <div class="position-absolute bottom-0 start-0 w-100 p-4 p-md-5 bg-gradient-to-t from-dark d-flex align-items-end">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('game_titles.index') }}" class="text-info text-decoration-none">Games</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">{{ $gameTitle->title }}</li>
                    </ol>
                </nav>
                <h1 class="display-3 fw-bold text-uppercase mb-0 text-white" style="text-shadow: 0 0 20px rgba(0, 240, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                    {{ $gameTitle->title }}
                </h1>
            </div>
        </div>
    </div>

    <div class="row g-5">
        <!-- Sidebar / Stats -->
        <div class="col-lg-4 order-lg-2">
            <div class="card bg-dark bg-opacity-75 border-info border-opacity-25 rounded-4 p-4 sticky-top" style="top: 20px; backdrop-filter: blur(10px);">
                <h4 class="text-info fw-bold text-uppercase mb-4" style="font-family: 'Orbitron', sans-serif;">Game Info</h4>
                
                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Templates Available</label>
                    <div class="h4 text-white fw-bold mb-0">
                        @php $totalCount = $gameTitle->templates->count() + $gameTitle->premiumTemplates->count(); @endphp
                        <i class="bi bi-grid-3x3-gap text-info me-2"></i> {{ $totalCount }}
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Status</label>
                    <div class="text-white">
                        <span class="badge bg-success rounded-pill px-3">
                            <i class="bi bi-check-circle-fill me-1"></i> Active & Ready
                        </span>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <a href="{{ route('templates.create', ['game_title_id' => $gameTitle->id]) }}" class="btn btn-neon-cyan fw-bold">
                        <i class="bi bi-plus-lg me-1"></i> Forge New Template
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8 order-lg-1">
            <div class="mb-5">
                <h2 class="h4 text-uppercase fw-bold mb-4" style="color: var(--neon-cyan); font-family: 'Orbitron', sans-serif; letter-spacing: 1px;">About the Game</h2>
                <div class="text-light opacity-75 lead" style="white-space: pre-line; line-height: 1.8;">
                    {{ $gameTitle->description ?: 'No detailed description provided for this game title yet.' }}
                </div>
            </div>

            <div>
                <h2 class="h4 text-uppercase fw-bold mb-4" style="color: var(--neon-magenta); font-family: 'Orbitron', sans-serif; letter-spacing: 1px;">Available Templates</h2>
                
                @php
                    $allTemplates = collect();
                    foreach($gameTitle->templates as $t) {
                        $t->is_premium_type = false;
                        $allTemplates->push($t);
                    }
                    foreach($gameTitle->premiumTemplates as $pt) {
                        $pt->is_premium_type = true;
                        $allTemplates->push($pt);
                    }
                    $allTemplates = $allTemplates->sortByDesc('created_at');
                @endphp

                @if($allTemplates->count() > 0)
                    <div class="row g-4">
                        @foreach($allTemplates as $template)
                            <div class="col-md-6">
                                <div class="card bg-dark bg-opacity-50 border-info border-opacity-25 rounded-4 overflow-hidden h-100 shadow-hover">
                                    <div class="position-relative" style="height: 120px;">
                                        @if($template->is_premium_type)
                                            <img src="{{ $template->display_photo }}" class="w-100 h-100 object-fit-cover" alt="{{ $template->template_title }}">
                                        @else
                                            <img src="{{ $template->display_photo }}" class="w-100 h-100 object-fit-cover" alt="{{ $template->card_title }}">
                                        @endif
                                        
                                        <div class="position-absolute top-0 end-0 p-2">
                                            @if($template->is_premium_type)
                                                <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i> Premium</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="fw-bold text-white mb-2">{{ $template->is_premium_type ? $template->template_title : $template->card_title }}</h5>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            @if($template->is_premium_type)
                                                {{-- Check if premium templates have a public detail page, if not, just show price --}}
                                                <button class="btn btn-sm btn-outline-warning rounded-pill px-3" disabled>Premium</button>
                                                <span class="text-warning small"><i class="bi bi-gem"></i> {{ number_format($template->price) }}</span>
                                            @else
                                                <a href="{{ route('templates.show', $template->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3">View Template</a>
                                                <span class="text-muted small"><i class="bi bi-lightning-fill text-warning"></i> Ready to Forge</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert bg-dark border-info border-opacity-25 text-secondary rounded-4">
                        <i class="bi bi-info-circle me-2"></i> No active templates found for this game title yet. Why not create one?
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-to-t {
    background: linear-gradient(to top, rgba(10, 10, 26, 0.95), transparent);
}

.shadow-hover:hover {
    border-color: var(--neon-magenta) !important;
    transform: translateY(-3px);
    transition: all 0.3s ease;
}

.breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255, 255, 255, 0.3);
}
</style>
@endsection
