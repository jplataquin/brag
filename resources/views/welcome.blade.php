@extends('layouts.app')

@section('title', 'Forge. Battle. Brag.')

@section('content')
<div class="hero-section">
    <div class="hero-content">
        <h1 class="hero-title">BRAG</h1>
        <p class="hero-subtitle">FORGE DIGITAL CARDS • BATTLE OPPONENTS • COLLECT TROPHIES</p>
        <p style="color: #8888aa; max-width: 600px; margin: 0 auto 2rem; font-size: 1rem;">
            Create unique templates, forge Digital Cards, challenge other players in epic battles,
            and collect their cards as trophies. How many can you collect?
        </p>
        <div class="hero-cta">
            @guest
                <a href="{{ route('register') }}" class="btn btn-neon btn-lg" id="hero-signup">
                    <i class="bi bi-lightning-charge-fill"></i> JOIN THE ARENA
                </a>
                <a href="{{ route('login') }}" class="btn btn-neon-magenta btn-lg" id="hero-login">
                    <i class="bi bi-box-arrow-in-right"></i> LOGIN
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="btn btn-neon btn-lg" id="hero-dashboard">
                    <i class="bi bi-grid-fill"></i> DASHBOARD
                </a>
            @endguest
        </div>

        <!-- Welcome Cards -->
        <div class="row mt-5 g-4 justify-content-center">
            <div class="col-md-4 col-sm-12">
                <a href="{{ url('/cards/gallery') }}" class="neon-card p-4 text-center h-100 d-flex flex-column justify-content-center text-decoration-none" style="transition: transform 0.2s, box-shadow 0.2s; border-color: #00f0ff;">
                    <div style="font-family: 'Orbitron', sans-serif; font-size: 3.5rem; font-weight: 700; color: #00f0ff; text-shadow: 0 0 20px rgba(0,240,255,0.5); line-height: 1;">
                        {{ number_format($cardsInCirculation) }}
                    </div>
                    <h5 class="mt-3" style="font-family: 'Orbitron', sans-serif; font-size: 1rem; color: #8888aa; letter-spacing: 2px;">CARDS IN CIRCULATION</h5>
                </a>
            </div>
            <div class="col-md-4 col-sm-12">
                <a href="{{ route('game_titles.index') }}" class="neon-card p-4 text-center h-100 d-flex flex-column justify-content-center text-decoration-none" style="transition: transform 0.2s, box-shadow 0.2s; border-color: #39ff14;">
                    <div style="font-family: 'Orbitron', sans-serif; font-size: 3.5rem; font-weight: 700; color: #39ff14; text-shadow: 0 0 20px rgba(57,255,20,0.5); line-height: 1;">
                        {{ number_format($gameTitlesCount) }}
                    </div>
                    <h5 class="mt-3" style="font-family: 'Orbitron', sans-serif; font-size: 1rem; color: #8888aa; letter-spacing: 2px;">ACTIVE GAME TITLES</h5>
                </a>
            </div>
            <div class="col-md-4 col-sm-12">
                <a href="{{ route('pwa.instructions') }}" id="btn-install-pwa-welcome" class="neon-card p-4 text-center h-100 d-flex flex-column justify-content-center text-decoration-none" style="transition: transform 0.2s, box-shadow 0.2s; border-color: #ff00ff; background: rgba(255,0,255,0.05);">
                    <div style="font-size: 3.5rem; margin-bottom: 0.5rem; color: #ff00ff; text-shadow: 0 0 20px rgba(255,0,255,0.5);"><i class="bi bi-download"></i></div>
                    <h5 style="font-family: 'Orbitron', sans-serif; font-size: 1.2rem; color: #ff00ff; letter-spacing: 2px; margin-bottom: 0;">INSTALL APP</h5>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
