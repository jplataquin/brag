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
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let editorInstance;

        ClassicEditor
            .create(document.querySelector('#editor'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'],
            })
            .then(editor => {
                editorInstance = editor;
            })
            .catch(error => {
                console.error(error);
            });

        const form = document.getElementById('privacy-form');
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
