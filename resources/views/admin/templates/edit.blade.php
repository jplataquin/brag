@extends('layouts.app')

@section('title', 'Edit Template #' . $template->id)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title m-0">
            <span class="page-title-accent"><i class="bi bi-images"></i></span> EDIT TEMPLATE <span class="text-muted fs-4">#{{ $template->id }}</span>
        </h1>
        <a href="{{ route('admin.templates.index') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left"></i> Back to Templates
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" style="background: rgba(255, 0, 0, 0.1); border: 1px solid rgba(255, 0, 0, 0.3); color: #ff4444;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Edit Form -->
        <div class="col-lg-8 mb-4">
            <div class="card bg-dark bg-opacity-75 border-info rounded-4 p-4 shadow-lg" style="backdrop-filter: blur(10px);">
                <form action="{{ route('admin.templates.update', $template->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <h4 class="text-neon-cyan mb-4" style="font-family: 'Orbitron', sans-serif;">Template Details</h4>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label text-white-50">Creator (User)</label>
                            <select name="user_id" id="user_id" class="form-select bg-dark text-white border-info" required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $template->user_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->username }} (ID: {{ $user->id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="game_title_id" class="form-label text-white-50">Game Title</label>
                            <select name="game_title_id" id="game_title_id" class="form-select bg-dark text-white border-info" required>
                                @foreach($gameTitles as $game)
                                    <option value="{{ $game->id }}" {{ $template->game_title_id == $game->id ? 'selected' : '' }}>
                                        {{ $game->title }} (ID: {{ $game->id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="card_title" class="form-label text-white-50">Card Title</label>
                        <input type="text" name="card_title" id="card_title" class="form-control bg-dark text-white border-info" value="{{ $template->card_title }}" required maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label for="quote" class="form-label text-white-50">Quote</label>
                        <textarea name="quote" id="quote" class="form-control bg-dark text-white border-info" rows="2" required maxlength="500">{{ $template->quote }}</textarea>
                    </div>

                    @if($template->is_premium)
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label text-neon-cyan small fw-bold">Price (Diamonds)</label>
                            <input type="number" name="price" id="price" class="form-control bg-dark text-white border-info" value="{{ old('price', $template->price) }}" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label text-neon-cyan small fw-bold">Status</label>
                            <select name="status" id="status" class="form-select bg-dark text-white border-info" required>
                                <option value="inactive" {{ $template->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="active" {{ $template->status === 'active' ? 'selected' : '' }}>Active</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="designer_name" class="form-label text-neon-cyan small fw-bold">Designer Name</label>
                        <input type="text" name="designer_name" id="designer_name" class="form-control bg-dark text-white border-info" value="{{ old('designer_name', $template->designer_name) }}" placeholder="Credit the artist">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label text-neon-cyan small fw-bold">Description</label>
                        <textarea name="description" id="description" class="form-control bg-dark text-white border-info" rows="3" placeholder="Marketing description for the shop">{{ old('description', $template->description) }}</textarea>
                    </div>
                    @endif

                    <div class="mb-4">
                        <label class="form-label text-neon-yellow">CARD IMAGE SOURCE (OPTIONAL)</label>
                        
                        <div class="d-flex gap-3 mb-3">
                            <div class="flex-fill">
                                <input type="radio" class="btn-check" name="image_mode" id="mode_upload" value="upload" checked autocomplete="off">
                                <label class="btn btn-outline-neon w-100" for="mode_upload">
                                    <i class="bi bi-cloud-upload"></i> UPLOAD PHOTO
                                </label>
                            </div>
                            <div class="flex-fill">
                                <input type="radio" class="btn-check" name="image_mode" id="mode_ai" value="ai" autocomplete="off">
                                <label class="btn btn-outline-neon-magenta w-100" for="mode_ai">
                                    <i class="bi bi-magic"></i> AI GENERATE
                                </label>
                            </div>
                        </div>

                        <!-- Upload Section -->
                        <div id="section_upload" style="display: {{ old('image_mode', 'upload') == 'upload' ? 'block' : 'none' }};">
                            <div class="position-relative" id="photo-upload-wrapper">
                                <input type="file" class="position-absolute w-100 h-100 opacity-0"
                                       style="z-index: 2; cursor: pointer; top: 0; left: 0;"
                                       id="photo" accept="image/*">
                                <div id="photo-dropzone" class="d-flex flex-column align-items-center justify-content-center p-4 text-center neon-card @error('temporary_photo_path') border-danger @enderror" style="border: 2px dashed rgba(0, 240, 255, 0.4); background: rgba(0, 240, 255, 0.02); transition: all 0.3s ease;">
                                    <i class="bi bi-cloud-arrow-up-fill mb-2" style="font-size: 2.5rem; color: #00f0ff; text-shadow: 0 0 10px rgba(0,240,255,0.4);"></i>
                                    <span style="font-family: 'Orbitron', sans-serif; color: #00f0ff; font-weight: 600; letter-spacing: 1px;">CLICK OR DRAG PHOTO HERE</span>
                                    <small class="mt-2" style="color: #8888aa; font-size: 0.75rem;">Uploading a new image will overwrite the existing one.</small>
                                </div>
                            </div>
                            <input type="hidden" name="temporary_photo_path" id="temporary_photo_path" value="{{ old('temporary_photo_path') }}">
                            @error('temporary_photo_path')
                                <div class="text-danger mt-1 small" style="text-shadow: 0 0 5px rgba(255,0,0,0.5);">{{ $message }}</div>
                            @enderror

                            <!-- Upload Progress -->
                            <div id="upload-progress-container" class="mt-2" style="display: none;">
                                <div class="progress" style="height: 10px; background-color: #111122;">
                                    <div id="upload-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%; background-color: #00f0ff;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div id="upload-status" class="text-center mt-1 small" style="color: #00f0ff;"></div>
                            </div>
                        </div>

                        <!-- AI Section -->
                        <div id="section_ai" class="p-3 neon-card" style="display: none; border: 1px dashed rgba(255, 0, 255, 0.4); background: rgba(255, 0, 255, 0.02);">
                            <label for="ai_prompt" class="form-label text-white-50">Art Style / Character Description</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control bg-dark text-white border-info" id="ai_prompt" name="ai_prompt" placeholder="e.g. Cyberpunk ninja, neon city background, realistic...">
                                <button class="btn btn-neon-magenta" type="button" id="btn-preview-ai">
                                    <i class="bi bi-magic"></i> Generate
                                </button>
                            </div>
                            <small class="text-muted d-block mb-3">Powered by Nano Banana AI. Generates a new image based on your prompt.</small>
                            
                            <div id="ai-loading" class="text-center my-3" style="display: none;">
                                <div class="spinner-border text-magenta" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-magenta" style="font-family: 'Orbitron', sans-serif;">GENERATING IMAGE...</p>
                            </div>

                            <div id="ai-result-container" class="text-center" style="display: none;">
                                <p class="text-success small mb-2"><i class="bi bi-check-circle-fill"></i> Image generated successfully!</p>
                                <input type="hidden" id="generated_ai_photo" name="generated_ai_photo">
                            </div>
                        </div>

                        <!-- Background Removal Feature -->
                        <div id="bg-removal-container" class="mt-4 pt-3 border-top border-secondary">
                            <button type="button" id="btn-remove-bg" class="btn btn-outline-neon-magenta w-100 mb-2">
                                <span class="spinner-border spinner-border-sm d-none" id="bg-remove-spinner" role="status" aria-hidden="true"></span>
                                <span id="bg-remove-text"><i class="bi bi-person-bounding-box"></i> REMOVE BACKGROUND</span>
                            </button>
                            <div id="bg-removal-progress-container" style="display: none;">
                                <div class="progress" style="height: 8px; background-color: #111122; border-radius: 4px; overflow: hidden;">
                                    <div id="bg-removal-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%; background-color: #ff00ff;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small id="bg-removal-status" style="color: #ff00ff; font-size: 0.7rem; font-family: 'Orbitron', sans-serif;">Initializing background removal...</small>
                            </div>
                            <small class="text-center d-block text-muted" style="font-size: 0.7rem;">
                                Runs locally in your browser. Powered by @imgly.
                            </small>
                        </div>
                    </div>

                    <hr class="border-info my-4">
                    <h4 class="text-neon-magenta mb-4" style="font-family: 'Orbitron', sans-serif;">Design Settings</h4>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="background_color" class="form-label text-white-50">Background Color</label>
                            <input type="color" name="background_color" id="background_color" class="form-control form-control-color bg-dark border-info w-100" value="{{ $template->background_color ?? '#000000' }}" title="Choose Background color">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="border_color" class="form-label text-white-50">Border Color</label>
                            <input type="color" name="border_color" id="border_color" class="form-control form-control-color bg-dark border-info w-100" value="{{ $template->border_color ?? '#ffffff' }}" title="Choose Border color">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="section_color" class="form-label text-white-50">Section Color</label>
                            <input type="color" name="section_color" id="section_color" class="form-control form-control-color bg-dark border-info w-100" value="{{ $template->section_color ?? '#222222' }}" title="Choose Section color">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="primary_text_color" class="form-label text-white-50">Primary Text Color</label>
                            <input type="color" name="primary_text_color" id="primary_text_color" class="form-control form-control-color bg-dark border-info w-100" value="{{ $template->primary_text_color ?? '#ffffff' }}" title="Choose Primary Text color">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="secondary_text_color" class="form-label text-white-50">Secondary Text Color</label>
                            <input type="color" name="secondary_text_color" id="secondary_text_color" class="form-control form-control-color bg-dark border-info w-100" value="{{ $template->secondary_text_color ?? '#aaaaaa' }}" title="Choose Secondary Text color">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="image_position_x" class="form-label text-white-50">Image X (%)</label>
                            <input type="number" name="image_position_x" id="image_position_x" class="form-control bg-dark text-white border-info" value="{{ $template->image_position_x ?? 50 }}" min="0" max="100">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="image_position_y" class="form-label text-white-50">Image Y (%)</label>
                            <input type="number" name="image_position_y" id="image_position_y" class="form-control bg-dark text-white border-info" value="{{ $template->image_position_y ?? 50 }}" min="0" max="100">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="image_scale" class="form-label text-white-50">Zoom</label>
                            <input type="number" name="image_scale" id="image_scale" class="form-control bg-dark text-white border-info" value="{{ $template->image_scale ?? 1.0 }}" min="1.0" max="5" step="0.01">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="image_stretch_y" class="form-label text-white-50">Stretch Y</label>
                            <input type="number" name="image_stretch_y" id="image_stretch_y" class="form-control bg-dark text-white border-info" value="{{ $template->image_stretch_y ?? 1.0 }}" min="0.5" max="2.0" step="0.01">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-neon-warning w-100 py-3 mt-4" style="font-family: 'Orbitron', sans-serif; letter-spacing: 2px;">
                        SAVE TEMPLATE CHANGES
                    </button>
                </form>
            </div>
        </div>

        <!-- Tracking Info & Preview -->
        <div class="col-lg-4">
            <div class="card bg-dark bg-opacity-75 border-info rounded-4 p-4 shadow-lg mb-4" style="backdrop-filter: blur(10px);">
                <h5 class="text-neon-yellow mb-3" style="font-family: 'Orbitron', sans-serif;"><i class="bi bi-clock-history"></i> Tracking Info</h5>
                
                <ul class="list-group list-group-flush bg-transparent">
                    <li class="list-group-item bg-transparent text-white-50 px-0 border-info">
                        <strong>Created At:</strong><br>
                        <span class="text-white">{{ $template->created_at->format('M j, Y H:i:s') }}</span>
                    </li>
                    <li class="list-group-item bg-transparent text-white-50 px-0 border-info">
                        <strong>Status:</strong><br>
                        @if($template->trashed())
                            <span class="text-danger">Deleted ({{ $template->deleted_at->format('M j, Y H:i') }})</span>
                        @else
                            <span class="text-success">Active</span>
                        @endif
                    </li>
                    <li class="list-group-item bg-transparent text-white-50 px-0 border-info">
                        <strong>Last Edited By Admin:</strong><br>
                        @if($template->adminEditor)
                            <span class="text-warning">{{ $template->adminEditor->username }}</span><br>
                            <span class="text-white small">{{ $template->admin_edited_at->format('M j, Y H:i:s') }}</span>
                        @else
                            <span class="text-muted">Never edited manually</span>
                        @endif
                    </li>
                </ul>
            </div>

            <!-- Preview Thumbnail -->
            <div class="card bg-dark border-info rounded-4 shadow-lg overflow-hidden">
                 <div class="card-header border-info bg-dark text-center">
                     <span class="text-muted small text-uppercase">Current Design Preview</span>
                 </div>
                 <div class="card-body p-0 d-flex justify-content-center align-items-center p-3" style="min-height: 400px; background: #050505;">
                     @php
                        // Create a dummy digital card instance for the component
                        $dummyCard = new \App\Models\DigitalCard();
                        $dummyCard->id = 0;
                        $dummyCard->level = 1;
                        $dummyCard->wins = 0;
                        $dummyCard->losses = 0;
                        $dummyCard->is_trophy = false;
                        $dummyCard->setRelation('template', $template);
                     @endphp
                     
                     <div style="transform: scale(0.85); transform-origin: top center; width: 100%;">
                        <x-digital-card 
                            id="card_canvas_admin_preview" 
                            :card="$dummyCard" 
                            :title="$template->card_title"
                            :game="$template->gameTitle->title ?? 'GAME'"
                            :creator="$template->user->username ?? 'CREATOR'"
                            :quote="$template->quote"
                            :image="$template->display_photo"
                            :imagePositionX="$template->image_position_x ?? 50"
                            :imagePositionY="$template->image_position_y ?? 50"
                            :imageScale="$template->image_scale ?? 1.0"
                            :imageStretchY="$template->image_stretch_y ?? 1.0"
                            :backgroundColor="$template->background_color"
                            :borderColor="$template->border_color"
                            :sectionColor="$template->section_color"
                            :primaryTextColor="$template->primary_text_color"
                            :secondaryTextColor="$template->secondary_text_color"
                        />
                     </div>
                 </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Live update helper for the digital card component
    function updateLivePreview(updates) {
        if(window.updateDigitalCard_card_canvas_admin_preview) {
            window.updateDigitalCard_card_canvas_admin_preview(updates);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Preload background removal model
        if (window.preloadImageBackgroundModel) {
            window.preloadImageBackgroundModel();
        }

        // Handle Game Title Dropdown changes
        const gameSelect = document.getElementById('game_title_id');
        if (gameSelect) {
            gameSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const gameText = selectedOption.text.split(' (ID:')[0].trim().toUpperCase();
                updateLivePreview({ game: gameText });
            });
        }

        // Handle User Dropdown changes
        const userSelect = document.getElementById('user_id');
        if (userSelect) {
            userSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const userText = selectedOption.text.split(' (ID:')[0].trim();
                updateLivePreview({ creator: userText });
            });
        }

        // Image Mode Toggle Logic
        document.querySelectorAll('input[name="image_mode"]').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'upload') {
                    document.getElementById('section_upload').style.display = 'block';
                    document.getElementById('section_ai').style.display = 'none';
                    
                    // The actual file input preview isn't live until upload, but we could hook into FileReader if we wanted.
                    // For now, it just falls back to the original image until saved unless a new one was uploaded.
                    const tempInput = document.getElementById('temporary_photo_path');
                    if (tempInput.value) {
                        // Ideally we'd show the preview of the newly uploaded image, but for now we just keep whatever is in the live preview
                    }
                } else {
                    document.getElementById('section_upload').style.display = 'none';
                    document.getElementById('section_ai').style.display = 'block';
                    
                    const generatedPhotoUrl = document.getElementById('generated_ai_photo').getAttribute('data-url');
                    if (generatedPhotoUrl) {
                        updateLivePreview({ image: generatedPhotoUrl });
                    }
                }
            });
        });

        // Background Removal Logic
        document.getElementById('btn-remove-bg').addEventListener('click', async function() {
            const renderer = window.digitalCardRenderers ? window.digitalCardRenderers['card_canvas_admin_preview'] : null;
            if (!renderer || !renderer.currentOptions || !renderer.currentOptions.image) {
                window.neonAlert('No image found in preview to process.');
                return;
            }

            const btn = this;
            const btnText = document.getElementById('bg-remove-text');
            const btnSpinner = document.getElementById('bg-remove-spinner');
            const progressContainer = document.getElementById('bg-removal-progress-container');
            const progressBar = document.getElementById('bg-removal-progress-bar');
            const statusText = document.getElementById('bg-removal-status');
            const submitBtn = document.getElementById('btn-update-template');

            try {
                btn.disabled = true;
                btnSpinner.classList.remove('d-none');
                btnText.innerText = ' PROCESSING...';
                submitBtn.disabled = true;
                progressContainer.style.display = 'block';
                progressBar.style.width = '0%';
                statusText.innerText = 'Initializing background removal...';
                statusText.style.color = '#ff00ff';

                const blob = await window.removeImageBackground(renderer.currentOptions.image, (key, current, total) => {
                    const percent = Math.round((current / total) * 100);
                    progressBar.style.width = percent + '%';
                    statusText.innerText = `Downloading model: ${percent}%`;
                    if (percent === 100) {
                        statusText.innerText = 'Processing image (this may take a few seconds)...';
                    }
                });

                statusText.innerText = 'Background removed! Preparing upload...';
                
                // Switch to upload mode
                document.getElementById('mode_upload').checked = true;
                document.getElementById('mode_upload').dispatchEvent(new Event('change'));

                // Create a file object from blob
                const processedFile = new File([blob], "isolated_subject.png", { type: "image/png" });
                
                // Local preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Update dropzone UI to show it's now using a processed image
                    dropzone.innerHTML = `
                        <i class="bi bi-person-check-fill mb-2" style="font-size: 2.5rem; color: #ff00ff; text-shadow: 0 0 10px rgba(255,0,255,0.4);"></i>
                        <span style="font-family: 'Orbitron', sans-serif; color: #ff00ff; font-weight: 600; letter-spacing: 1px;">PROCESSED_IMAGE.PNG</span>
                        <small class="mt-2" style="color: #8888aa; font-size: 0.75rem;">Background Removed</small>
                    `;
                    dropzone.style.borderColor = '#ff00ff';

                    updateLivePreview({ image: e.target.result });
                };
                reader.readAsDataURL(processedFile);

                // Re-use chunk upload logic
                const file = processedFile;
                const fileId = Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                const CHUNK_SIZE = 256 * 1024; // 256KB
                const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
                const extension = 'png';
                const tempInput = document.getElementById('temporary_photo_path');
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
                            statusText.innerText = 'Process complete!';
                            statusText.style.color = '#39ff14';
                            submitBtn.disabled = false;
                            setTimeout(() => {
                                progressContainer.style.display = 'none';
                            }, 3000);
                        }
                    });
                }
                uploadNextChunk();

            } catch (error) {
                console.error('Background Removal Error:', error);
                window.neonAlert('Failed to remove background: ' + error.message);
                statusText.innerText = 'Error occurred.';
                statusText.style.color = 'red';
            } finally {
                btn.disabled = false;
                btnSpinner.classList.add('d-none');
                btnText.innerHTML = '<i class="bi bi-person-bounding-box"></i> REMOVE BACKGROUND';
                submitBtn.disabled = false;
            }
        });

        const photoInput = document.getElementById('photo');
        const dropzone = document.getElementById('photo-dropzone');
        
        photoInput.addEventListener('dragenter', () => {
            dropzone.style.background = 'rgba(0, 240, 255, 0.1)';
            dropzone.style.borderColor = '#00f0ff';
        });
        photoInput.addEventListener('dragleave', () => {
            dropzone.style.background = 'rgba(0, 240, 255, 0.02)';
            dropzone.style.borderColor = 'rgba(0, 240, 255, 0.4)';
        });
        photoInput.addEventListener('drop', () => {
            dropzone.style.background = 'rgba(0, 240, 255, 0.02)';
            dropzone.style.borderColor = 'rgba(0, 240, 255, 0.4)';
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
                    if (document.getElementById('mode_upload').checked) {
                        updateLivePreview({ image: e.target.result });
                    }
                };
                reader.readAsDataURL(file);

                // Chunk Upload
                const btnSubmit = document.querySelector('button[type="submit"]');
                const progressContainer = document.getElementById('upload-progress-container');
                const progressBar = document.getElementById('upload-progress-bar');
                const statusText = document.getElementById('upload-status');
                const tempInput = document.getElementById('temporary_photo_path');

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

        // AI Generation Logic
        document.getElementById('btn-preview-ai').addEventListener('click', function() {
            const prompt = document.getElementById('ai_prompt').value;
            if (!prompt) {
                window.neonAlert('Please enter an art style prompt first.');
                return;
            }

            const btn = this;
            const loading = document.getElementById('ai-loading');
            const resultContainer = document.getElementById('ai-result-container');
            const hiddenInput = document.getElementById('generated_ai_photo');

            btn.disabled = true;
            loading.style.display = 'block';
            resultContainer.style.display = 'none';

            const formData = new FormData();
            formData.append('ai_prompt', prompt);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch('{{ route("templates.ai-preview") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                loading.style.display = 'none';

                if (data.success) {
                    // Reset sliders to default
                    const defaults = {
                        image_position_x: 50,
                        image_position_y: 50,
                        image_scale: 1.0,
                        image_stretch_y: 1.0
                    };

                    Object.keys(defaults).forEach(id => {
                        const input = document.getElementById(id);
                        if (input) input.value = defaults[id];
                    });

                    hiddenInput.value = data.path;
                    hiddenInput.setAttribute('data-url', data.url);
                    resultContainer.style.display = 'block';
                    
                    updateLivePreview({ 
                        image: data.url,
                        imagePositionX: 50,
                        imagePositionY: 50,
                        imageScale: 1.0,
                        imageStretchY: 1.0
                    });
                } else {
                    window.neonAlert('Failed to generate preview: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                btn.disabled = false;
                loading.style.display = 'none';
                window.neonAlert('An error occurred during generation.');
                console.error('Error:', error);
            });
        });

        // Add live preview listeners for design inputs
        const inputs = ['card_title', 'quote', 'background_color', 'border_color', 'section_color', 'primary_text_color', 'secondary_text_color', 'image_position_x', 'image_position_y', 'image_scale', 'image_stretch_y'];
        inputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', function() {
                    let key = id;
                    if (id === 'card_title') key = 'title';
                    if (id === 'image_position_x') key = 'imagePositionX';
                    if (id === 'image_position_y') key = 'imagePositionY';
                    if (id === 'image_scale') key = 'imageScale';
                    if (id === 'image_stretch_y') key = 'imageStretchY';
                    if (id === 'background_color') key = 'backgroundColor';
                    if (id === 'border_color') key = 'borderColor';
                    if (id === 'section_color') key = 'sectionColor';
                    if (id === 'primary_text_color') key = 'primaryTextColor';
                    if (id === 'secondary_text_color') key = 'secondaryTextColor';
                    
                    let updates = {};
                    updates[key] = (id === 'image_position_x' || id === 'image_position_y') ? parseInt(this.value) : 
                                  (id === 'image_scale' || id === 'image_stretch_y') ? parseFloat(this.value) : this.value;
                    updateLivePreview(updates);
                });
            }
        });
    });
</script>
@endsection
