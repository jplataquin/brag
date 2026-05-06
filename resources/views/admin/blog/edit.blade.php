@extends('layouts.app')

@section('title', 'Edit Blog Post')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <a href="{{ route('admin.blog.index') }}" class="text-cyan text-decoration-none orbitron small">
                <i class="bi bi-arrow-left"></i> BACK TO BLOG LIST
            </a>

            <div class="card bg-dark border-secondary mt-4 shadow-lg">
                <div class="card-body p-4 p-md-5">
                    <h1 class="orbitron text-cyan mb-4">EDIT BLOG POST</h1>

                    <form action="{{ route('admin.blog.update', $post) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label orbitron text-muted small">POST TITLE</label>
                            <input type="text" name="title" class="form-control bg-dark border-secondary text-white @error('title') is-invalid @enderror" required value="{{ old('title', $post->title) }}">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label orbitron text-muted small">CONTENT</label>
                            <textarea name="content" id="editor" class="form-control bg-dark border-secondary text-white @error('content') is-invalid @enderror" rows="15">{{ old('content', $post->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_published" value="1" id="is_published" {{ $post->is_published ? 'checked' : '' }}>
                                <label class="form-check-label orbitron text-white small" for="is_published">PUBLISHED STATUS</label>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-neon btn-lg orbitron py-3">
                                <i class="bi bi-save-fill"></i> UPDATE BLOG POST
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let editorInstance;

        ClassicEditor
            .create(document.querySelector('#editor'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'outdent', 'indent', 'blockQuote', 'undo', 'redo'],
            })
            .then(editor => {
                editorInstance = editor;
            })
            .catch(error => {
                console.error(error);
            });

        const form = document.querySelector('form');
        form.addEventListener('submit', function() {
            if (editorInstance) {
                editorInstance.updateSourceElement();
            }
        });
    });
</script>
<style>
    .ck-editor__editable {
        min-height: 400px;
        background-color: #111122 !important;
        color: #fff !important;
        border: 1px solid rgba(0, 240, 255, 0.2) !important;
    }
    .ck.ck-editor__main>.ck-editor__editable:not(.ck-focused) {
        border-color: rgba(0, 240, 255, 0.1);
    }
    .ck.ck-toolbar {
        background-color: #0a0a1a !important;
        border: 1px solid rgba(0, 240, 255, 0.2) !important;
    }
    .ck.ck-button {
        color: #fff !important;
    }
    .ck.ck-button:hover {
        background-color: rgba(0, 240, 255, 0.1) !important;
    }
    .ck.ck-button.ck-on {
        background-color: rgba(0, 240, 255, 0.2) !important;
        color: #00f0ff !important;
    }
</style>
@endsection
