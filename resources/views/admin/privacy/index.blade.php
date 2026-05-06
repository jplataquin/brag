@extends('layouts.app')

@section('title', 'Manage Privacy Policy')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="page-title m-0">
            <span class="page-title-accent"><i class="bi bi-shield-shaded"></i></span> MANAGE PRIVACY POLICY
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
                    @if($latestPrivacy)
                        <span class="badge bg-neon-magenta">Current Version: #{{ $latestPrivacy->id }}</span>
                    @else
                        <span class="badge bg-secondary">No version created yet</span>
                    @endif
                </div>

                <form action="{{ route('admin.privacy.store') }}" method="POST" id="privacy-form">
                    @csrf
                    <div class="mb-4">
                        <textarea name="content" id="editor">{{ $latestPrivacy ? $latestPrivacy->content : '' }}</textarea>
                    </div>

                    <div class="alert alert-info" style="background: rgba(0, 240, 255, 0.05); border: 1px solid rgba(0, 240, 255, 0.2); color: #00f0ff;">
                        <i class="bi bi-info-circle-fill me-2"></i> Saving a new version will require all users to re-agree upon their next sign-in.
                    </div>

                    <button type="submit" class="btn btn-neon-magenta w-100 py-3 mt-2" style="font-family: 'Orbitron', sans-serif; letter-spacing: 2px;">
                        PUBLISH NEW VERSION
                    </button>
                </form>
            </div>

            <div class="neon-card p-4 mt-4">
                <h3 class="neon-text mb-4" style="font-size: 1.2rem;">VERSION HISTORY</h3>
                @if($history->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0" style="border-color: rgba(0,240,255,0.1);">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-uppercase text-muted small fw-bold">Version ID</th>
                                    <th scope="col" class="text-uppercase text-muted small fw-bold">Date Created</th>
                                    <th scope="col" class="text-uppercase text-muted small fw-bold text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($history as $privacy)
                                    <tr>
                                        <td>
                                            <span class="fw-bold" style="color: var(--neon-cyan);">#{{ $privacy->id }}</span>
                                            @if($latestPrivacy && $latestPrivacy->id === $privacy->id)
                                                <span class="badge bg-neon-magenta ms-2">Current</span>
                                            @endif
                                        </td>
                                        <td class="text-secondary">{{ $privacy->created_at->format('M j, Y h:i A') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.privacy.show_previous', $privacy->id) }}" class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No previous versions available.</p>
                @endif
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

    const form = document.getElementById('privacy-form');
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Ensure editor data is synced back to textarea
            if (editorInstance) {
                editorInstance.updateSourceElement();
            }

            const confirmed = await window.neonConfirm(
                'Are you sure you want to publish this new version of the Privacy Policy? All users will be required to re-agree upon their next sign-in.',
                'PUBLISH VERSION'
            );

            if (confirmed) {
                form.submit();
            }
        });
    }
</script>
@endsection
