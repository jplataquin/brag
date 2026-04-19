@extends('layouts.app')

@section('title', 'My Cards')

@section('content')
<h1 class="page-title">
    <span class="page-title-accent"><i class="bi bi-suit-diamond-fill"></i></span>INVENTORY
</h1>

<!-- Own Cards Section -->
<div class="mb-5">
    <h5 class="section-header">
        <i class="bi bi-suit-diamond-fill section-icon"></i> MY CARDS
        <span class="badge rounded-pill ms-2" style="background: rgba(0,240,255,0.1); color: #00f0ff; font-size: 0.7rem;">{{ $ownCards->count() }}/3</span>
    </h5>

    @if($ownCards->count() > 0)
        <div class="card-grid">
            @foreach($ownCards as $card)
                @include('partials.card-mini', ['card' => $card])
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">🃏</div>
            <div class="empty-text">No cards in your inventory</div>
            <a href="{{ route('templates.index') }}" class="btn btn-neon btn-neon-sm">Forge from Templates</a>
        </div>
    @endif
</div>

<!-- Trophies Section -->
<div>
    <h5 class="section-header">
        <i class="bi bi-trophy-fill section-icon" style="color: #ffdd00;"></i> TROPHY COLLECTION
        <span class="badge rounded-pill ms-2" style="background: rgba(255,221,0,0.1); color: #ffdd00; font-size: 0.7rem;">{{ $trophies->count() }}</span>
    </h5>

    @if($trophies->count() > 0)
        <div class="card-grid">
            @foreach($trophies as $card)
                @include('partials.card-mini', ['card' => $card, 'isTrophy' => true])
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">🏆</div>
            <div class="empty-text">No trophies collected yet</div>
            <p style="color: #555577; font-size: 0.85rem;">Win battles to collect opponent's cards as trophies!</p>
            <a href="{{ route('battles.index') }}" class="btn btn-neon-magenta btn-neon-sm">Find Battles</a>
        </div>
    @endif
</div>
@endsection
