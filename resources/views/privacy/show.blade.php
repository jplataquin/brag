@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="text-center mb-4">
            <h2 style="font-family: 'Orbitron', sans-serif; color: #00f0ff; text-shadow: 0 0 15px rgba(0,240,255,0.3);">
                <i class="bi bi-shield-lock-fill"></i> PRIVACY POLICY
            </h2>
            <p style="color: #555577; font-size: 0.9rem;">Please review and agree to our latest privacy policy to continue using Brag.</p>
        </div>

        <div class="neon-card p-5 mb-4" style="background: rgba(10, 10, 26, 0.8); max-height: 60vh; overflow-y: auto; border: 1px solid rgba(0, 240, 255, 0.1);">
            <div class="privacy-content" style="color: #ccc; line-height: 1.8;">
                {!! $latestPrivacy->content !!}
            </div>
        </div>

        <div class="text-center">
            @auth
                @if(auth()->user()->privacy_version_agreed < $latestPrivacy->id)
                    <form action="{{ route('privacy.agree') }}" method="POST">
                        @csrf
                        <div class="mb-4 d-flex justify-content-center align-items-center gap-2">
                            <input type="checkbox" id="agree-check" required style="width: 20px; height: 20px; cursor: pointer;">
                            <label for="agree-check" style="color: #fff; cursor: pointer; font-size: 1.1rem;">I have read and agree to the privacy policy above.</label>
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
    .privacy-content h1, .privacy-content h2, .privacy-content h3 {
        color: #00f0ff;
        font-family: 'Orbitron', sans-serif;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
    }
    .privacy-content p {
        margin-bottom: 1rem;
    }
    .privacy-content ul, .privacy-content ol {
        margin-bottom: 1rem;
        padding-left: 2rem;
    }
    /* Custom Scrollbar for privacy */
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
</style>
@endsection
