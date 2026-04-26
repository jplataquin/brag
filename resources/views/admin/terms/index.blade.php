@extends('layouts.app')

@section('title', 'Manage Terms of Service')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="page-title m-0">
            <span class="page-title-accent"><i class="bi bi-shield-lock-fill"></i></span> MANAGE TERMS OF SERVICE
        </h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="neon-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="neon-text" style="font-size: 1.2rem;">CREATE NEW VERSION</h3>
                    @if($latestTerms)
                        <span class="badge bg-neon-magenta">Current Version: #{{ $latestTerms->id }}</span>
                    @else
                        <span class="badge bg-secondary">No version created yet</span>
                    @endif
                </div>

                <form action="{{ route('admin.terms.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <textarea name="content" id="editor">{{ $latestTerms ? $latestTerms->content : '' }}</textarea>
                    </div>

                    <div class="alert alert-info" style="background: rgba(0, 240, 255, 0.05); border: 1px solid rgba(0, 240, 255, 0.2); color: #00f0ff;">
                        <i class="bi bi-info-circle-fill me-2"></i> Saving a new version will require all users to re-agree upon their next sign-in.
                    </div>

                    <button type="submit" class="btn btn-neon-magenta w-100 py-3 mt-2" style="font-family: 'Orbitron', sans-serif; letter-spacing: 2px;">
                        PUBLISH NEW VERSION
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'],
        })
        .catch(error => {
            console.error(error);
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
