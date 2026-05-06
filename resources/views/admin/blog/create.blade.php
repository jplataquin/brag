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

@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.0.0/ckeditor5.css">
<style>
    :root {
        --ck-color-base-background: #111122;
        --ck-color-base-border: rgba(0, 240, 255, 0.2);
        --ck-color-base-text: #fff;
        --ck-color-text: #fff;
        --ck-color-toolbar-background: #0a0a1a;
        --ck-color-toolbar-border: rgba(0, 240, 255, 0.2);
        --ck-color-button-default-hover-background: rgba(0, 240, 255, 0.1);
        --ck-color-button-on-background: rgba(0, 240, 255, 0.2);
        --ck-color-button-on-color: #00f0ff;
        --ck-color-list-background: #0a0a1a;
        --ck-color-list-button-hover-background: rgba(0, 240, 255, 0.1);
        --ck-color-panel-background: #0a0a1a;
        --ck-color-panel-border: rgba(0, 240, 255, 0.2);
        --ck-color-dropdown-panel-background: #0a0a1a;
        --ck-color-dropdown-panel-border: rgba(0, 240, 255, 0.2);
        --ck-color-input-background: #111122;
        --ck-color-input-border: rgba(0, 240, 255, 0.2);
        --ck-color-input-text: #fff;
        --ck-color-list-button-on-background: rgba(0, 240, 255, 0.2);
        --ck-color-list-button-on-text: #00f0ff;
    }
    .ck-editor__editable {
        min-height: 400px;
    }
    .ck.ck-editor__main>.ck-editor__editable:not(.ck-focused) {
        border-color: rgba(0, 240, 255, 0.1);
    }
</style>
<script type="importmap">
{
    "imports": {
        "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/43.0.0/ckeditor5.js",
        "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/43.0.0/"
    }
}
</script>
<script type="module">
    import {
        ClassicEditor,
        Essentials,
        Paragraph,
        Heading,
        Bold,
        Italic,
        Link,
        List,
        Indent,
        IndentBlock,
        BlockQuote,
        Undo
    } from 'ckeditor5';

    let editorInstance;

    ClassicEditor
        .create(document.querySelector('#editor'), {
            plugins: [ Essentials, Paragraph, Heading, Bold, Italic, Link, List, Indent, IndentBlock, BlockQuote, Undo ],
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'outdent', 'indent', 'blockQuote', 'undo', 'redo' ]
        })
        .then(editor => {
            editorInstance = editor;
        })
        .catch(error => {
            console.error(error);
        });

    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            if (editorInstance) {
                editorInstance.updateSourceElement();
            }
        });
    }
</script>
@endsection
