@extends('layouts.app')

@section('title', $announcement->title)

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('announcements.list') }}" class="text-cyan text-decoration-none orbitron small">
            <i class="bi bi-arrow-left"></i> BACK TO ANNOUNCEMENTS
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <article class="neon-card p-5">
                <header class="mb-5 border-bottom border-secondary pb-4" style="border-bottom-color: rgba(0, 240, 255, 0.1) !important;">
                    <h1 class="orbitron text-white mb-3">{{ $announcement->title }}</h1>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-dark border border-neon-cyan text-cyan px-3 py-2 orbitron small">
                            <i class="bi bi-calendar3 me-2"></i>{{ $announcement->created_at->format('F j, Y') }}
                        </span>
                        <span class="text-muted small orbitron">
                            <i class="bi bi-person-badge me-1"></i> SYSTEM ADMIN
                        </span>
                    </div>
                </header>

                <div class="announcement-body text-light">
                    {!! $announcement->content !!}
                </div>

                <footer class="mt-5 pt-5 border-top border-secondary" style="border-top-color: rgba(0, 240, 255, 0.1) !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="share-links">
                            <span class="text-muted small orbitron me-3">SHARE:</span>
                            <a href="https://twitter.com/intent/tweet?text={{ urlencode($announcement->title) }}&url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="text-cyan me-3" title="Share on X (Twitter)">
                                <i class="bi bi-twitter-x"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="text-cyan me-3" title="Share on Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="javascript:void(0)" onclick="copyAnnouncementUrl()" class="text-cyan" title="Copy Link">
                                <i class="bi bi-link-45deg" style="font-size: 1.4rem; vertical-align: middle;"></i>
                            </a>
                        </div>
                    </div>
                </footer>
            </article>
        </div>
    </div>
</div>

<script>
    function copyAnnouncementUrl() {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            if (typeof window.neonAlert === 'function') {
                window.neonAlert('Announcement link copied to clipboard!', 'LINK COPIED');
            } else {
                alert('Link copied to clipboard!');
            }
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    }
</script>

<style>
    .announcement-body {
        font-size: 1.15rem;
        line-height: 1.8;
        color: #ddd;
    }
    .announcement-body p {
        margin-bottom: 1.5rem;
    }
    .border-neon-cyan {
        border-color: var(--neon-cyan) !important;
    }
</style>
@endsection
