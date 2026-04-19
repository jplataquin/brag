@extends('layouts.app')

@section('title', 'My Templates')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <h1 class="page-title mb-0">
        <span class="page-title-accent"><i class="bi bi-layers-fill"></i></span> MY TEMPLATES
    </h1>
    <a href="{{ route('templates.create') }}" class="btn btn-neon" id="btn-create-template">
        <i class="bi bi-plus-lg"></i> NEW TEMPLATE
    </a>
</div>

@if($templates->count() > 0)
    <div class="card-grid">
        @foreach($templates as $template)
        <div class="neon-card" style="animation-delay: {{ $loop->index * 0.1 }}s;">
            <a href="{{ route('templates.show', $template) }}" style="text-decoration: none; color: inherit; display: block;">
                @if($template->ai_photo || $template->photo)
                    <img src="{{ $template->display_photo }}" alt="{{ $template->card_title }}" style="width: 100%; height: 180px; object-fit: cover; border-bottom: 1px solid rgba(0,240,255,0.1);">
                @else
                    <div style="width: 100%; height: 180px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, rgba(0,240,255,0.03), rgba(255,0,255,0.03)); border-bottom: 1px solid rgba(0,240,255,0.1);">
                        <i class="bi bi-image" style="font-size: 2.5rem; color: #333355;"></i>
                    </div>
                @endif

                <div class="p-3">
                    <h6 style="font-family: 'Orbitron', sans-serif; font-size: 0.85rem; margin-bottom: 0.25rem;">{{ $template->card_title }}</h6>
                    <div style="font-size: 0.75rem; color: #00f0ff; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">
                        🎮 {{ $template->gameTitle->title }}
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span style="font-size: 0.75rem; color: #8888aa;">
                            <i class="bi bi-suit-diamond"></i> {{ $template->digital_cards_count }} cards forged
                        </span>
                        <span style="font-size: 0.7rem; color: #555577;">
                            {{ $template->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
@else
    <div class="empty-state">
        <div class="empty-icon">🎨</div>
        <div class="empty-text">No templates yet. Create your first!</div>
        <p style="color: #555577; font-size: 0.85rem; margin-bottom: 1rem;">Templates are the blueprints from which you forge Digital Cards.</p>
        <a href="{{ route('templates.create') }}" class="btn btn-neon">
            <i class="bi bi-plus-lg"></i> Create Template
        </a>
    </div>
@endif
@endsection
