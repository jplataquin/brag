@extends('layouts.app')

@section('title', 'Install App')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="mb-4">
                <i class="bi bi-phone-vibrate neon-text" style="font-size: 4rem;"></i>
            </div>
            <h1 class="neon-text mb-4" style="font-family: 'Orbitron', sans-serif; letter-spacing: 2px;">INSTALL BRAG</h1>
            <p class="lead text-light mb-5">Take the arena with you. Install BRAG on your home screen for a full-screen, app-like experience.</p>

            <div class="card bg-dark border-neon-cyan mb-5" style="background: rgba(10, 10, 30, 0.95); backdrop-filter: blur(20px);">
                <div class="card-body p-4">
                    <h3 class="text-white mb-4" style="font-family: 'Orbitron', sans-serif;">HOW TO INSTALL</h3>
                    
                    <div class="accordion accordion-flush bg-transparent" id="installInstructions">
                        <!-- iOS / Safari -->
                        <div class="accordion-item bg-transparent border-bottom border-secondary" style="border-bottom-color: rgba(0, 240, 255, 0.1) !important;">
                            <h2 class="accordion-header" id="headingiOS">
                                <button class="accordion-button collapsed bg-transparent text-white shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseiOS" aria-expanded="false" aria-controls="collapseiOS">
                                    <i class="bi bi-apple me-3 neon-text"></i> iOS / Safari
                                </button>
                            </h2>
                            <div id="collapseiOS" class="accordion-collapse collapse" aria-labelledby="headingiOS" data-bs-parent="#installInstructions">
                                <div class="accordion-body text-start text-secondary">
                                    <ol class="mb-0">
                                        <li>Open <span class="text-white">Safari</span> and navigate to <span class="text-white">{{ config('app.url') }}</span></li>
                                        <li>Tap the <span class="text-white">Share</span> button (the square with an arrow pointing up) at the bottom of the screen.</li>
                                        <li>Scroll down and tap <span class="text-white">"Add to Home Screen"</span>.</li>
                                        <li>Tap <span class="text-white">"Add"</span> in the top-right corner.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Android / Chrome -->
                        <div class="accordion-item bg-transparent border-bottom border-secondary" style="border-bottom-color: rgba(0, 240, 255, 0.1) !important;">
                            <h2 class="accordion-header" id="headingAndroid">
                                <button class="accordion-button collapsed bg-transparent text-white shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAndroid" aria-expanded="false" aria-controls="collapseAndroid">
                                    <i class="bi bi-google-play me-3 neon-text"></i> Android / Chrome
                                </button>
                            </h2>
                            <div id="collapseAndroid" class="accordion-collapse collapse" aria-labelledby="headingAndroid" data-bs-parent="#installInstructions">
                                <div class="accordion-body text-start text-secondary">
                                    <ol class="mb-0">
                                        <li>Open <span class="text-white">Chrome</span> and navigate to <span class="text-white">{{ config('app.url') }}</span></li>
                                        <li>Tap the <span class="text-white">three dots</span> menu icon in the top-right corner.</li>
                                        <li>Tap <span class="text-white">"Install app"</span> or <span class="text-white">"Add to Home screen"</span>.</li>
                                        <li>Follow the on-screen prompts to confirm installation.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Desktop / Chrome/Edge -->
                        <div class="accordion-item bg-transparent">
                            <h2 class="accordion-header" id="headingDesktop">
                                <button class="accordion-button collapsed bg-transparent text-white shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDesktop" aria-expanded="false" aria-controls="collapseDesktop">
                                    <i class="bi bi-laptop me-3 neon-text"></i> Desktop (Chrome / Edge)
                                </button>
                            </h2>
                            <div id="collapseDesktop" class="accordion-collapse collapse" aria-labelledby="headingDesktop" data-bs-parent="#installInstructions">
                                <div class="accordion-body text-start text-secondary">
                                    <ol class="mb-0">
                                        <li>Look for the <span class="text-white">Install icon</span> (usually a computer screen with an arrow) in the right side of the address bar.</li>
                                        <li>Click the icon and select <span class="text-white">"Install"</span>.</li>
                                        <li>Alternatively, open the <span class="text-white">three dots menu</span> and select <span class="text-white">"Save and Share" &gt; "Install BRAG"</span>.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <a href="{{ route('dashboard') }}" class="btn btn-neon">
                    <i class="bi bi-arrow-left me-2"></i> RETURN TO ARENA
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .border-neon-cyan {
        border-color: rgba(0, 240, 255, 0.3) !important;
        box-shadow: 0 0 20px rgba(0, 240, 255, 0.1);
    }
    .accordion-button::after {
        filter: invert(1) hue-rotate(180deg) brightness(2);
    }
    .accordion-button:not(.collapsed) {
        color: var(--neon-cyan);
        box-shadow: none;
    }
</style>
@endsection
