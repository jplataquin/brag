@extends('layouts.app')

@section('title', 'Edit Template')

@section('content')
<h1 class="page-title">
    <span class="page-title-accent"><i class="bi bi-pencil-fill"></i></span> EDIT TEMPLATE
</h1>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="neon-card p-4">
            <form method="POST" action="{{ route('templates.update', $template) }}" enctype="multipart/form-data" id="template-edit-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="image_position_x" id="hidden_image_position_x" value="{{ old('image_position_x', $template->image_position_x ?? 50) }}">
                <input type="hidden" name="image_position_y" id="hidden_image_position_y" value="{{ old('image_position_y', $template->image_position_y ?? 50) }}">
                <input type="hidden" name="image_scale" id="hidden_image_scale" value="{{ old('image_scale', $template->image_scale ?? 1.0) }}">
                <input type="hidden" name="image_stretch_y" id="hidden_image_stretch_y" value="{{ old('image_stretch_y', $template->image_stretch_y ?? 1.0) }}">

                <div class="mb-3">
                    <label for="card_title" class="form-label">CARD TITLE</label>
                    <input type="text" class="form-control @error('card_title') is-invalid @enderror"
                           id="card_title" name="card_title" value="{{ old('card_title', $template->card_title) }}"
                           maxlength="50" style="text-transform: uppercase;" required>
                    @error('card_title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">GAME TITLE</label>
                    <input type="text" class="form-control" value="{{ $template->gameTitle->title ?? 'Unknown Game' }}" disabled style="background-color: rgba(255,255,255,0.05); color: #8888aa; border-color: rgba(255,255,255,0.1);">
                    <small style="color: #555577; font-size: 0.75rem;"><i class="bi bi-lock-fill"></i> Game title is permanent and cannot be changed.</small>
                </div>

                <div class="mb-3">
                    <label for="quote" class="form-label">QUOTE</label>
                    <textarea class="form-control @error('quote') is-invalid @enderror"
                              id="quote" name="quote" rows="4"
                              maxlength="500" required>{{ old('quote', $template->quote) }}</textarea>
                    @error('quote')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <x-color-picker id="background_color" name="background_color" label="BACKGROUND COLOR" :value="$template->background_color ?? '#0a0a1a'" />
                    </div>
                    <div class="col-md-4">
                        <x-color-picker id="border_color" name="border_color" label="BORDER COLOR" :value="$template->border_color ?? '#00f0ff'" />
                    </div>
                    <div class="col-md-4">
                        <x-color-picker id="section_color" name="section_color" label="SECTION COLOR" :value="$template->section_color ?? '#111122'" />
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">CARD IMAGE SOURCE</label>
                    
                    <div class="d-flex gap-3 mb-3">
                        <div class="flex-fill">
                            <input type="radio" class="btn-check" name="image_mode" id="mode_upload" value="upload" {{ old('image_mode', $template->ai_photo ? 'ai' : 'upload') == 'upload' ? 'checked' : '' }} autocomplete="off">
                            <label class="btn btn-outline-neon w-100" for="mode_upload">
                                <i class="bi bi-cloud-upload"></i> UPLOAD PHOTO
                            </label>
                        </div>
                        <div class="flex-fill">
                            <input type="radio" class="btn-check" name="image_mode" id="mode_ai" value="ai" {{ old('image_mode', $template->ai_photo ? 'ai' : 'upload') == 'ai' ? 'checked' : '' }} autocomplete="off">
                            <label class="btn btn-outline-neon-magenta w-100" for="mode_ai">
                                <i class="bi bi-magic"></i> AI GENERATE
                            </label>
                        </div>
                    </div>

                    <!-- Upload Section -->
                    <div id="section_upload" style="display: {{ old('image_mode', $template->ai_photo ? 'ai' : 'upload') == 'upload' ? 'block' : 'none' }};">
                        @if($template->photo && !$template->ai_photo)
                            <div class="mb-3">
                                <label class="d-block mb-2" style="font-size: 0.75rem; color: #8888aa;">CURRENT PHOTO</label>
                                <img src="{{ asset('storage/' . $template->photo) }}" alt="Current" style="max-width: 150px; border-radius: 12px; border: 1px solid rgba(0,240,255,0.2);">
                            </div>
                        @endif
                        <div class="position-relative" id="photo-upload-wrapper">
                            <input type="file" class="position-absolute w-100 h-100 opacity-0"
                                   style="z-index: 2; cursor: pointer; top: 0; left: 0;"
                                   id="photo" accept="image/*">
                            <div id="photo-dropzone" class="d-flex flex-column align-items-center justify-content-center p-4 text-center neon-card @error('temporary_photo_path') border-danger @enderror" style="border: 2px dashed rgba(0, 240, 255, 0.4); background: rgba(0, 240, 255, 0.02); transition: all 0.3s ease;">
                                <i class="bi bi-cloud-arrow-up-fill mb-2" style="font-size: 2.5rem; color: #00f0ff; text-shadow: 0 0 10px rgba(0,240,255,0.4);"></i>
                                <span style="font-family: 'Orbitron', sans-serif; color: #00f0ff; font-weight: 600; letter-spacing: 1px;">CLICK OR DRAG TO REPLACE PHOTO</span>
                                <small class="mt-2" style="color: #8888aa; font-size: 0.75rem;">Supports JPEG, PNG, GIF, WebP</small>
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
                            <small id="upload-status" style="color: #00f0ff; font-size: 0.75rem;">Uploading: 0%</small>
                        </div>

                        <!-- Photo Preview -->
                        <div id="photo-preview" class="mt-3" style="display: {{ old('temporary_photo_path') ? 'block' : 'none' }};">
                            <img id="preview-img" src="{{ old('temporary_photo_path') ? asset('storage/' . old('temporary_photo_path')) : '' }}" alt="Preview" style="max-width: 200px; border-radius: 12px; border: 2px solid rgba(0,240,255,0.2);">
                        </div>
                    </div>

                    <!-- AI Section -->
                    <div id="section_ai" class="neon-card p-3" style="display: {{ old('image_mode', $template->ai_photo ? 'ai' : 'upload') == 'ai' ? 'block' : 'none' }}; border-color: #ff00ff; background: rgba(255,0,255,0.05);">
                        @if($template->ai_photo)
                            <div class="mb-3">
                                <label class="d-block mb-2" style="font-size: 0.75rem; color: #ff00ff;">CURRENT AI IMAGE</label>
                                <img src="{{ asset('storage/' . $template->ai_photo) }}" alt="Current AI" style="max-width: 150px; border-radius: 12px; border: 2px solid #ff00ff; box-shadow: 0 0 10px rgba(255,0,255,0.4);">
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="ai_art_style" class="form-label" style="font-size: 0.85rem; color: #bbbbd0;">ART STYLE</label>
                            <select name="ai_art_style" id="ai_art_style" class="form-select neon-input" style="background-color: #1a0a1a; color: #fff; border-color: rgba(255, 0, 255, 0.3);">
                                <option value="neon" {{ old('ai_art_style') == 'neon' ? 'selected' : '' }}>Vibrant Neon Game Character</option>
                                <option value="anime" {{ old('ai_art_style') == 'anime' ? 'selected' : '' }}>Anime & Manga</option>
                                <option value="fantasy" {{ old('ai_art_style') == 'fantasy' ? 'selected' : '' }}>Game Fantasy</option>
                                <option value="raw" {{ old('ai_art_style') == 'raw' ? 'selected' : '' }}>Raw Prompt</option>
                            </select>
                        </div>

                        <label for="ai_prompt" class="form-label" style="font-size: 0.85rem; color: #bbbbd0;">PROMPT</label>
                        <textarea class="form-control @error('ai_prompt') is-invalid @enderror"
                               id="ai_prompt" name="ai_prompt" rows="2"
                               placeholder="e.g. Cyberpunk hacker, Neon glowing eyes, Fantasy warrior...">{{ old('ai_prompt') }}</textarea>
                        <small style="color: #555577; font-size: 0.75rem;">Describe the character or art style you want. Pollinations AI will generate it from scratch.</small>
                        @error('ai_prompt')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <input type="hidden" name="generated_ai_photo" id="generated_ai_photo" value="{{ old('generated_ai_photo') }}">
                        
                        <div class="mt-3">
                            <button type="button" class="btn btn-neon-magenta btn-sm" id="btn-preview-ai">
                                <i class="bi bi-magic"></i> Generate AI Image
                            </button>
                            <span id="ai-loading" style="display: none; color: #00f0ff; margin-left: 10px; font-size: 0.85rem;">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating...
                            </span>
                        </div>

                        <div id="ai-result-container" class="mt-3" style="display: {{ old('generated_ai_photo') ? 'block' : 'none' }};">
                            <label style="font-size: 0.75rem; color: #ff00ff; display: block; margin-bottom: 5px;">AI PREVIEW</label>
                            <img id="ai-preview-img" src="{{ old('generated_ai_photo') ? asset('storage/' . old('generated_ai_photo')) : '' }}" alt="AI Preview" style="max-width: 200px; border-radius: 12px; border: 2px solid #ff00ff; box-shadow: 0 0 10px rgba(255,0,255,0.4);">
                        </div>

                        <!-- AI History Gallery -->
                        <div id="ai-history-container" class="mt-3" style="display: {{ old('generated_ai_photo') || $template->ai_photo ? 'block' : 'none' }};">
                            <label style="font-size: 0.75rem; color: #8888aa; display: block; margin-bottom: 5px; font-family: 'Orbitron', sans-serif;">RECENT GENERATIONS</label>
                            <div id="ai-history-gallery" class="d-flex gap-2 overflow-auto py-2 custom-scrollbar" style="white-space: nowrap; min-height: 100px;">
                                @if($template->ai_photo)
                                    <img src="{{ asset('storage/' . $template->ai_photo) }}" 
                                         class="history-thumb active" 
                                         data-path="{{ $template->ai_photo }}"
                                         onclick="selectHistoryImage(this)">
                                @endif
                                @if(old('generated_ai_photo') && old('generated_ai_photo') !== $template->ai_photo)
                                    <img src="{{ asset('storage/' . old('generated_ai_photo')) }}" 
                                         class="history-thumb" 
                                         data-path="{{ old('generated_ai_photo') }}"
                                         onclick="selectHistoryImage(this)">
                                @endif
                            </div>
                        </div>

                        @error('generated_ai_photo')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Background Removal Feature -->
                    <div id="bg-removal-container" class="mt-4 pt-3 border-top border-secondary">
                        <button type="button" id="btn-remove-bg" class="btn btn-outline-neon-magenta w-100 mb-2">
                            <i class="bi bi-person-bounding-box"></i> REMOVE BACKGROUND
                        </button>
                        <div id="bg-removal-progress-container" style="display: none;">
                            <div class="progress" style="height: 8px; background-color: #111122; border-radius: 4px; overflow: hidden;">
                                <div id="bg-removal-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%; background-color: #ff00ff;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small id="bg-removal-status" style="color: #ff00ff; font-size: 0.7rem; font-family: 'Orbitron', sans-serif;">Initializing background removal...</small>
                        </div>
                        <small class="text-center d-block" style="color: #555577; font-size: 0.7rem;">
                            Runs locally in your browser. Powered by @imgly.
                        </small>
                    </div>
                </div>

            </form>
        </div>
    </div>
    
    <!-- Live Preview Column -->
    <div class="col-lg-5 mt-4 mt-lg-0">
        <div class="neon-card p-4 position-sticky" style="top: 100px;">
            <h5 class="text-center mb-3" style="color: #00f0ff; font-family: 'Orbitron', sans-serif; letter-spacing: 2px;">LIVE PREVIEW</h5>
            
            <div class="mb-4">
                <label for="preview_rank_level" class="form-label" style="font-size: 0.75rem; color: #bbbbd0;">TEST RANK BADGE (PREVIEW ONLY)</label>
                <select id="preview_rank_level" class="form-select form-select-sm" style="background-color: #0a0a1a; color: #fff; border-color: rgba(0,240,255,0.3);">
                    <option value="1">Level 1 - CASUAL</option>
                    <option value="2">Level 2 - COMPETITIVE</option>
                    <option value="3">Level 3 - ELITE</option>
                    <option value="4">Level 4 - LEGENDARY</option>
                    <option value="5">Level 5 - GOAT</option>
                </select>
            </div>

            <div class="d-flex justify-content-center">
                <x-digital-card 
                    id="live_preview_card" 
                    mode="display"
                    :fullscreen="true"
                    :width="250"
                    :height="350"
                    :title="$template->card_title" 
                    :game="$template->gameTitle->title ?? 'GAME'" 
                    :creator="$template->user->username ?? 'Creator'"
                    :quote="$template->quote"
                    :backgroundColor="$template->background_color"
                    :borderColor="$template->border_color"
                    :sectionColor="$template->section_color"
                    :primaryTextColor="$template->primary_text_color"
                    :secondaryTextColor="$template->secondary_text_color"
                    :image="$template->display_photo"
                    :imagePositionX="$template->image_position_x ?? 50"
                    :imagePositionY="$template->image_position_y ?? 50"
                    :imageScale="$template->image_scale ?? 1.0"
                    :imageStretchY="$template->image_stretch_y ?? 1.0"
                    :year="$template->created_at->format('Y')"
                />
            </div>
            <p class="text-center mt-3 mb-4" style="color: #555577; font-size: 0.8rem;">
                This is a preview of how digital cards forged from this template will look.
            </p>

            <div class="mb-4">
                <label for="image_position_x" class="form-label" style="font-size: 0.75rem; color: #bbbbd0;">IMAGE HORIZONTAL POSITION (X-AXIS CROP)</label>
                <input type="range" class="form-range" id="image_position_x" form="template-edit-form" min="0" max="100" value="{{ old('image_position_x', $template->image_position_x ?? 50) }}">
                <div class="d-flex justify-content-between mb-3">
                    <small style="color: #8888aa; font-size: 0.75rem;">Left</small>
                    <small style="color: #8888aa; font-size: 0.75rem;">Center</small>
                    <small style="color: #8888aa; font-size: 0.75rem;">Right</small>
                </div>

                <label for="image_position_y" class="form-label" style="font-size: 0.75rem; color: #bbbbd0;">IMAGE VERTICAL POSITION (Y-AXIS CROP)</label>
                <input type="range" class="form-range" id="image_position_y" form="template-edit-form" min="0" max="100" value="{{ old('image_position_y', $template->image_position_y ?? 50) }}">
                <div class="d-flex justify-content-between mb-3">
                    <small style="color: #8888aa; font-size: 0.75rem;">Top</small>
                    <small style="color: #8888aa; font-size: 0.75rem;">Center</small>
                    <small style="color: #8888aa; font-size: 0.75rem;">Bottom</small>
                </div>

                <label for="image_scale" class="form-label" style="font-size: 0.75rem; color: #bbbbd0;">IMAGE ZOOM (SCALE)</label>
                <input type="range" class="form-range" id="image_scale" form="template-edit-form" min="1.0" max="3" step="0.01" value="{{ old('image_scale', $template->image_scale ?? 1.0) }}">
                <div class="d-flex justify-content-between mb-3">
                    <small style="color: #555577;">1.0x (Standard)</small>
                    <small style="color: #555577;">3.0x (Zoom)</small>
                </div>

                <label for="image_stretch_y" class="form-label" style="font-size: 0.75rem; color: #bbbbd0;">IMAGE VERTICAL STRETCH</label>
                <input type="range" class="form-range" id="image_stretch_y" form="template-edit-form" min="0.5" max="2.0" step="0.01" value="{{ old('image_stretch_y', $template->image_stretch_y ?? 1.0) }}">
                <div class="d-flex justify-content-between">
                    <small style="color: #555577;">0.5x (Squash)</small>
                    <small style="color: #555577;">1.0x (Normal)</small>
                    <small style="color: #555577;">2.0x (Stretch)</small>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-center">
                <button type="submit" form="template-edit-form" class="btn btn-neon" id="btn-update-template">
                    <i class="bi bi-check-lg"></i> UPDATE
                </button>
                <a href="{{ route('templates.show', $template) }}" class="btn btn-neon-danger">Cancel</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .ts-wrapper .ts-control {
        background-color: #0a0a1a !important;
        border: 1px solid rgba(0, 240, 255, 0.3) !important;
        color: #fff !important;
        border-radius: 8px;
    }
    .ts-wrapper.is-invalid .ts-control {
        border-color: #ff4444 !important;
    }
    .ts-wrapper .ts-control input {
        color: #fff !important;
    }
    .ts-dropdown {
        background-color: #111122 !important;
        border: 1px solid rgba(0, 240, 255, 0.3) !important;
        color: #fff !important;
    }
    .ts-dropdown .option {
        color: #bbbbd0;
    }
    .ts-dropdown .option:hover, .ts-dropdown .active {
        background-color: rgba(0, 240, 255, 0.1) !important;
        color: #00f0ff !important;
    }

    /* History Gallery Styles */
    .history-thumb {
        cursor: pointer;
        border: 2px solid transparent;
        border-radius: 8px;
        width: 65px;
        height: 91px;
        object-fit: cover;
        opacity: 0.5;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }
    .history-thumb:hover {
        opacity: 0.8;
        transform: scale(1.05);
    }
    .history-thumb.active {
        border-color: #00f0ff;
        opacity: 1;
        box-shadow: 0 0 10px rgba(0, 240, 255, 0.6);
        transform: scale(1.1);
    }
    .custom-scrollbar::-webkit-scrollbar {
        height: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(0, 240, 255, 0.3);
        border-radius: 2px;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    function updateLivePreview(updates) {
        if(window.updateDigitalCard_live_preview_card) {
            window.updateDigitalCard_live_preview_card(updates);
        }
    }

    // Reusable chunk upload function
    function uploadFileInChunks(file, options = {}) {
        const btnSubmit = document.getElementById('btn-update-template');
        const progressContainer = options.progressContainer || document.getElementById('upload-progress-container');
        const progressBar = options.progressBar || document.getElementById('upload-progress-bar');
        const statusText = options.statusText || document.getElementById('upload-status');
        const tempInput = document.getElementById('temporary_photo_path');

        btnSubmit.disabled = true;
        progressContainer.style.display = 'block';
        progressBar.style.width = '0%';
        statusText.innerText = 'Uploading: 0%';
        statusText.style.color = options.statusColor || '#00f0ff';

        const CHUNK_SIZE = 256 * 1024; // 256KB
        const fileId = Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
        const extension = options.extension || (file.name ? file.name.split('.').pop() : 'png');
        let chunkIndex = 0;

        return new Promise((resolve, reject) => {
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
                        reject(data.error);
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
                        resolve(data.path);
                    }
                })
                .catch(err => {
                    console.error('Upload Error:', err);
                    statusText.innerText = 'Upload error!';
                    statusText.style.color = 'red';
                    btnSubmit.disabled = false;
                    reject(err);
                });
            }
            uploadNextChunk();
        });
    }

    // Clear validation errors on input for all standard fields
    document.querySelectorAll('.form-control').forEach(function(input) {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            const feedback = this.parentNode.querySelector('.invalid-feedback');
            if (feedback) feedback.style.display = 'none';
        });
    });

    document.getElementById('card_title').addEventListener('input', function() {
        const uppercaseValue = this.value.toUpperCase();
        if (this.value !== uppercaseValue) {
            // Only update value if necessary to prevent cursor jumping
            const start = this.selectionStart;
            const end = this.selectionEnd;
            this.value = uppercaseValue;
            this.setSelectionRange(start, end);
        }
        updateLivePreview({ title: uppercaseValue || 'CARD TITLE' });
    });

    document.getElementById('preview_rank_level').addEventListener('change', function() {
        updateLivePreview({ rankLevel: parseInt(this.value) });
    });

    document.getElementById('quote').addEventListener('input', function() {
        updateLivePreview({ quote: this.value || 'Card quote goes here...' });
    });

    ['background_color', 'border_color', 'section_color', 'primary_text_color', 'secondary_text_color'].forEach(function(field) {
        const hexInput = document.getElementById(field + '_hex');
        if (hexInput) {
            hexInput.addEventListener('input', function() {
                const opt = {};
                if(field === 'background_color') opt.backgroundColor = this.value;
                if(field === 'border_color') opt.borderColor = this.value;
                if(field === 'section_color') opt.sectionColor = this.value;
                if(field === 'primary_text_color') opt.primaryTextColor = this.value;
                if(field === 'secondary_text_color') opt.secondaryTextColor = this.value;
                updateLivePreview(opt);
            });
        }
    });

    document.getElementById('image_position_x').addEventListener('input', function() {
        updateLivePreview({ imagePositionX: parseInt(this.value) });
        document.getElementById('hidden_image_position_x').value = this.value;
    });

    document.getElementById('image_position_y').addEventListener('input', function() {
        updateLivePreview({ imagePositionY: parseInt(this.value) });
        document.getElementById('hidden_image_position_y').value = this.value;
    });

    document.getElementById('image_scale').addEventListener('input', function() {
        updateLivePreview({ imageScale: parseFloat(this.value) });
        document.getElementById('hidden_image_scale').value = this.value;
    });

    document.getElementById('image_stretch_y').addEventListener('input', function() {
        updateLivePreview({ imageStretchY: parseFloat(this.value) });
        document.getElementById('hidden_image_stretch_y').value = this.value;
    });

    const photoInput = document.getElementById('photo');
    const dropzone = document.getElementById('photo-dropzone');
    
    if (photoInput && dropzone) {
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

        photoInput.addEventListener('change', async function(e) {
            const file = e.target.files[0] || this.files[0];
            if (file) {
                dropzone.innerHTML = `
                    <i class="bi bi-file-earmark-image-fill mb-2" style="font-size: 2.5rem; color: #39ff14; text-shadow: 0 0 10px rgba(57,255,20,0.4);"></i>
                    <span style="font-family: 'Orbitron', sans-serif; color: #39ff14; font-weight: 600; letter-spacing: 1px;">${file.name}</span>
                    <small class="mt-2" style="color: #8888aa; font-size: 0.75rem;">Click or drag to change</small>
                `;
                dropzone.style.borderColor = '#39ff14';

                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-img').src = e.target.result;
                    document.getElementById('photo-preview').style.display = 'block';
                    if (document.getElementById('mode_upload').checked) {
                        updateLivePreview({ image: e.target.result });
                    }
                };
                reader.readAsDataURL(file);

                // Chunk Upload
                try {
                    await uploadFileInChunks(file);
                } catch (err) {
                    console.error('Initial upload failed:', err);
                }
            }
        });
    }

    // Background Removal Logic
    document.getElementById('btn-remove-bg').addEventListener('click', async function() {
        const renderer = window.digitalCardRenderers ? window.digitalCardRenderers['live_preview_card'] : null;
        if (!renderer || !renderer.currentOptions || !renderer.currentOptions.image) {
            window.neonAlert('No image found in preview to process.');
            return;
        }

        const btn = this;
        const progressContainer = document.getElementById('bg-removal-progress-container');
        const progressBar = document.getElementById('bg-removal-progress-bar');
        const statusText = document.getElementById('bg-removal-status');
        const submitBtn = document.getElementById('btn-update-template');

        try {
            btn.disabled = true;
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
            
            // Update local preview
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('photo-preview').style.display = 'block';
                
                // Update dropzone UI to show it's now using a processed image
                if (dropzone) {
                    dropzone.innerHTML = `
                        <i class="bi bi-person-check-fill mb-2" style="font-size: 2.5rem; color: #ff00ff; text-shadow: 0 0 10px rgba(255,0,255,0.4);"></i>
                        <span style="font-family: 'Orbitron', sans-serif; color: #ff00ff; font-weight: 600; letter-spacing: 1px;">PROCESSED_IMAGE.PNG</span>
                        <small class="mt-2" style="color: #8888aa; font-size: 0.75rem;">Background Removed</small>
                    `;
                    dropzone.style.borderColor = '#ff00ff';
                }

                updateLivePreview({ image: e.target.result });
            };
            reader.readAsDataURL(processedFile);

            // Upload via chunks
            await uploadFileInChunks(processedFile, {
                progressContainer: progressContainer,
                progressBar: progressBar,
                statusText: statusText,
                statusColor: '#ff00ff',
                extension: 'png'
            });

            statusText.innerText = 'Process complete!';
            statusText.style.color = '#39ff14';
            setTimeout(() => {
                progressContainer.style.display = 'none';
            }, 3000);

        } catch (error) {
            console.error('Background Removal Error:', error);
            window.neonAlert('Failed to remove background: ' + error.message);
            statusText.innerText = 'Error occurred.';
            statusText.style.color = 'red';
        } finally {
            btn.disabled = false;
            submitBtn.disabled = false;
        }
    });

    // Image Mode Toggle Logic
    document.querySelectorAll('input[name="image_mode"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'upload') {
                document.getElementById('section_upload').style.display = 'block';
                document.getElementById('section_ai').style.display = 'none';
                
                // Update live preview
                const uploadPreview = document.getElementById('preview-img').src;
                if (uploadPreview && !uploadPreview.endsWith('/edit')) {
                    updateLivePreview({ image: uploadPreview });
                } else {
                    // Fallback to original template photo
                    updateLivePreview({ image: "{{ asset('storage/' . $template->photo) }}" });
                }
            } else {
                document.getElementById('section_upload').style.display = 'none';
                document.getElementById('section_ai').style.display = 'block';
                
                // Update live preview
                const aiPreview = document.getElementById('ai-preview-img').src;
                if (aiPreview && !aiPreview.endsWith('/edit')) {
                    updateLivePreview({ image: aiPreview });
                } else if ("{{ $template->ai_photo }}") {
                    updateLivePreview({ image: "{{ asset('storage/' . $template->ai_photo) }}" });
                } else {
                    updateLivePreview({ image: '' });
                }
            }
        });
    });

    document.getElementById('btn-preview-ai').addEventListener('click', function() {
        const prompt = document.getElementById('ai_prompt').value;
        const style = document.getElementById('ai_art_style').value;
        if (!prompt) {
            window.neonAlert('Please enter an art style prompt first.');
            return;
        }

        const btn = this;
        const loading = document.getElementById('ai-loading');
        const resultContainer = document.getElementById('ai-result-container');
        const img = document.getElementById('ai-preview-img');
        const hiddenInput = document.getElementById('generated_ai_photo');

        btn.disabled = true;
        loading.style.display = 'inline-block';
        resultContainer.style.display = 'none';

        const formData = new FormData();
        formData.append('ai_prompt', prompt);
        formData.append('ai_art_style', style);
        // Note: We do NOT send temporary_photo_path here because the user wants purely text-to-image
        
        // Add CSRF token
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
                // Reset sliders to default before applying new image
                const defaults = {
                    image_position_x: 50,
                    image_position_y: 50,
                    image_scale: 1.0,
                    image_stretch_y: 1.0
                };

                Object.keys(defaults).forEach(id => {
                    const slider = document.getElementById(id);
                    const hidden = document.getElementById('hidden_' + id);
                    if (slider) slider.value = defaults[id];
                    if (hidden) hidden.value = defaults[id];
                });

                img.src = data.url;
                hiddenInput.value = data.path;
                resultContainer.style.display = 'block';
                document.getElementById('ai-history-container').style.display = 'block';
                
                // Force preview update with defaults and new image
                updateLivePreview({ 
                    image: data.url,
                    imagePositionX: 50,
                    imagePositionY: 50,
                    imageScale: 1.0,
                    imageStretchY: 1.0
                });

                // Add to history
                const gallery = document.getElementById('ai-history-gallery');
                const newThumb = document.createElement('img');
                newThumb.src = data.url;
                newThumb.className = 'history-thumb';
                newThumb.setAttribute('data-path', data.path);
                newThumb.onclick = function() { selectHistoryImage(this); };
                gallery.prepend(newThumb);
                selectHistoryImage(newThumb);
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

    function selectHistoryImage(el) {
        // Remove active class from all
        document.querySelectorAll('.history-thumb').forEach(thumb => thumb.classList.remove('active'));
        // Add to current
        el.classList.add('active');
        
        // Update main preview and input
        document.getElementById('ai-preview-img').src = el.src;
        document.getElementById('generated_ai_photo').value = el.getAttribute('data-path');
        
        // Update live card
        updateLivePreview({ image: el.src });
    }
    document.getElementById('template-edit-form').addEventListener('submit', function(e) {
        if (this.checkValidity()) {
            const btn = document.getElementById('btn-update-template');
            setTimeout(() => {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> UPDATING...';
            }, 10);
        }
    });
</script>
@endsection
