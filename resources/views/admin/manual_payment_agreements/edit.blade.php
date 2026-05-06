@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-5 fw-bold text-uppercase mb-1" style="color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                <i class="bi bi-pencil-square"></i> Edit Agreement
            </h1>
            <p class="text-secondary lead mb-0">Update an existing manual payment agreement.</p>
        </div>
        <a href="{{ route('admin.manual-payment-agreements.index') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card bg-dark bg-opacity-75 border-info rounded-4 p-4 shadow-lg" style="backdrop-filter: blur(10px);">
                <div class="card-body">
                    <form action="{{ route('admin.manual-payment-agreements.update', $manualPaymentAgreement->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="editor" class="form-label text-muted small text-uppercase fw-bold">Agreement Content <span class="text-danger">*</span></label>
                            <textarea name="content" id="editor" rows="12" class="form-control bg-dark text-white border-info @error('content') is-invalid @enderror" required placeholder="Enter the terms, conditions, and disclaimers users must agree to before submitting manual payments...">{{ old('content', $manualPaymentAgreement->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 mt-5">
                            <button type="submit" class="btn btn-lg btn-neon-cyan fw-bold">
                                <i class="bi bi-check-lg"></i> Update Agreement
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
