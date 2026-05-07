@extends('layouts.app')

@section('title', 'Trophy Collection')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <h1 class="page-title m-0">
        <span class="page-title-accent"><i class="bi bi-trophy-fill" style="color: #ffdd00;"></i></span> TROPHY COLLECTION
    </h1>

    <div class="d-flex flex-wrap align-items-center justify-content-md-end gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small text-nowrap"><i class="bi bi-funnel"></i> FILTER:</span>
            <select class="form-select form-select-sm" style="background-color: rgba(10, 10, 26, 0.8); color: #fff; border-color: rgba(255, 221, 0, 0.3); width: auto;" onchange="window.location.href=this.value">
                <option value="{{ route('cards.trophies', ['sort' => $sortBy]) }}">All Games</option>
                @foreach($games as $game)
                    <option value="{{ route('cards.trophies', ['sort' => $sortBy, 'game' => $game->id]) }}" {{ $gameId == $game->id ? 'selected' : '' }}>
                        {{ $game->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small text-nowrap"><i class="bi bi-sort-down"></i> SORT BY:</span>
            <div class="btn-group btn-group-sm" role="group" aria-label="Sort trophies">
                @php
                    $nextDirLatest = ($sortBy === 'latest' && $direction === 'asc') ? 'desc' : 'asc';
                    $nextDirLevel = ($sortBy === 'level' && $direction === 'asc') ? 'desc' : 'asc';
                    $nextDirName = ($sortBy === 'name' && $direction === 'asc') ? 'desc' : 'asc';
                    $nextDirSerial = ($sortBy === 'serial' && $direction === 'asc') ? 'desc' : 'asc';
                @endphp

                <a href="{{ route('cards.trophies', ['sort' => 'latest', 'game' => $gameId, 'dir' => $nextDirLatest]) }}" 
                   class="btn {{ $sortBy === 'latest' ? 'btn-neon-yellow text-dark' : 'btn-outline-warning' }}">
                    LATEST 
                    @if($sortBy === 'latest')
                        <i class="bi bi-caret-{{ $direction === 'asc' ? 'down-fill' : 'up-fill' }}"></i>
                    @endif
                </a>
                <a href="{{ route('cards.trophies', ['sort' => 'level', 'game' => $gameId, 'dir' => $nextDirLevel]) }}" 
                   class="btn {{ $sortBy === 'level' ? 'btn-neon-yellow text-dark' : 'btn-outline-warning' }}">
                    LEVEL
                    @if($sortBy === 'level')
                        <i class="bi bi-caret-{{ $direction === 'asc' ? 'up-fill' : 'down-fill' }}"></i>
                    @endif
                </a>
                <a href="{{ route('cards.trophies', ['sort' => 'name', 'game' => $gameId, 'dir' => $nextDirName]) }}" 
                   class="btn {{ $sortBy === 'name' ? 'btn-neon-yellow text-dark' : 'btn-outline-warning' }}">
                    NAME
                    @if($sortBy === 'name')
                        <i class="bi bi-caret-{{ $direction === 'asc' ? 'down-fill' : 'up-fill' }}"></i>
                    @endif
                </a>
                <a href="{{ route('cards.trophies', ['sort' => 'serial', 'game' => $gameId, 'dir' => $nextDirSerial]) }}" 
                   class="btn {{ $sortBy === 'serial' ? 'btn-neon-yellow text-dark' : 'btn-outline-warning' }}">
                    SERIAL
                    @if($sortBy === 'serial')
                        <i class="bi bi-caret-{{ $direction === 'asc' ? 'down-fill' : 'up-fill' }}"></i>
                    @endif
                </a>
            </div>
        </div>
    </div>
</div>

<div class="mb-5">
    @if($cards->count() > 0)
        @php
            $groupedCards = $cards->groupBy(function($card) {
                return $card->template->gameTitle->title ?? 'Unknown Game';
            });
        @endphp

        @foreach($groupedCards as $gameTitle => $gameCards)
            <div class="mb-5">
                <h5 class="section-header" style="color: #ffdd00; border-color: rgba(255, 221, 0, 0.2);">
                    <i class="bi bi-controller section-icon" style="color: #ffdd00;"></i> {{ strtoupper($gameTitle) }}
                    <span class="badge rounded-pill ms-2" style="background: rgba(255,221,0,0.1); color: #ffdd00; font-size: 0.7rem;">{{ $gameCards->count() }}</span>
                </h5>

                <div class="card-grid">
                    @foreach($gameCards as $card)
                        @include('partials.card-mini', ['card' => $card, 'isTrophy' => true])
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <div class="empty-icon" style="color: #ffdd00;">🏆</div>
            <div class="empty-text">No trophies collected yet</div>
            <div class="d-flex gap-3 justify-content-center">
                <a href="{{ route('battles.index') }}" class="btn btn-neon-magenta btn-neon-sm">Enter the Arena</a>
            </div>
        </div>
    @endif
</div>
@endsection
