@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-5 fw-bold text-uppercase mb-1" style="color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                <i class="bi bi-controller"></i> Game Titles
            </h1>
            <p class="text-secondary lead mb-0">Manage supported game titles for card forging.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Actions & Alerts -->
    <div class="d-flex justify-content-between mb-4">
        <a href="{{ route('admin.game_titles.create') }}" class="btn btn-neon-cyan">
            <i class="bi bi-plus-lg"></i> Add New Game Title
        </a>
        
        <form action="{{ route('admin.game_titles.index') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control bg-dark text-white border-info" placeholder="Search Game Title..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-info px-4">
                <i class="bi bi-search"></i>
            </button>
            @if(request()->has('search'))
                <a href="{{ route('admin.game_titles.index') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Game Titles Table -->
    <div class="card bg-dark bg-opacity-75 border-info rounded-4 shadow-lg" style="backdrop-filter: blur(10px);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">ID</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3 text-start">Title</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Status</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Templates Count</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Created</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gameTitles as $gameTitle)
                            <tr>
                                <td class="py-3 text-white-50">#{{ $gameTitle->id }}</td>
                                <td class="py-3 text-start">
                                    <span class="fw-bold text-white fs-6">{{ $gameTitle->title }}</span>
                                </td>
                                <td class="py-3">
                                    @if($gameTitle->status === 'active')
                                        <span class="badge bg-success rounded-pill px-3">Active</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-3">Hidden</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-secondary fs-6 rounded-pill px-3">{{ $gameTitle->templates_count }}</span>
                                </td>
                                <td class="py-3 text-white-50">
                                    {{ $gameTitle->created_at->format('M j, Y') }}
                                </td>
                                <td class="py-3">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.game_titles.edit', $gameTitle->id) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.game_titles.destroy', $gameTitle->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this game title? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" {{ $gameTitle->templates_count > 0 ? 'disabled' : '' }} title="{{ $gameTitle->templates_count > 0 ? 'Cannot delete: Templates exist' : 'Delete Game Title' }}">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-5 text-muted text-center">No game titles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($gameTitles->hasPages())
            <div class="card-footer border-info border-top p-3 d-flex justify-content-center bg-transparent">
                {{ $gameTitles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
