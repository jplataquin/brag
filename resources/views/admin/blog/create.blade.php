@extends('layouts.app')

@section('title', 'Create Blog Post')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <a href="{{ route('admin.blog.index') }}" class="text-cyan text-decoration-none orbitron small">
                <i class="bi bi-arrow-left"></i> BACK TO BLOG LIST
            </a>

            <div class="card bg-dark border-secondary mt-4 shadow-lg">
                <div class="card-body p-4 p-md-5">
                    <h1 class="orbitron text-cyan mb-4">CREATE BLOG POST</h1>

                    <form action="{{ route('admin.blog.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label orbitron text-muted small">POST TITLE</label>
                            <input type="text" name="title" class="form-control bg-dark border-secondary text-white @error('title') is-invalid @enderror" required value="{{ old('title') }}" placeholder="Enter post title...">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label orbitron text-muted small">CONTENT</label>
                            <textarea name="content" id="editor" class="form-control bg-dark border-secondary text-white @error('content') is-invalid @enderror" rows="15">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row align-items-center mb-5">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3 mb-md-0">
                                    <input class="form-check-input" type="checkbox" name="is_published" value="1" id="is_published" checked>
                                    <label class="form-check-label orbitron text-white small" for="is_published">PUBLISH IMMEDIATELY</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="notify_users" value="1" id="notify_users">
                                    <label class="form-check-label orbitron text-white small" for="notify_users">NOTIFY ALL USERS VIA NOTIFICATION</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-neon btn-lg orbitron py-3">
                                <i class="bi bi-send-fill"></i> POST TO BLOG
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CKEditor or similar can be integrated here --}}
@endsection
