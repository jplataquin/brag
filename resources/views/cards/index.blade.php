@extends('layouts.app')

@section('title', 'My Collection')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <h1 class="page-title m-0">
        <span class="page-title-accent"><i class="bi bi-suit-diamond-fill"></i></span> MY COLLECTION
    </h1>

    <div class="d-flex flex-wrap align-items-center justify-content-md-end gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small text-nowrap"><i class="bi bi-funnel"></i> FILTER:</span>
            <select class="form-select form-select-sm" style="background-color: rgba(10, 10, 26, 0.8); color: #fff; border-color: rgba(0, 240, 255, 0.3); width: auto;" onchange="window.location.href=this.value">
                <option value="{{ route('cards.index', ['sort' => $sortBy]) }}">All Games</option>
                @foreach($games as $game)
                    <option value="{{ route('cards.index', ['sort' => $sortBy, 'game' => $game->id]) }}" {{ $gameId == $game->id ? 'selected' : '' }}>
                        {{ $game->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small text-nowrap"><i class="bi bi-sort-down"></i> SORT BY:</span>
            <div class="btn-group btn-group-sm" role="group" aria-label="Sort cards">
                <a href="{{ route('cards.index', ['sort' => 'latest', 'game' => $gameId]) }}" 
                   class="btn {{ $sortBy === 'latest' ? 'btn-neon' : 'btn-outline-neon' }}">
                    LATEST
                </a>
                <a href="{{ route('cards.index', ['sort' => 'level', 'game' => $gameId]) }}" 
                   class="btn {{ $sortBy === 'level' ? 'btn-neon' : 'btn-outline-neon' }}">
                    LEVEL
                </a>
            </div>
        </div>
    </div>
</div>

<div class="mb-5">
    <h5 class="section-header">
        <i class="bi bi-collection-fill section-icon"></i> ALL CARDS
        <span class="badge rounded-pill ms-2" style="background: rgba(0,240,255,0.1); color: #00f0ff; font-size: 0.7rem;">{{ $cards->count() }}</span>
    </h5>

    @if($cards->count() > 0)
        <div class="card-grid">
            @foreach($cards as $card)
                @include('partials.card-mini', ['card' => $card])
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">🃏</div>
            <div class="empty-text">Your collection is empty</div>
            <div class="d-flex gap-3 justify-content-center">
                <a href="{{ route('templates.index') }}" class="btn btn-neon btn-neon-sm">Forge from Templates</a>
                <a href="{{ route('battles.index') }}" class="btn btn-neon-magenta btn-neon-sm">Win Trophies</a>
            </div>
        </div>
    @endif
</div>
@endsection
