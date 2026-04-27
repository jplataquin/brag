@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-5 fw-bold text-uppercase mb-1" style="color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                <i class="bi bi-plus-circle"></i> Add Game Title
            </h1>
            <p class="text-secondary lead mb-0">Create a new game title to allow users to forge related cards.</p>
        </div>
        <a href="{{ route('admin.game_titles.index') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left"></i> Back to Game Titles
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-dark bg-opacity-75 border-info rounded-4 p-4 shadow-lg" style="backdrop-filter: blur(10px);">
                <div class="card-body">
                    <form action="{{ route('admin.game_titles.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="title" class="form-label text-muted small text-uppercase fw-bold">Game Title Name <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control form-control-lg bg-dark text-white border-info @error('title') is-invalid @enderror" value="{{ old('title') }}" required autofocus>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-secondary mt-2">Example: "Valorant", "League of Legends", "Street Fighter 6"</div>
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label text-muted small text-uppercase fw-bold">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select form-control-lg bg-dark text-white border-info @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active (Visible to Users)</option>
                                <option value="hidden" {{ old('status') == 'hidden' ? 'selected' : '' }}>Hidden (Not Visible)</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-secondary mt-2">Hidden game titles cannot be selected by users when forging new templates.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-lg btn-neon-cyan fw-bold">
                                <i class="bi bi-check-lg"></i> Save Game Title
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
