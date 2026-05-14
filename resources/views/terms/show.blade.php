@extends('layouts.app')

@section('title', 'Terms of Service')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="text-center mb-4">
            <h2 style="font-family: 'Orbitron', sans-serif; color: #00f0ff; text-shadow: 0 0 15px rgba(0,240,255,0.3);">
                <i class="bi bi-file-earmark-text-fill"></i> TERMS OF SERVICE
            </h2>
            <p style="color: #555577; font-size: 0.9rem;">Please review and agree to our latest terms to continue using Brag.</p>
            
            <button onclick="window.print()" class="btn btn-outline-info btn-sm no-print" style="border-radius: 20px; font-family: 'Orbitron', sans-serif; font-size: 0.7rem; letter-spacing: 1px;">
                <i class="bi bi-printer"></i> PRINT TERMS
            </button>
        </div>

        <div class="neon-card p-5 mb-4 terms-print-container" style="background: rgba(10, 10, 26, 0.8); max-height: 60vh; overflow-y: auto; border: 1px solid rgba(0, 240, 255, 0.1);">
            <div class="terms-content" style="color: #ccc; line-height: 1.8;">
                {!! $latestTerms->content !!}
            </div>
        </div>

        <div class="text-center">
            @auth
                @if(auth()->user()->terms_version_agreed < $latestTerms->id)
                    <form action="{{ route('terms.agree') }}" method="POST">
                        @csrf
                        <div class="mb-4 d-flex justify-content-center align-items-center gap-2">
                            <input type="checkbox" id="agree-check" required style="width: 20px; height: 20px; cursor: pointer;">
                            <label for="agree-check" style="color: #fff; cursor: pointer; font-size: 1.1rem;">I have read and agree to the terms above.</label>
                        </div>

                        <button type="submit" class="btn btn-neon py-3 px-5" style="font-family: 'Orbitron', sans-serif; letter-spacing: 2px;">
                            AGREE & CONTINUE
                        </button>
                    </form>
                @endif
            @endauth
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .terms-content h1, .terms-content h2, .terms-content h3 {
        color: #00f0ff;
        font-family: 'Orbitron', sans-serif;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
    }
    .terms-content p {
        margin-bottom: 1rem;
    }
    .terms-content ul, .terms-content ol {
        margin-bottom: 1rem;
        padding-left: 2rem;
    }
    /* Custom Scrollbar for terms */
    .neon-card::-webkit-scrollbar {
        width: 8px;
    }
    .neon-card::-webkit-scrollbar-track {
        background: #0a0a1a;
    }
    .neon-card::-webkit-scrollbar-thumb {
        background: #00f0ff;
        border-radius: 4px;
    }
    .neon-card::-webkit-scrollbar-thumb:hover {
        background: #00d0ee;
    }

    /* Print Styles */
    @media print {
        .no-print, .navbar, footer, .btn-neon, #agree-check, label[for="agree-check"] {
            display: none !important;
        }
        body {
            background: white !important;
            color: black !important;
        }
        .neon-card {
            background: white !important;
            color: black !important;
            border: none !important;
            max-height: none !important;
            overflow: visible !important;
            padding: 0 !important;
        }
        .terms-content {
            color: black !important;
        }
        .terms-content h1, .terms-content h2, .terms-content h3 {
            color: black !important;
            text-shadow: none !important;
        }
        .row, .col-lg-10 {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
    }
</style>
@endsection
