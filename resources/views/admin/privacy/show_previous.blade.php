@extends('layouts.app')

@section('title', 'View Privacy Policy Version #' . $privacy->id)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title m-0">
            <span class="page-title-accent"><i class="bi bi-file-earmark-lock"></i></span> PRIVACY POLICY <span class="text-muted fs-4">#{{ $privacy->id }}</span>
        </h1>
        <a href="{{ route('admin.privacy.index') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left"></i> Back to Manage Privacy
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card bg-dark bg-opacity-75 border-info rounded-4 p-4 shadow-lg" style="backdrop-filter: blur(10px); border: 1px solid rgba(0, 240, 255, 0.2);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom border-info pb-3 mb-4">
                        <span class="text-muted">
                            <i class="bi bi-calendar3"></i> Published on: {{ $privacy->created_at->format('F j, Y, g:i a') }}
                        </span>
                        @if(\App\Models\PrivacyPolicy::latest('id')->first()->id === $privacy->id)
                            <span class="badge bg-neon-magenta text-uppercase tracking-wide px-3 py-2">Current Version</span>
                        @else
                            <span class="badge bg-secondary text-uppercase tracking-wide px-3 py-2">Archived Version</span>
                        @endif
                    </div>

                    <div class="privacy-content text-white-50" style="line-height: 1.8;">
                        {!! $privacy->content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .privacy-content h1, .privacy-content h2, .privacy-content h3 {
        color: var(--neon-cyan);
        font-family: 'Orbitron', sans-serif;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
    }
    .privacy-content a {
        color: var(--neon-magenta);
        text-decoration: none;
    }
    .privacy-content a:hover {
        text-decoration: underline;
    }
    .privacy-content ul, .privacy-content ol {
        margin-bottom: 1.5rem;
    }
    .tracking-wide { letter-spacing: 1px; }
</style>
@endsection
