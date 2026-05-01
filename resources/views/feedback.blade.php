@extends('layouts.app')

@section('title', 'Provide Feedback')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="page-title text-center mb-4">
                <span class="page-title-accent"><i class="bi bi-chat-right-text-fill"></i></span> FEEDBACK
            </h1>
            
            <p class="text-center text-muted mb-5 orbitron" style="font-size: 0.9rem; letter-spacing: 1px;">
                HAVE A SUGGESTION? FOUND A BUG? LET US KNOW HOW TO IMPROVE THE ARENA.
            </p>

            <div class="neon-card p-4 p-md-5">
                <form action="{{ route('feedback.send') }}" method="POST" id="feedback-form">
                    @csrf

                    <div class="mb-4">
                        <label for="subject" class="form-label orbitron text-cyan">SUBJECT</label>
                        <input type="text" name="subject" id="subject" class="form-control bg-dark text-white border-secondary @error('subject') is-invalid @enderror" 
                               placeholder="e.g. Card Balancing, Feature Request, Bug Report" value="{{ old('subject') }}" required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="message" class="form-label orbitron text-cyan">YOUR MESSAGE</label>
                        <textarea name="message" id="message" rows="8" class="form-control bg-dark text-white border-secondary @error('message') is-invalid @enderror" 
                                  placeholder="Type your feedback here..." required>{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 d-flex justify-content-center">
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.key') }}" data-theme="dark"></div>
                    </div>
                    @error('cf-turnstile-response')
                        <div class="text-danger text-center small mb-4">{{ $message }}</div>
                    @enderror

                    <button type="submit" class="btn btn-neon w-100 py-3 orbitron mt-2" id="btn-send-feedback">
                        <i class="bi bi-send-fill me-2"></i> SEND FEEDBACK
                    </button>
                </form>
            </div>
            
            <div class="text-center mt-5">
                <a href="{{ route('dashboard') }}" class="text-muted text-decoration-none small hover-cyan transition-all">
                    <i class="bi bi-arrow-left"></i> RETURN TO ARENA
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<script>
    document.getElementById('feedback-form').addEventListener('submit', function() {
        const btn = document.getElementById('btn-send-feedback');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> SENDING...';
    });
</script>
@endsection
