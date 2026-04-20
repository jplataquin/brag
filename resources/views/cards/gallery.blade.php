@extends('layouts.app')

@section('title', 'Card Gallery')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <h1 class="page-title m-0">
        <span class="page-title-accent"><i class="bi bi-images"></i></span> GALLERY
    </h1>

    <div class="d-flex flex-wrap align-items-center justify-content-md-end gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small text-nowrap"><i class="bi bi-funnel"></i> FILTER:</span>
            <select class="form-select form-select-sm" style="background-color: rgba(10, 10, 26, 0.8); color: #fff; border-color: rgba(0, 240, 255, 0.3); width: auto;" onchange="window.location.href=this.value">
                <option value="{{ route('gallery', ['sort' => $sortBy]) }}">All Games</option>
                @foreach($games as $game)
                    <option value="{{ route('gallery', ['sort' => $sortBy, 'game' => $game->id]) }}" {{ $gameId == $game->id ? 'selected' : '' }}>
                        {{ $game->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small text-nowrap"><i class="bi bi-sort-down"></i> SORT BY:</span>
            <div class="btn-group btn-group-sm" role="group" aria-label="Sort cards">
                @php
                    $nextDirLatest = ($sortBy === 'latest' && $direction === 'asc') ? 'desc' : 'asc';
                    $nextDirLevel = ($sortBy === 'level' && $direction === 'asc') ? 'desc' : 'asc';
                    $nextDirName = ($sortBy === 'name' && $direction === 'asc') ? 'desc' : 'asc';
                    $nextDirSerial = ($sortBy === 'serial' && $direction === 'asc') ? 'desc' : 'asc';
                @endphp

                <a href="{{ route('gallery', ['sort' => 'latest', 'game' => $gameId, 'dir' => $nextDirLatest]) }}" 
                   class="btn {{ $sortBy === 'latest' ? 'btn-neon' : 'btn-outline-neon' }}">
                    LATEST 
                    @if($sortBy === 'latest')
                        <i class="bi bi-caret-{{ $direction === 'asc' ? 'down-fill' : 'up-fill' }}"></i>
                    @endif
                </a>
                <a href="{{ route('gallery', ['sort' => 'level', 'game' => $gameId, 'dir' => $nextDirLevel]) }}" 
                   class="btn {{ $sortBy === 'level' ? 'btn-neon' : 'btn-outline-neon' }}">
                    LEVEL
                    @if($sortBy === 'level')
                        <i class="bi bi-caret-{{ $direction === 'asc' ? 'up-fill' : 'down-fill' }}"></i>
                    @endif
                </a>
                <a href="{{ route('gallery', ['sort' => 'name', 'game' => $gameId, 'dir' => $nextDirName]) }}" 
                   class="btn {{ $sortBy === 'name' ? 'btn-neon' : 'btn-outline-neon' }}">
                    NAME
                    @if($sortBy === 'name')
                        <i class="bi bi-caret-{{ $direction === 'asc' ? 'down-fill' : 'up-fill' }}"></i>
                    @endif
                </a>
                <a href="{{ route('gallery', ['sort' => 'serial', 'game' => $gameId, 'dir' => $nextDirSerial]) }}" 
                   class="btn {{ $sortBy === 'serial' ? 'btn-neon' : 'btn-outline-neon' }}">
                    SERIAL
                    @if($sortBy === 'serial')
                        <i class="bi bi-caret-{{ $direction === 'asc' ? 'up-fill' : 'down-fill' }}"></i>
                    @endif
                </a>
            </div>
        </div>
    </div>
</div>

<div class="mb-5">
    <h5 class="section-header">
        <i class="bi bi-collection-fill section-icon"></i> CARDS IN CIRCULATION
    </h5>

    @if($cards->count() > 0)
        <div class="card-grid">
            @foreach($cards as $card)
                @include('partials.card-mini', ['card' => $card])
            @endforeach
        </div>
        
        <div class="mt-4 d-flex justify-content-center">
            {{ $cards->links() }}
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">🖼️</div>
            <div class="empty-text">No cards found in circulation</div>
        </div>
    @endif
</div>
@endsection