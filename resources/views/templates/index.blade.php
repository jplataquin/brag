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
        <div class="d-flex flex-column align-items-center mb-4" style="animation: fadeInUp 0.5s ease backwards; animation-delay: {{ $loop->index * 0.05 }}s;">
            <x-digital-card 
                id="template_index_{{ $template->id }}" 
                mode="template"
                asThumbnail="true"
                linkUrl="{{ route('templates.show', $template) }}"
                :title="$template->card_title" 
                :game="$template->gameTitle->title ?? 'GAME'" 
                :creator="$template->user->username ?? 'Creator'"
                :quote="$template->quote"
                :backgroundColor="$template->background_color"
                :borderColor="$template->border_color"
                :sectionColor="$template->section_color"
                :primaryTextColor="$template->primary_text_color"
                :secondaryTextColor="$template->secondary_text_color"
                :image="$template->display_photo"
                :year="$template->created_at->format('Y')"
            />
            
            <div class="mt-3 text-center">
                <h6 class="mb-1" style="font-family: 'Orbitron', sans-serif; font-size: 0.9rem; color: #fff; letter-spacing: 1px;">{{ strtoupper($template->card_title) }}</h6>
                <div style="font-size: 0.75rem; color: #00f0ff; text-transform: uppercase; letter-spacing: 1px;">
                    🎮 {{ $template->gameTitle->title }}
                </div>
                <div class="mt-2 d-flex justify-content-center gap-2 align-items-center">
                    <span class="badge bg-dark border" style="border-color: rgba(0, 240, 255, 0.2) !important; color: #8888aa; font-size: 0.7rem;">
                        <i class="bi bi-layers"></i> {{ $template->digital_cards_count }} CARDS
                    </span>
                    <span style="font-size: 0.65rem; color: #555577;">
                        {{ $template->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@else
    <div class="empty-state">
        <div class="empty-icon">🎨</div>
        <div class="empty-text">No templates yet.</div>
        <p style="color: #555577; font-size: 0.85rem; margin-bottom: 1rem;">Templates are the blueprints from which you forge Digital Cards.</p>
        @if($platformSettings->allow_template_creation)
        <a href="{{ route('templates.create') }}" class="btn btn-neon">
            <i class="bi bi-plus-lg"></i> Create Template
        </a>
        @endif
    </div>
@endif
@endsection
