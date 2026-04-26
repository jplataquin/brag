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
            <div class="card bg-dark bg-opacity-75 border-secondary rounded-4 p-4 shadow-lg" style="backdrop-filter: blur(10px);">
                <form action="{{ route('admin.templates.update', $template->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <h4 class="text-neon-cyan mb-4" style="font-family: 'Orbitron', sans-serif;">Template Details</h4>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label text-white-50">Creator (User)</label>
                            <select name="user_id" id="user_id" class="form-select bg-dark text-white border-secondary" required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $template->user_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->username }} (ID: {{ $user->id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="game_title_id" class="form-label text-white-50">Game Title</label>
                            <select name="game_title_id" id="game_title_id" class="form-select bg-dark text-white border-secondary" required>
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
                        <input type="text" name="card_title" id="card_title" class="form-control bg-dark text-white border-secondary" value="{{ $template->card_title }}" required maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label for="quote" class="form-label text-white-50">Quote</label>
                        <textarea name="quote" id="quote" class="form-control bg-dark text-white border-secondary" rows="3" required maxlength="500">{{ $template->quote }}</textarea>
                    </div>

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
                        <div id="section_upload" class="p-3 neon-card" style="border: 1px dashed rgba(0, 240, 255, 0.4); background: rgba(0, 240, 255, 0.02);">
                            <label for="photo" class="form-label text-white-50">Upload New File</label>
                            <input type="file" name="photo" id="photo" class="form-control bg-dark text-white border-secondary" accept="image/*">
                            <small class="text-muted mt-2 d-block">Uploading a new image will overwrite the existing one (and delete the AI photo if present).</small>
                        </div>

                        <!-- AI Section -->
                        <div id="section_ai" class="p-3 neon-card" style="display: none; border: 1px dashed rgba(255, 0, 255, 0.4); background: rgba(255, 0, 255, 0.02);">
                            <label for="ai_prompt" class="form-label text-white-50">Art Style / Character Description</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control bg-dark text-white border-secondary" id="ai_prompt" name="ai_prompt" placeholder="e.g. Cyberpunk ninja, neon city background, realistic...">
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
                    </div>

                    <hr class="border-secondary my-4">
                    <h4 class="text-neon-magenta mb-4" style="font-family: 'Orbitron', sans-serif;">Design Settings</h4>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="background_color" class="form-label text-white-50">Background Color</label>
                            <input type="color" name="background_color" id="background_color" class="form-control form-control-color bg-dark border-secondary w-100" value="{{ $template->background_color ?? '#000000' }}" title="Choose Background color">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="border_color" class="form-label text-white-50">Border Color</label>
                            <input type="color" name="border_color" id="border_color" class="form-control form-control-color bg-dark border-secondary w-100" value="{{ $template->border_color ?? '#ffffff' }}" title="Choose Border color">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="section_color" class="form-label text-white-50">Section Color</label>
                            <input type="color" name="section_color" id="section_color" class="form-control form-control-color bg-dark border-secondary w-100" value="{{ $template->section_color ?? '#222222' }}" title="Choose Section color">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="primary_text_color" class="form-label text-white-50">Primary Text Color</label>
                            <input type="color" name="primary_text_color" id="primary_text_color" class="form-control form-control-color bg-dark border-secondary w-100" value="{{ $template->primary_text_color ?? '#ffffff' }}" title="Choose Primary Text color">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="secondary_text_color" class="form-label text-white-50">Secondary Text Color</label>
                            <input type="color" name="secondary_text_color" id="secondary_text_color" class="form-control form-control-color bg-dark border-secondary w-100" value="{{ $template->secondary_text_color ?? '#aaaaaa' }}" title="Choose Secondary Text color">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="image_position_y" class="form-label text-white-50">Image Y Position (%)</label>
                            <input type="number" name="image_position_y" id="image_position_y" class="form-control bg-dark text-white border-secondary" value="{{ $template->image_position_y ?? 50 }}" min="0" max="100">
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
            <div class="card bg-dark bg-opacity-75 border-secondary rounded-4 p-4 shadow-lg mb-4" style="backdrop-filter: blur(10px);">
                <h5 class="text-neon-yellow mb-3" style="font-family: 'Orbitron', sans-serif;"><i class="bi bi-clock-history"></i> Tracking Info</h5>
                
                <ul class="list-group list-group-flush bg-transparent">
                    <li class="list-group-item bg-transparent text-white-50 px-0 border-secondary">
                        <strong>Created At:</strong><br>
                        <span class="text-white">{{ $template->created_at->format('M j, Y H:i:s') }}</span>
                    </li>
                    <li class="list-group-item bg-transparent text-white-50 px-0 border-secondary">
                        <strong>Status:</strong><br>
                        @if($template->trashed())
                            <span class="text-danger">Deleted ({{ $template->deleted_at->format('M j, Y H:i') }})</span>
                        @else
                            <span class="text-success">Active</span>
                        @endif
                    </li>
                    <li class="list-group-item bg-transparent text-white-50 px-0 border-secondary">
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
            <div class="card bg-dark border-secondary rounded-4 shadow-lg overflow-hidden">
                 <div class="card-header border-secondary bg-dark text-center">
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
                            :imagePositionY="$template->image_position_y ?? 50"
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
                    // For now, it just falls back to the original image until saved.
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
                    hiddenInput.value = data.path;
                    hiddenInput.setAttribute('data-url', data.url);
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

        // Add live preview listeners for design inputs
        const inputs = ['card_title', 'quote', 'background_color', 'border_color', 'section_color', 'primary_text_color', 'secondary_text_color', 'image_position_y'];
        inputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', function() {
                    let key = id;
                    if (id === 'card_title') key = 'title';
                    if (id === 'image_position_y') key = 'imagePositionY';
                    if (id === 'background_color') key = 'backgroundColor';
                    if (id === 'border_color') key = 'borderColor';
                    if (id === 'section_color') key = 'sectionColor';
                    if (id === 'primary_text_color') key = 'primaryTextColor';
                    if (id === 'secondary_text_color') key = 'secondaryTextColor';
                    
                    let updates = {};
                    updates[key] = this.value;
                    updateLivePreview(updates);
                });
            }
        });
    });
</script>
@endsection
