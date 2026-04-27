@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-5 fw-bold text-uppercase mb-1" style="color: var(--neon-magenta); text-shadow: 0 0 10px rgba(255, 0, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                <i class="bi bi-pencil-square"></i> Edit Game Title
            </h1>
            <p class="text-secondary lead mb-0">Update the name of an existing game title.</p>
        </div>
        <a href="{{ route('admin.game_titles.index') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left"></i> Back to Game Titles
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-dark bg-opacity-75 border-info rounded-4 p-4 shadow-lg" style="backdrop-filter: blur(10px);">
                <div class="card-body">
                    <form action="{{ route('admin.game_titles.update', $gameTitle->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="title" class="form-label text-muted small text-uppercase fw-bold">Game Title Name <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control form-control-lg bg-dark text-white border-info @error('title') is-invalid @enderror" value="{{ old('title', $gameTitle->title) }}" required autofocus>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-lg fw-bold text-white" style="background-color: var(--neon-magenta); border-color: var(--neon-magenta); box-shadow: 0 0 15px rgba(255, 0, 255, 0.5);">
                                <i class="bi bi-check-lg"></i> Update Game Title
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
