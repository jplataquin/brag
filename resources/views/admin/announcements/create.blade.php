@extends('layouts.app')

@section('title', 'Create Announcement')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('announcements.index') }}" class="text-cyan text-decoration-none orbitron small">
            <i class="bi bi-arrow-left"></i> BACK TO LIST
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="orbitron text-cyan mb-4">CREATE ANNOUNCEMENT</h1>
            
            <div class="neon-card p-4">
                <form action="{{ route('announcements.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label orbitron text-cyan">TITLE</label>
                        <input type="text" name="title" class="form-control bg-dark border-secondary text-white" required value="{{ old('title') }}">
                        @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label orbitron text-cyan">CONTENT</label>
                        <textarea name="content" class="form-control bg-dark border-secondary text-white" rows="10" required>{{ old('content') }}</textarea>
                        @error('content') <span class="text-danger small">{{ $message }}</span> @enderror
                        <p class="text-muted small mt-2 italic">HTML tags are allowed for basic formatting.</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_published" value="1" id="is_published" checked>
                                <label class="form-check-label text-light" for="is_published">Publish immediately</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="notify_users" value="1" id="notify_users" checked>
                                <label class="form-check-label text-light" for="notify_users">Notify all users</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-neon w-100 py-3 orbitron">
                        <i class="bi bi-send-fill"></i> POST ANNOUNCEMENT
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
