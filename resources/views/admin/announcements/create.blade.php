@extends('layouts.app')

@section('title', 'Create Announcement')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('admin.announcements.index') }}" class="text-cyan text-decoration-none orbitron small">
            <i class="bi bi-arrow-left"></i> BACK TO LIST
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="orbitron text-cyan mb-4">CREATE ANNOUNCEMENT</h1>
            
            <div class="neon-card p-4">
                <form action="{{ route('admin.announcements.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label orbitron text-cyan">TITLE</label>
                        <input type="text" name="title" class="form-control bg-dark border-secondary text-white" required value="{{ old('title') }}">
                        @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label orbitron text-cyan">CONTENT</label>
                        <div class="ck-editor-container">
                            <textarea name="content" id="editor" class="form-control bg-dark border-secondary text-white" rows="10">{{ old('content') }}</textarea>
                        </div>
                        @error('content') <span class="text-danger small">{{ $message }}</span> @enderror
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

<style>
    .ck-editor__animated-shortcut { display: none !important; }
    .ck-editor__view-wrapper { background: #111122 !important; color: #fff !important; border-color: #444 !important; }
    .ck-content { min-height: 300px; background-color: #111122 !important; color: #fff !important; }
    .ck.ck-editor__main>.ck-editor__editable { background: #111122 !important; border-color: #444 !important; }
    .ck.ck-toolbar { background: #1a1a2e !important; border-color: #444 !important; }
    .ck.ck-toolbar__separator { background: #444 !important; }
    .ck.ck-button { color: #fff !important; cursor: pointer; }
    .ck.ck-button:hover { background: #2a2a4e !important; }
    .ck.ck-button.ck-on { background: #00f0ff33 !important; color: #00f0ff !important; }
    .ck.ck-list { background: #1a1a2e !important; }
    .ck.ck-list__item .ck-button:hover { background: #2a2a4e !important; }
</style>

@section('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        ClassicEditor
            .create(document.querySelector('#editor'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'],
            })
            .catch(error => {
                console.error(error);
            });
    });
</script>
@endsection
