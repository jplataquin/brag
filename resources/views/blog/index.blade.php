@extends('layouts.app')

@section('title', 'Arena Blog')

@section('content')
<div class="container py-5">
    <h1 class="orbitron text-cyan mb-5"><i class="bi bi-megaphone-fill"></i> ARENA BLOG</h1>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            @forelse($posts as $post)
                <div class="card bg-dark border-secondary mb-4 shadow-sm hover-glow">
                    <div class="card-body p-4">
                        <h2 class="orbitron h4 mb-0 text-white">{{ $post->title }}</h2>
                        <span class="text-muted small orbitron">{{ $post->created_at->format('M j, Y') }}</span>
                        
                        <hr class="border-secondary my-3">
                        
                        <div class="text-light mb-4 post-preview">
                            {{ Str::limit(html_entity_decode(strip_tags($post->content), ENT_QUOTES, 'UTF-8'), 300) }}
                        </div>
                        
                        <a href="{{ route('blog.show', $post) }}" class="btn btn-neon btn-sm orbitron">
                            READ FULL POST <i class="bi bi-chevron-right ms-1"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-megaphone-fill text-muted display-1"></i>
                    <p class="text-muted mt-3 orbitron">NO BLOG POSTS YET.</p>
                </div>
            @endforelse

            <div class="mt-5">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    .post-preview {
        line-height: 1.6;
    }
    .hover-glow:hover {
        border-color: #00f0ff !important;
        box-shadow: 0 0 15px rgba(0, 240, 255, 0.2) !important;
        transition: all 0.3s ease;
    }
</style>
@endsection
