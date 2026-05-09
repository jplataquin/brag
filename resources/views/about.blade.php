@extends('layouts.app')

@section('title', 'About Brag')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-3 orbitron fw-bold neon-text">ABOUT BRAG</h1>
        <p class="lead text-white-50 mt-3" style="font-family: 'Orbitron', sans-serif; letter-spacing: 2px;">FORGE. BATTLE. BRAG.</p>
    </div>

    <div class="row g-5 align-items-center mb-5">
        <div class="col-lg-6">
            <h2 class="orbitron neon-text-magenta mb-4"><i class="bi bi-lightning-charge-fill me-2"></i> THE CONCEPT</h2>
            <p class="text-light lead">
                Brag is a sleek, competitive social platform where gamers don't just talk about their skills—they stake their digital legacy on them.
            </p>
            <p class="text-secondary">
                Born from the competitive spirit of PvP gaming, Brag allows players to create unique "Digital Cards" from customizable templates and use them as physical stakes in head-to-head battles. It's not just a game; it's a social arena where your inventory represents your dominance and every win is a trophy for your collection.
            </p>
        </div>
        <div class="col-lg-6">
            <div class="neon-card p-5 text-center" style="background: rgba(0, 240, 255, 0.05); transform: rotate(2deg);">
                <i class="bi bi-gem display-1 neon-text mb-3"></i>
                <h3 class="orbitron text-white">YOUR CARDS</h3>
                <p class="text-secondary mb-0">Unique trophies tracking your legacy: Level, Wins, Losses, and Copies in Circulation.</p>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="neon-card p-4 h-100 text-center" style="border-color: var(--neon-lime);">
                <div class="fs-1 neon-text-lime mb-3"><i class="bi bi-hammer"></i></div>
                <h4 class="orbitron text-white">1. FORGE</h4>
                <p class="text-secondary">Create unique templates for your favorite games and forge them into Digital Cards. Each card is a unique item in your inventory, tracking its own history and rarity.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="neon-card p-4 h-100 text-center" style="border-color: var(--neon-yellow);">
                <div class="fs-1 neon-text-yellow mb-3"><i class="bi bi-swords"></i></div>
                <h4 class="orbitron text-white">2. BATTLE</h4>
                <p class="text-secondary">Enter the Arena. Challenge opponents in real-time rooms and bet your Digital Cards. Win the match, and you take home your opponent's staked card as a trophy.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="neon-card p-4 h-100 text-center" style="border-color: var(--neon-magenta);">
                <div class="fs-1 neon-text-magenta mb-3"><i class="bi bi-megaphone"></i></div>
                <h4 class="orbitron text-white">3. BRAG</h4>
                <p class="text-secondary">Showcase your collected trophies on your public profile. Build your reputation as the ultimate card-battler and let your inventory do the talking.</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="neon-card p-5" style="border-color: var(--neon-cyan); background: rgba(0, 0, 0, 0.5);">
                <h2 class="orbitron text-center neon-text mb-5">THE ARENA EXPERIENCE</h2>
                <div class="row text-start g-4">
                    <div class="col-md-6">
                        <h5 class="text-white mb-2"><i class="bi bi-shield-check me-2 neon-text-yellow"></i> FAIR ADJUDICATION</h5>
                        <p class="small text-secondary">Invite a neutral Marshall—a trusted third-party user—to oversee your battles and declare winners fairly in case of conflicts.</p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-white mb-2"><i class="bi bi-robot me-2 neon-text-magenta"></i> AI ENHANCED</h5>
                        <p class="small text-secondary">Utilize integrated AI generation to enhance your template display photos, creating truly unique visual assets for your cards.</p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-white mb-2"><i class="bi bi-clock-history me-2 neon-text-lime"></i> REAL-TIME ROOMS</h5>
                        <p class="small text-secondary">Experience live match updates powered by Laravel Reverb WebSockets for seamless joining, starting, and resolving matches.</p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-white mb-2"><i class="bi bi-phone me-2 neon-text"></i> MOBILE FIRST</h5>
                        <p class="small text-secondary">Manage your collection and join battles anywhere. Brag is optimized for mobile with intuitive carousels and QR code sharing.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <h3 class="orbitron text-white mb-4">READY TO BEGIN?</h3>
        @guest
            <a href="{{ route('register') }}" class="btn btn-neon btn-lg px-5">JOIN THE ARENA</a>
        @else
            <a href="{{ route('dashboard') }}" class="btn btn-neon btn-lg px-5">GO TO DASHBOARD</a>
        @endguest
    </div>
</div>

<style>
    :root {
        --neon-cyan: #00f0ff;
        --neon-magenta: #ff00ff;
        --neon-lime: #39ff14;
        --neon-yellow: #ffdd00;
    }
    .neon-text-yellow { color: var(--neon-yellow); text-shadow: 0 0 10px rgba(255, 221, 0, 0.5); }
</style>
@endsection

