@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <a href="{{ route('blog.index') }}" class="text-cyan text-decoration-none orbitron small">
                <i class="bi bi-arrow-left"></i> BACK TO BLOG
            </a>

            <div class="card bg-dark border-secondary mt-4 shadow-lg">
                <div class="card-body p-5">
                    <header class="mb-5">
                        <h1 class="orbitron text-white mb-3">{{ $post->title }}</h1>
                        <div class="d-flex align-items-center text-muted small orbitron">
                            <span class="me-4">
                                <i class="bi bi-calendar3 me-2"></i>{{ $post->created_at->format('F j, Y') }}
                            </span>
                            <span>
                                <i class="bi bi-clock me-2"></i>{{ ceil(str_word_count(strip_tags($post->content)) / 200) }} min read
                            </span>
                        </div>
                    </header>

                    <div class="post-body text-light">
                        {!! $post->content !!}
                    </div>

                    <hr class="border-secondary my-5">

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="share-buttons">
                            <span class="text-muted small orbitron me-3">SHARE:</span>
                            <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="text-cyan me-3" title="Share on X (Twitter)">
                                <i class="bi bi-twitter-x"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="text-cyan me-3" title="Share on Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="javascript:void(0)" onclick="copyPostUrl()" class="text-cyan" title="Copy Link">
                                <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                        
                        <div class="tags">
                            <span class="badge bg-dark border border-cyan text-cyan orbitron">#ARENA</span>
                            <span class="badge bg-dark border border-magenta text-magenta orbitron">#BRAG</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function copyPostUrl() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            if (window.neonAlert) {
                window.neonAlert('Blog post link copied to clipboard!', 'LINK COPIED');
            } else {
                alert('Link copied!');
            }
        });
    }
</script>

<style>
    .post-body {
        font-size: 1.1rem;
        line-height: 1.8;
    }
    .post-body p {
        margin-bottom: 1.5rem;
    }
    .post-body h2, .post-body h3 {
        color: #00f0ff;
        font-family: 'Orbitron', sans-serif;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }
</style>
@endsection
