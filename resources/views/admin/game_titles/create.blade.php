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
                    <form action="{{ route('admin.game_titles.store') }}" method="POST" id="game-title-form">
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
                            <label for="header_image" class="form-label text-muted small text-uppercase fw-bold">Header Image</label>
                            <input type="file" id="header_image_input" class="form-control bg-dark text-white border-info" accept="image/*">
                            <input type="hidden" name="temporary_header_path" id="temporary_header_path">
                            <div id="upload-progress-container" class="mt-2" style="display: none;">
                                <div class="progress bg-dark border border-info" style="height: 10px;">
                                    <div id="upload-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" style="width: 0%"></div>
                                </div>
                                <div id="upload-status" class="small mt-1 text-info">Uploading: 0%</div>
                            </div>
                            <div class="form-text text-secondary mt-2">Recommended size: 1200x400. This will be shown on the public showcase page.</div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label text-muted small text-uppercase fw-bold">Description</label>
                            <textarea name="description" id="description" rows="5" class="form-control bg-dark text-white border-info @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-secondary mt-2">Tell users about this game and what kind of cards they can forge.</div>
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
                            <button type="submit" id="btn-submit" class="btn btn-lg btn-neon-cyan fw-bold">
                                <i class="bi bi-check-lg"></i> Save Game Title
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const headerInput = document.getElementById('header_image_input');
    const btnSubmit = document.getElementById('btn-submit');
    const progressContainer = document.getElementById('upload-progress-container');
    const progressBar = document.getElementById('upload-progress-bar');
    const statusText = document.getElementById('upload-status');
    const tempInput = document.getElementById('temporary_header_path');

    headerInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            btnSubmit.disabled = true;
            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';
            statusText.innerText = 'Uploading: 0%';
            statusText.style.color = '#00f0ff';

            const CHUNK_SIZE = 512 * 1024; // 512KB
            const fileId = 'gt_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
            const extension = file.name.split('.').pop();
            let chunkIndex = 0;

            function uploadNextChunk() {
                const start = chunkIndex * CHUNK_SIZE;
                const end = Math.min(start + CHUNK_SIZE, file.size);
                const chunk = file.slice(start, end);

                const formData = new FormData();
                formData.append('file', chunk);
                formData.append('file_id', fileId);
                formData.append('chunk_index', chunkIndex);
                formData.append('total_chunks', totalChunks);
                formData.append('extension', extension);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                fetch('{{ route("upload.chunk") }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        statusText.innerText = 'Upload failed!';
                        statusText.style.color = 'red';
                        btnSubmit.disabled = false;
                        return;
                    }
                    
                    chunkIndex++;
                    const percent = Math.round((chunkIndex / totalChunks) * 100);
                    progressBar.style.width = percent + '%';
                    statusText.innerText = 'Uploading: ' + percent + '%';

                    if (chunkIndex < totalChunks) {
                        uploadNextChunk();
                    } else if (data.success && data.path) {
                        tempInput.value = data.path;
                        statusText.innerText = 'Upload complete!';
                        statusText.style.color = '#39ff14';
                        btnSubmit.disabled = false;
                    }
                })
                .catch(err => {
                    console.error('Upload Error:', err);
                    statusText.innerText = 'Upload error!';
                    statusText.style.color = 'red';
                    btnSubmit.disabled = false;
                });
            }
            uploadNextChunk();
        }
    });
});
</script>
@endsection
