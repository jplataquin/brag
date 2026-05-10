@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<style>
    :root {
        --neon-cyan: #00f0ff;
    }
    .neon-tabs .nav-link {
        color: #8888aa;
        font-family: 'Orbitron', sans-serif;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.9rem;
        border: none;
        padding: 1rem 1.5rem;
        transition: all 0.3s ease;
    }
    .neon-tabs .nav-link:hover {
        color: #fff;
    }
    .neon-tabs .nav-link.active {
        background-color: transparent !important;
        color: var(--neon-cyan) !important;
        border-bottom: 2px solid var(--neon-cyan) !important;
        text-shadow: 0 0 10px rgba(0, 240, 255, 0.5);
    }
</style>
<h1 class="page-title">
    <span class="page-title-accent"><i class="bi bi-gear-fill"></i></span> EDIT PROFILE
</h1>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <ul class="nav nav-tabs neon-tabs border-bottom-0 mb-4" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-pane" type="button" role="tab">
                    <i class="bi bi-person-circle"></i> Profile Info
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="verification-tab" data-bs-toggle="tab" data-bs-target="#verification-pane" type="button" role="tab">
                    <i class="bi bi-shield-check"></i> Verification
                </button>
            </li>
        </ul>

        <div class="tab-content" id="profileTabContent">
            <!-- Profile Info Pane -->
            <div class="tab-pane fade show active" id="profile-pane" role="tabpanel" aria-labelledby="profile-tab">
                <div class="neon-card p-4">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profile-edit-form">
                        @csrf
                        @method('PUT')

                        <div class="text-center mb-4">
                            <img id="current-avatar" src="{{ $user->avatar_url }}" alt="" style="width: 100px; height: 100px; border-radius: 50%; border: 3px solid rgba(0,240,255,0.3); margin-bottom: 0.5rem; object-fit: cover;">
                            <div style="font-size: 0.85rem; color: #00f0ff;">
                                @<x-username :user="$user" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">FIRST NAME</label>
                                <input type="text" class="form-control" value="{{ $user->firstname }}" readonly disabled style="background-color: rgba(255,255,255,0.05); color: #8888aa; border-color: rgba(255,255,255,0.1);">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">LAST NAME</label>
                                <input type="text" class="form-control" value="{{ $user->lastname }}" readonly disabled style="background-color: rgba(255,255,255,0.05); color: #8888aa; border-color: rgba(255,255,255,0.1);">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">BIRTHDATE</label>
                            <input type="text" class="form-control" value="{{ $user->birthdate ? $user->birthdate->format('Y-m-d') : 'N/A' }}" readonly disabled style="background-color: rgba(255,255,255,0.05); color: #8888aa; border-color: rgba(255,255,255,0.1);">
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">BIO</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror"
                                      id="bio" name="bio" rows="3" maxlength="500"
                                      placeholder="Tell the world about your gaming prowess...">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">AVATAR</label>
                            <div class="position-relative" id="photo-upload-wrapper">
                                <input type="file" class="position-absolute w-100 h-100 opacity-0"
                                       style="z-index: 2; cursor: pointer; top: 0; left: 0;"
                                       id="avatar" accept="image/*">
                                <div id="photo-dropzone" class="d-flex flex-column align-items-center justify-content-center p-4 text-center neon-card @error('temporary_avatar_path') border-danger @enderror" style="border: 2px dashed rgba(0, 240, 255, 0.4); background: rgba(0, 240, 255, 0.02); transition: all 0.3s ease;">
                                    <i class="bi bi-cloud-arrow-up-fill mb-2" style="font-size: 2.5rem; color: #00f0ff; text-shadow: 0 0 10px rgba(0,240,255,0.4);"></i>
                                    <span style="font-family: 'Orbitron', sans-serif; color: #00f0ff; font-weight: 600; letter-spacing: 1px;">CLICK OR DRAG PHOTO HERE</span>
                                    <small class="mt-2" style="color: #8888aa; font-size: 0.75rem;">Supports JPEG, PNG, GIF, WebP</small>
                                </div>
                            </div>
                            <input type="hidden" name="temporary_avatar_path" id="temporary_avatar_path" value="{{ old('temporary_avatar_path') }}">
                            @error('temporary_avatar_path')
                                <div class="text-danger mt-1 small" style="text-shadow: 0 0 5px rgba(255,0,0,0.5);">{{ $message }}</div>
                            @enderror

                            <!-- Upload Progress -->
                            <div id="upload-progress-container" class="mt-2" style="display: none;">
                                <div class="progress" style="height: 10px; background-color: #111122;">
                                    <div id="upload-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%; background-color: #00f0ff;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small id="upload-status" style="color: #00f0ff; font-size: 0.75rem;">Uploading: 0%</small>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-neon" id="btn-update-profile">
                                <i class="bi bi-check-lg"></i> SAVE CHANGES
                            </button>
                            <a href="{{ route('profile.show', $user->username) }}" class="btn btn-neon-danger">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Verification Pane -->
            <div class="tab-pane fade" id="verification-pane" role="tabpanel" aria-labelledby="verification-tab">
                <div class="neon-card p-4">
                    <h3 class="h4 text-uppercase fw-bold mb-4" style="font-family: 'Orbitron', sans-serif; color: var(--neon-cyan);">Identity Verification</h3>
                    
                    @if($user->is_verified)
                        <div class="alert alert-success bg-success bg-opacity-10 border-success text-success p-4 rounded-4 mb-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-patch-check-fill me-3" style="font-size: 2.5rem;"></i>
                                <div>
                                    <h4 class="h5 fw-bold mb-1">YOUR IDENTITY IS VERIFIED</h4>
                                    <p class="mb-0">A blue checkmark is now visible next to your username across the platform.</p>
                                </div>
                            </div>
                        </div>
                    @elseif($pending = $user->pending_verification)
                        <div class="alert alert-info bg-info bg-opacity-10 border-info text-info p-4 rounded-4 mb-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock-history me-3" style="font-size: 2.5rem;"></i>
                                <div>
                                    <h4 class="h5 fw-bold mb-1">VERIFICATION PENDING</h4>
                                    <p class="mb-0">Your application was submitted on {{ $pending->created_at->format('M d, Y') }}. Our admin team is reviewing your documents.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-secondary mb-4">Verify your identity to get the blue checkmark. This helps build trust within the community and confirms you are the genuine owner of your cards.</p>
                        
                        <form action="{{ route('verification.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="id_photo" class="form-label text-muted small fw-bold text-uppercase">Government ID</label>
                                    <input type="file" name="id_photo" id="id_photo" class="form-control bg-dark text-white border-info" accept="image/*" required>
                                    <div class="form-text text-secondary mt-2">Upload a clear photo of your driver's license, passport, or national ID.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="selfie_photo" class="form-label text-muted small fw-bold text-uppercase">Selfie with ID</label>
                                    <input type="file" name="selfie_photo" id="selfie_photo" class="form-control bg-dark text-white border-info" accept="image/*" required>
                                    <div class="form-text text-secondary mt-2">Take a photo of yourself holding your ID next to your face. Ensure both your face and the ID details are visible.</div>
                                </div>
                            </div>
                            
                            <div class="mt-4 p-3 rounded bg-dark border border-warning border-opacity-25">
                                <div class="d-flex">
                                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                                    <small class="text-secondary">Your documents are stored securely and are only visible to platform administrators for verification purposes. They will never be shared with other users.</small>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-neon-cyan mt-4 w-100">
                                <i class="bi bi-shield-lock-fill"></i> SUBMIT FOR VERIFICATION
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.getElementById('avatar');
    const dropzone = document.getElementById('photo-dropzone');
    const currentAvatar = document.getElementById('current-avatar');

    photoInput.addEventListener('dragenter', () => {
        dropzone.style.borderColor = '#00f0ff';
        dropzone.style.background = 'rgba(0, 240, 255, 0.1)';
        dropzone.style.boxShadow = '0 0 20px rgba(0, 240, 255, 0.3)';
    });

    photoInput.addEventListener('dragleave', () => {
        dropzone.style.borderColor = 'rgba(0, 240, 255, 0.4)';
        dropzone.style.background = 'rgba(0, 240, 255, 0.02)';
        dropzone.style.boxShadow = 'none';
    });

    photoInput.addEventListener('drop', () => {
        dropzone.style.borderColor = 'rgba(0, 240, 255, 0.4)';
        dropzone.style.background = 'rgba(0, 240, 255, 0.02)';
        dropzone.style.boxShadow = 'none';
    });

    photoInput.addEventListener('mouseenter', () => {
        dropzone.style.borderColor = '#00f0ff';
        dropzone.style.background = 'rgba(0, 240, 255, 0.05)';
        dropzone.style.transform = 'translateY(-2px)';
    });

    photoInput.addEventListener('mouseleave', () => {
        dropzone.style.borderColor = 'rgba(0, 240, 255, 0.4)';
        dropzone.style.background = 'rgba(0, 240, 255, 0.02)';
        dropzone.style.transform = 'translateY(0)';
    });

    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0] || this.files[0];
        if (file) {
            // Update dropzone UI
            dropzone.innerHTML = `
                <i class="bi bi-file-earmark-image-fill mb-2" style="font-size: 2.5rem; color: #39ff14; text-shadow: 0 0 10px rgba(57,255,20,0.4);"></i>
                <span style="font-family: 'Orbitron', sans-serif; color: #39ff14; font-weight: 600; letter-spacing: 1px;">${file.name}</span>
                <small class="mt-2" style="color: #8888aa; font-size: 0.75rem;">Click or drag to change</small>
            `;
            dropzone.style.borderColor = '#39ff14';

            // Local preview
            const reader = new FileReader();
            reader.onload = function(e) {
                currentAvatar.src = e.target.result;
            };
            reader.readAsDataURL(file);

            // Chunk Upload
            const btnSubmit = document.getElementById('btn-update-profile');
            const progressContainer = document.getElementById('upload-progress-container');
            const progressBar = document.getElementById('upload-progress-bar');
            const statusText = document.getElementById('upload-status');
            const tempInput = document.getElementById('temporary_avatar_path');

            btnSubmit.disabled = true;
            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';
            statusText.innerText = 'Uploading: 0%';

            const CHUNK_SIZE = 256 * 1024; // 256KB
            const fileId = Date.now() + '_' + Math.random().toString(36).substr(2, 9);
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
                .catch(error => {
                    console.error('Upload Error:', error);
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
