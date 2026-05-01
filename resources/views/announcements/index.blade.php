@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="container py-4">
    <h1 class="orbitron text-cyan mb-5"><i class="bi bi-megaphone-fill"></i> ARENA ANNOUNCEMENTS</h1>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            @forelse($announcements as $announcement)
                <div class="neon-card p-4 mb-4 hover-glow" style="transition: all 0.3s ease; border-left: 4px solid var(--neon-cyan);">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h2 class="orbitron h4 mb-0 text-white">{{ $announcement->title }}</h2>
                        <span class="text-muted small orbitron">{{ $announcement->created_at->format('M j, Y') }}</span>
                    </div>
                    
                    <div class="text-light mb-4 announcement-preview">
                        {!! Str::limit($announcement->content, 300) !!}
                    </div>

                    <a href="{{ route('announcements.show', $announcement) }}" class="btn btn-neon btn-sm orbitron">
                        READ MORE <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-chat-dots text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 orbitron">NO ANNOUNCEMENTS YET.</p>
                </div>
            @endforelse

            <div class="mt-5">
                {{ $announcements->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    .announcement-preview {
        line-height: 1.6;
        color: #ccd !important;
    }
    .hover-glow:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 240, 255, 0.15);
    }
</style>
@endsection
