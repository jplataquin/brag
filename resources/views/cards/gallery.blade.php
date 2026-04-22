@extends('layouts.app')

@section('title', 'Card Gallery')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <h1 class="page-title m-0">
        <span class="page-title-accent"><i class="bi bi-images"></i></span> GALLERY
    </h1>

    <div class="d-flex flex-wrap align-items-center justify-content-md-end gap-3">
        <form action="{{ route('gallery') }}" method="GET" class="d-flex align-items-center gap-2">
            @if($sortBy !== 'latest') <input type="hidden" name="sort" value="{{ $sortBy }}"> @endif
            @if($gameId) <input type="hidden" name="game" value="{{ $gameId }}"> @endif
            @if($direction !== 'desc') <input type="hidden" name="dir" value="{{ $direction }}"> @endif
            <div class="input-group input-group-sm">
                <span class="input-group-text" style="background-color: rgba(10, 10, 26, 0.8); color: rgba(0, 240, 255, 0.6); border-color: rgba(0, 240, 255, 0.3);">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="search" class="form-control" placeholder="Search card title..." value="{{ $search }}" style="background-color: rgba(10, 10, 26, 0.8); color: #fff; border-color: rgba(0, 240, 255, 0.3); min-width: 150px;">
                @if($search)
                    <a href="{{ route('gallery', array_filter(['sort' => $sortBy, 'game' => $gameId, 'dir' => $direction])) }}" class="btn btn-outline-secondary" style="border-color: rgba(0, 240, 255, 0.3); color: rgba(0, 240, 255, 0.6);">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
                <button type="submit" class="btn btn-neon-cyan btn-sm">GO</button>
            </div>
        </form>

        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small text-nowrap"><i class="bi bi-funnel"></i> FILTER:</span>
            <select class="form-select form-select-sm" style="background-color: rgba(10, 10, 26, 0.8); color: #fff; border-color: rgba(0, 240, 255, 0.3); width: auto;" onchange="window.location.href=this.value">
                <option value="{{ route('gallery', array_filter(['sort' => $sortBy, 'search' => $search])) }}">All Games</option>
                @foreach($games as $game)
                    <option value="{{ route('gallery', array_filter(['sort' => $sortBy, 'game' => $game->id, 'search' => $search])) }}" {{ $gameId == $game->id ? 'selected' : '' }}>
                        {{ $game->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small text-nowrap"><i class="bi bi-sort-down"></i> SORT BY:</span>
            <div class="btn-group btn-group-sm" role="group" aria-label="Sort cards">
                <a href="{{ route('gallery', array_filter(['sort' => 'latest', 'game' => $gameId, 'dir' => ($sortBy === 'latest' && $direction === 'asc') ? 'desc' : 'asc', 'search' => $search])) }}" 
                   class="btn {{ $sortBy === 'latest' ? 'btn-neon' : 'btn-outline-neon' }}">
                    LATEST 
                    @if($sortBy === 'latest')
                        <i class="bi bi-caret-{{ $direction === 'asc' ? 'down-fill' : 'up-fill' }}"></i>
                    @endif
                </a>
                <a href="{{ route('gallery', array_filter(['sort' => 'level', 'game' => $gameId, 'dir' => ($sortBy === 'level' && $direction === 'asc') ? 'desc' : 'asc', 'search' => $search])) }}" 
                   class="btn {{ $sortBy === 'level' ? 'btn-neon' : 'btn-outline-neon' }}">
                    LEVEL
                    @if($sortBy === 'level')
                        <i class="bi bi-caret-{{ $direction === 'asc' ? 'up-fill' : 'down-fill' }}"></i>
                    @endif
                </a>
                <a href="{{ route('gallery', array_filter(['sort' => 'name', 'game' => $gameId, 'dir' => ($sortBy === 'name' && $direction === 'asc') ? 'desc' : 'asc', 'search' => $search])) }}" 
                   class="btn {{ $sortBy === 'name' ? 'btn-neon' : 'btn-outline-neon' }}">
                    NAME
                    @if($sortBy === 'name')
                        <i class="bi bi-caret-{{ $direction === 'asc' ? 'down-fill' : 'up-fill' }}"></i>
                    @endif
                </a>
                <a href="{{ route('gallery', array_filter(['sort' => 'serial', 'game' => $gameId, 'dir' => ($sortBy === 'serial' && $direction === 'asc') ? 'desc' : 'asc', 'search' => $search])) }}" 
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