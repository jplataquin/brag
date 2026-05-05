@extends('layouts.app')

@section('title', 'Create Template')

@php
    $selectedGameTitle = 'GAME';
    if (old('game_title_id')) {
        $selectedGame = $gameTitles->firstWhere('id', old('game_title_id'));
        $selectedGameTitle = $selectedGame ? $selectedGame->title : 'GAME';
    }
    
    $initialPreviewImage = '';
    if (old('image_mode') == 'ai' && old('generated_ai_photo')) {
        $initialPreviewImage = asset('storage/' . old('generated_ai_photo'));
    } elseif (old('image_mode', 'upload') == 'upload' && old('temporary_photo_path')) {
        $initialPreviewImage = asset('storage/' . old('temporary_photo_path'));
    }

    $baseCost = config('diamonds.costs.template_creation');
    $forgeCost = config('diamonds.costs.forging');
    $isAutoForgeDefault = old('auto_forge', '1') == '1';
    $initialTotalCost = $isAutoForgeDefault ? ($baseCost + $forgeCost) : $baseCost;
@endphp

@section('content')
<h1 class="page-title">
    <span class="page-title-accent"><i class="bi bi-plus-circle-fill"></i></span> NEW TEMPLATE
</h1>

<div class="row justify-content-center">
    <div class="col-lg-7">
        @if(Auth::user()->diamonds_balance < $baseCost)
            <div class="alert alert-danger mb-4" style="background: rgba(255, 0, 0, 0.1); border: 1px solid #ff0000; color: #ff0000;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>INSUFFICIENT DIAMONDS:</strong> You need at least {{ $baseCost }} Diamonds to create a template. Please acquire more Diamonds before proceeding.
            </div>
        @endif
        
        <div class="neon-card p-4">
            <form method="POST" action="{{ route('templates.store') }}" enctype="multipart/form-data" id="template-form">
                @csrf
                <input type="hidden" name="image_position_y" id="hidden_image_position_y" value="{{ old('image_position_y', 50) }}">

                <div class="mb-3">
                    <label for="card_title" class="form-label">CARD TITLE</label>
                    <input type="text" class="form-control @error('card_title') is-invalid @enderror"
                           id="card_title" name="card_title" value="{{ old('card_title') }}"
                           placeholder="e.g. SHADOW STRIKER" maxlength="50" style="text-transform: uppercase;" required>
                    @error('card_title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="game_title_id" class="form-label">GAME TITLE</label>
                    <select class="form-select @error('game_title_id') is-invalid @enderror"
                           id="game_title_id" name="game_title_id" required>
                        <option value="">Select a Game...</option>
                        @foreach($gameTitles as $game)
                            @php
                                $count = $gameTemplateCounts[$game->id] ?? 0;
                                $isFull = $count >= 3;
                            @endphp
                            <option value="{{ $game->id }}" {{ old('game_title_id') == $game->id ? 'selected' : '' }} {{ $isFull ? 'disabled' : '' }}>
                                {{ $game->title }} {{ $isFull ? '(LIMIT REACHED)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <small style="color: #555577; font-size: 0.75rem;">Rule of 3: You can only have a maximum of 3 templates per game title.</small>
                    @error('game_title_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="quote" class="form-label">QUOTE</label>
                    <textarea class="form-control @error('quote') is-invalid @enderror"
                              id="quote" name="quote" rows="4"
                              placeholder="Add a quote for your card..." maxlength="500" required>{{ old('quote') }}</textarea>
                    @error('quote')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <x-color-picker id="background_color" name="background_color" label="BACKGROUND COLOR" value="#0a0a1a" />
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <x-color-picker id="border_color" name="border_color" label="BORDER COLOR" value="#00f0ff" />
                    </div>
                    <div class="col-md-4">
                        <x-color-picker id="section_color" name="section_color" label="SECTION COLOR" value="#111122" />
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <x-color-picker id="primary_text_color" name="primary_text_color" label="PRIMARY TEXT COLOR" value="#ffffff" />
                    </div>
                    <div class="col-md-8">
                        <x-color-picker id="secondary_text_color" name="secondary_text_color" label="SECONDARY TEXT COLOR" value="#dddddd" />
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">CARD IMAGE SOURCE</label>
                    
                    <div class="d-flex gap-3 mb-3">
                        <div class="flex-fill">
                            <input type="radio" class="btn-check" name="image_mode" id="mode_upload" value="upload" {{ old('image_mode', 'upload') == 'upload' ? 'checked' : '' }} autocomplete="off">
                            <label class="btn btn-outline-neon w-100" for="mode_upload">
                                <i class="bi bi-cloud-upload"></i> UPLOAD PHOTO
                            </label>
                        </div>
                        <div class="flex-fill">
                            <input type="radio" class="btn-check" name="image_mode" id="mode_ai" value="ai" {{ old('image_mode') == 'ai' ? 'checked' : '' }} autocomplete="off">
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
                    <div id="section_ai" class="neon-card p-3" style="display: {{ old('image_mode') == 'ai' ? 'block' : 'none' }}; border-color: #ff00ff; background: rgba(255,0,255,0.05);">
                        <label for="ai_prompt" class="form-label" style="font-size: 0.85rem; color: #bbbbd0;">ART STYLE PROMPT</label>
                        <textarea class="form-control @error('ai_prompt') is-invalid @enderror"
                               id="ai_prompt" name="ai_prompt" rows="2"
                               placeholder="e.g. Cyberpunk hacker, Neon glowing eyes, Fantasy warrior...">{{ old('ai_prompt') }}</textarea>
                        <small style="color: #555577; font-size: 0.75rem;">Describe the character or art style you want. Nano Banana will generate it from scratch.</small>
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
                            <img id="ai-preview-img" src="{{ $initialPreviewImage }}" alt="AI Preview" style="max-width: 200px; border-radius: 12px; border: 2px solid #ff00ff; box-shadow: 0 0 10px rgba(255,0,255,0.4);">
                        </div>
                        @error('generated_ai_photo')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
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
                <x-digital-card id="live_preview_card" 
                               mode="display"
                               :fullscreen="true"
                               :width="250"
                               :height="350"
                               :title="old('card_title', 'CARD TITLE')"
                               :game="$selectedGameTitle"
                               :creator="Auth::user()->username"
                               :quote="old('quote', 'Card quote goes here...')"
                               :image="$initialPreviewImage"
                               :backgroundColor="old('background_color', '#0a0a1a')"
                               :borderColor="old('border_color', '#00f0ff')"
                               :sectionColor="old('section_color', '#111122')"
                               :primaryTextColor="old('primary_text_color', '#ffffff')"
                               :secondaryTextColor="old('secondary_text_color', '#dddddd')"
                               :imagePositionY="old('image_position_y', 50)" />
            </div>
            <p class="text-center mt-3 mb-4" style="color: #555577; font-size: 0.8rem;">
                This is a preview of how digital cards forged from this template will look.
            </p>

            <div class="mb-4">
                <label for="image_position_y" class="form-label" style="font-size: 0.75rem; color: #bbbbd0;">IMAGE VERTICAL POSITION (Y-AXIS CROP)</label>
                <input type="range" class="form-range" id="image_position_y" form="template-form" min="0" max="100" value="{{ old('image_position_y', 50) }}">
                <div class="d-flex justify-content-between">
                    <small style="color: #8888aa; font-size: 0.75rem;">Top</small>
                    <small style="color: #8888aa; font-size: 0.75rem;">Center</small>
                    <small style="color: #8888aa; font-size: 0.75rem;">Bottom</small>
                </div>
            </div>

            <p class="text-center mt-3 mb-1" style="color: #00f0ff; font-size: 0.85rem;">
                <i class="bi bi-gem"></i> Cost: <span id="display-total-cost">{{ $initialTotalCost }}</span> Diamonds
            </p>

            <div class="mb-3 d-flex justify-content-center">
                <div class="form-check form-switch neon-switch">
                    <input class="form-check-input" type="checkbox" name="auto_forge" id="auto_forge" value="1" {{ $isAutoForgeDefault ? 'checked' : '' }} form="template-form">
                    <label class="form-check-label small ms-2" for="auto_forge" style="color: #bbbbd0; cursor: pointer;">
                        Auto-forge first card (+{{ $forgeCost }} <i class="bi bi-gem"></i>)
                    </label>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-center">
                @if(Auth::user()->diamonds_balance < $baseCost)
                    <button type="button" class="btn btn-secondary" disabled>
                        <i class="bi bi-x-circle"></i> INSUFFICIENT DIAMONDS
                    </button>
                @else
                    <button type="submit" form="template-form" class="btn btn-neon" id="btn-submit-template" data-confirm="Create a new template {{ $isAutoForgeDefault ? 'and forge your first card ' : '' }}for {{ $initialTotalCost }} Diamonds?">
                        <i class="bi bi-check-lg"></i> CREATE TEMPLATE
                    </button>
                @endif
                <a href="{{ route('templates.index') }}" class="btn btn-neon-danger">Cancel</a>
            </div>
        </div>
    </div>
</div>

<style>
    .neon-switch .form-check-input {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: rgba(0, 240, 255, 0.3);
        cursor: pointer;
    }
    .neon-switch .form-check-input:checked {
        background-color: #00f0ff;
        border-color: #00f0ff;
        box-shadow: 0 0 10px rgba(0, 240, 255, 0.5);
    }
    .neon-switch .form-check-input:focus {
        box-shadow: 0 0 10px rgba(0, 240, 255, 0.5);
        border-color: #00f0ff;
    }
</style>
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
</style>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    function updateLivePreview(updates) {
        if(window.updateDigitalCard_live_preview_card) {
            window.updateDigitalCard_live_preview_card(updates);
        }
    }
const ts = new TomSelect("#game_title_id",{
    create: false,
    sortField: {
        field: "text",
        direction: "asc"
    },
    onChange: function(value) {
        // Clear validation error on change
        const selectEl = this.control.closest('.ts-wrapper');
        if (selectEl) {
            selectEl.classList.remove('is-invalid');
            const feedback = selectEl.parentNode.querySelector('.invalid-feedback');
            if (feedback) feedback.style.display = 'none';
        }

        const opt = this.options[value];
        updateLivePreview({ game: opt ? opt.text.trim() : 'GAME' });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const autoForgeCheckbox = document.getElementById('auto_forge');
    const displayCost = document.getElementById('display-total-cost');
    const submitBtn = document.getElementById('btn-submit-template');
    const userBalance = {{ Auth::user()->diamonds_balance }};
    const baseCost = {{ config('diamonds.costs.template_creation') }};
    const forgeCost = {{ config('diamonds.costs.forging') }};

    function updateUI() {
        const isChecked = autoForgeCheckbox.checked;
        const total = isChecked ? (baseCost + forgeCost) : baseCost;

        displayCost.innerText = total;

        if (submitBtn) {
            if (userBalance < total) {
                submitBtn.disabled = true;
                submitBtn.classList.replace('btn-neon', 'btn-secondary');
                submitBtn.innerHTML = '<i class="bi bi-x-circle"></i> INSUFFICIENT DIAMONDS';
                submitBtn.removeAttribute('data-confirm');
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.replace('btn-secondary', 'btn-neon');
                submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> CREATE TEMPLATE';
                submitBtn.setAttribute('data-confirm', `Create a new template ${isChecked ? 'and forge your first card ' : ''}for ${total} Diamonds?`);
            }
        }
    }

    if (autoForgeCheckbox) {
        autoForgeCheckbox.addEventListener('change', updateUI);
        updateUI(); // Initial check
    }
});

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

    document.getElementById('image_position_y').addEventListener('input', function() {
        updateLivePreview({ imagePositionY: parseInt(this.value) });
        document.getElementById('hidden_image_position_y').value = this.value;
    });

    const photoInput = document.getElementById('photo');
    const dropzone = document.getElementById('photo-dropzone');

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
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('photo-preview').style.display = 'block';
                if (document.getElementById('mode_upload').checked) {
                    updateLivePreview({ image: e.target.result });
                }
            };
            reader.readAsDataURL(file);

            // Chunk Upload
            const btnSubmit = document.getElementById('btn-submit-template');
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

    // Image Mode Toggle Logic
    document.querySelectorAll('input[name="image_mode"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'upload') {
                document.getElementById('section_upload').style.display = 'block';
                document.getElementById('section_ai').style.display = 'none';
                
                // Update live preview with uploaded image
                const uploadPreview = document.getElementById('preview-img').src;
                if (uploadPreview && !uploadPreview.endsWith('/create')) {
                    updateLivePreview({ image: uploadPreview });
                } else {
                    updateLivePreview({ image: '' });
                }
            } else {
                document.getElementById('section_upload').style.display = 'none';
                document.getElementById('section_ai').style.display = 'block';
                
                // Update live preview with AI image
                const aiPreview = document.getElementById('ai-preview-img').src;
                if (aiPreview && !aiPreview.endsWith('/create')) {
                    updateLivePreview({ image: aiPreview });
                } else {
                    updateLivePreview({ image: '' });
                }
            }
        });
    });

    document.getElementById('btn-preview-ai').addEventListener('click', function() {
        const prompt = document.getElementById('ai_prompt').value;
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
                img.src = data.url;
                hiddenInput.value = data.path;
                resultContainer.style.display = 'block';
                updateLivePreview({ image: data.url });
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
    document.getElementById('template-form').addEventListener('submit', function(e) {
        if (this.checkValidity()) {
            const btn = document.getElementById('btn-submit-template');
            setTimeout(() => {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> CREATING...';
            }, 10);
        }
    });
</script>
@endsection
