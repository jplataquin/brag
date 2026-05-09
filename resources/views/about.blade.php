@extends('layouts.app')

@section('title', 'About Brag')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-3 orbitron fw-bold neon-text">ABOUT BRAG</h1>
        <p class="lead text-white-50 mt-3" style="font-family: 'Orbitron', sans-serif; letter-spacing: 2px;">FORGE. BATTLE. BRAG.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="neon-card p-5" style="background: rgba(10, 10, 30, 0.8); border-color: var(--neon-cyan);">
                <div class="about-content text-light" style="font-size: 1.15rem; line-height: 1.8;">
                    <p class="mb-4">
                        Brag is an online platform that tracks your epic battles and rivalries across different games, sports, and versus encounters.
                    </p>

                    <p class="mb-4">
                        Players and teams represent themselves as uniquely personalized cards.
                    </p>

                    <p class="mb-4">
                        These cards serve as the <strong>"what's at stake?"</strong> in your competitive showdown. They track your wins, losses, and other key indicators. Every victory you gain could level up your card, ultimately showcasing your accomplishments over time.
                    </p>

                    <div class="my-5 p-4 text-center" style="background: rgba(255, 0, 255, 0.05); border-left: 4px solid var(--neon-magenta); border-radius: 4px;">
                        <p class="mb-0 text-white" style="font-size: 1.3rem;">
                            Losing three times however means surrendering your card to the opponent, which becomes their forever trophies and rights to brag.
                        </p>
                    </div>

                    <p class="mb-4">
                        Remember, losing is just a set back and winning is a sweet treat. But the spirit of competition is what really ignites the heart.
                    </p>

                    <p class="mb-5">
                        Make friends along the way, and never give up.
                    </p>
                    
                    <div class="text-center mt-5">
                        <h2 class="orbitron neon-text-lime mb-0" style="font-size: 1.8rem; line-height: 1.4;">
                            Forge your maker's mark, stamp your card, show the world you have something to brag about.
                        </h2>
                    </div>
                </div>

                <div class="text-center mt-5 pt-4">
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-neon btn-lg px-5">JOIN THE ARENA</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-neon btn-lg px-5">GO TO DASHBOARD</a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --neon-cyan: #00f0ff;
        --neon-magenta: #ff00ff;
        --neon-lime: #39ff14;
        --neon-yellow: #ffdd00;
    }
</style>
@endsection
