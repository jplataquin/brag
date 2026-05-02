@extends('layouts.app')

@section('title', 'Manage Blog')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="orbitron text-cyan"><i class="bi bi-megaphone-fill"></i> BLOG POSTS</h1>
        <a href="{{ route('admin.blog.create') }}" class="btn btn-neon">
            <i class="bi bi-plus-lg"></i> CREATE POST
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-dark border-success text-success orbitron small mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="card bg-dark border-secondary shadow-sm">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr class="orbitron text-muted small">
                        <th>TITLE</th>
                        <th>STATUS</th>
                        <th>CREATED AT</th>
                        <th class="text-end">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $post->title }}</div>
                                <div class="small text-muted">{{ Str::limit(strip_tags($post->content), 100) }}</div>
                            </td>
                            <td>
                                @if($post->is_published)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td>{{ $post->created_at->format('M j, Y H:i') }}</td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('admin.blog.edit', $post) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" x-data x-on:click="window.neonConfirm('Are you sure you want to delete this blog post?').then(c => { if(c) document.getElementById('delete-post-{{ $post->id }}').submit() })">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-post-{{ $post->id }}" action="{{ route('admin.blog.destroy', $post) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted orbitron">
                                No blog posts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $posts->links() }}
    </div>
</div>
@endsection
