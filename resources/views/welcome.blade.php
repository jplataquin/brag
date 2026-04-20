@extends('layouts.app')

@section('title', 'Forge. Battle. Brag.')

@section('content')
<div class="hero-section">
    <div class="hero-content">
        <img src="{{ asset('img/logo.svg') }}" alt="Brag - Forge. Battle. Brag." class="img-fluid mb-3" style="max-width: 500px; width: 100%;">
        <p class="hero-subtitle mt-2">FORGE DIGITAL CARDS • BATTLE OPPONENTS • COLLECT TROPHIES</p>
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

        <!-- Feature Cards -->
        <div class="row mt-5 g-4 justify-content-center">
            <div class="col-md-4 col-sm-6">
                <div class="neon-card p-4 text-center h-100">
                    <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">🎨</div>
                    <h5 style="font-family: 'Orbitron', sans-serif; font-size: 0.9rem; color: #00f0ff;">CREATE</h5>
                    <p style="font-size: 0.85rem; color: #8888aa;">Design unique card templates for your favorite games. Each one is your personal masterpiece.</p>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="neon-card p-4 text-center h-100">
                    <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">⚔️</div>
                    <h5 style="font-family: 'Orbitron', sans-serif; font-size: 0.9rem; color: #ff00ff;">BATTLE</h5>
                    <p style="font-size: 0.85rem; color: #8888aa;">Challenge players to battles. Bet your cards and prove your dominance in the arena.</p>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="neon-card p-4 text-center h-100">
                    <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">🏆</div>
                    <h5 style="font-family: 'Orbitron', sans-serif; font-size: 0.9rem; color: #39ff14;">COLLECT</h5>
                    <p style="font-size: 0.85rem; color: #8888aa;">Win battles and collect opponent cards as trophies. Build the ultimate collection.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
