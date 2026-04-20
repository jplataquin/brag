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
                @<span>{{ $user->username }}</span>
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
    <div class="col-4 col-md">
        <div class="stat-box">
            <div class="stat-value">{{ $stats['total_cards'] }}</div>
            <div class="stat-label">Cards</div>
        </div>
    </div>
    <div class="col-4 col-md">
        <div class="stat-box">
            <div class="stat-value" style="color: #ffdd00;">{{ $stats['total_trophies'] }}</div>
            <div class="stat-label">Trophies</div>
        </div>
    </div>
    <div class="col-4 col-md">
        <div class="stat-box">
            <div class="stat-value" style="color: #39ff14;">{{ $stats['total_wins'] }}</div>
            <div class="stat-label">Wins</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-box">
            <div class="stat-value" style="color: #ff00ff;">{{ $stats['failed_battles'] }}</div>
            <div class="stat-label">Failed</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-box">
            <div class="stat-value" style="color: #ff6600;">{{ $stats['total_battles'] }}</div>
            <div class="stat-label">Completed</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Digital Cards -->
    <div class="col-lg-6">
        <h5 class="section-header">
            <i class="bi bi-suit-diamond-fill section-icon"></i> DIGITAL CARDS
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
            <div class="card-grid">
                @foreach($trophies as $card)
                    @include('partials.card-mini', ['card' => $card, 'isTrophy' => true])
                @endforeach
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
