@extends('layouts.app')

@section('title', 'Edit Premium Template #' . $premiumTemplate->id)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title m-0">
            <span class="page-title-accent"><i class="bi bi-gem"></i></span> EDIT PREMIUM TEMPLATE <span class="text-muted fs-4">#{{ $premiumTemplate->id }}</span>
        </h1>
        <a href="{{ route('admin.templates.index') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left"></i> Back to Catalog
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
        <!-- Preview Column -->
        <div class="col-lg-4 mb-4">
            <div class="card bg-dark bg-opacity-75 border-neon-magenta rounded-4 p-4 shadow-lg h-100 text-center" style="backdrop-filter: blur(10px);">
                <h5 class="text-neon-magenta mb-4" style="font-family: 'Orbitron', sans-serif;">LIVE PREVIEW</h5>
                
                <div class="d-flex flex-column align-items-center">
                    <div class="preview-container bg-black border border-secondary rounded overflow-hidden shadow-sm mb-3" style="width: 250px; height: 350px; position: relative; cursor: zoom-in;" id="btn-fullscreen-trigger">
                        <canvas id="preview-canvas" width="500" height="700" style="width: 100%; height: 100%;"></canvas>
                        <div class="position-absolute bottom-0 end-0 p-2 text-white-50" style="pointer-events: none;">
                            <i class="bi bi-fullscreen"></i>
                        </div>
                    </div>
                    
                    <div class="btn-group btn-group-sm w-100 shadow-sm" id="level-toggle-group">
                        @foreach([1,2,3,4,5] as $lv)
                            <input type="radio" class="btn-check" name="preview-level" id="p-lv{{ $lv }}" value="{{ $lv }}" {{ $lv == 1 ? 'checked' : '' }}>
                            <label class="btn btn-outline-secondary py-1" for="p-lv{{ $lv }}">{{ $lv }}</label>
                        @endforeach
                    </div>
                    
                    <div class="mt-4 text-start w-100">
                        <small class="text-muted d-block mb-1">DESIGNER</small>
                        <p class="text-white mb-3">{{ $premiumTemplate->designer_name ?? 'Anonymous' }}</p>
                        
                        <small class="text-muted d-block mb-1">GAME TITLE</small>
                        <p class="text-white mb-0">{{ $premiumTemplate->gameTitle->title ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Column -->
        <div class="col-lg-8 mb-4">
            <div class="card bg-dark bg-opacity-75 border-info rounded-4 p-4 shadow-lg h-100" style="backdrop-filter: blur(10px);">
                <form action="{{ route('admin.premium-templates.update', $premiumTemplate->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h4 class="text-neon-cyan mb-4" style="font-family: 'Orbitron', sans-serif;">Template Metadata</h4>

                    <div class="mb-3">
                        <label for="template_title" class="form-label text-white-50">Template Title</label>
                        <input type="text" name="template_title" id="template_title" class="form-control bg-dark text-white border-info" value="{{ old('template_title', $premiumTemplate->template_title) }}" required maxlength="255">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="game_title_id" class="form-label text-white-50">Game Title</label>
                            <select name="game_title_id" id="game_title_id" class="form-select bg-dark text-white border-info" required>
                                @foreach($gameTitles as $game)
                                    <option value="{{ $game->id }}" {{ $premiumTemplate->game_title_id == $game->id ? 'selected' : '' }}>
                                        {{ $game->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label text-white-50">Price (Diamonds)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-info text-neon-lime"><i class="bi bi-gem"></i></span>
                                <input type="number" name="price" id="price" class="form-control bg-dark text-white border-info" value="{{ old('price', $premiumTemplate->price) }}" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="designer_name" class="form-label text-white-50">Designer Name</label>
                            <input type="text" name="designer_name" id="designer_name" class="form-control bg-dark text-white border-info" value="{{ old('designer_name', $premiumTemplate->designer_name) }}" placeholder="Credit the artist">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label text-white-50">Status</label>
                            <select name="status" id="status" class="form-select bg-dark text-white border-info" required>
                                <option value="inactive" {{ $premiumTemplate->status === 'inactive' ? 'selected' : '' }}>Inactive (Internal Testing)</option>
                                <option value="active" {{ $premiumTemplate->status === 'active' ? 'selected' : '' }}>Active (Public Shop)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label text-white-50">Description</label>
                        <textarea name="description" id="description" class="form-control bg-dark text-white border-info" rows="4" placeholder="Marketing description for the shop">{{ old('description', $premiumTemplate->description) }}</textarea>
                    </div>

                    <div class="alert alert-info py-2 px-3 border-info border-opacity-25" style="background: rgba(0, 240, 255, 0.05);">
                        <small class="text-info"><i class="bi bi-info-circle me-1"></i> Note: The design layers (JSON) cannot be edited here. To change the design, please re-upload a new template from the Studio.</small>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-4">
                        <a href="{{ route('admin.templates.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-neon-cyan px-5 fw-bold">SAVE METADATA</button>
                    </div>
                </form>
            </div>

            <!-- User Experience Test Panel -->
            <div class="card bg-dark bg-opacity-75 border-neon-magenta rounded-4 p-4 shadow-lg mt-4" style="backdrop-filter: blur(10px);">
                <h4 class="text-neon-magenta mb-4" style="font-family: 'Orbitron', sans-serif;"><i class="bi bi-controller"></i> USER EXPERIENCE TEST</h4>
                <p class="text-muted small mb-4">Test how this template behaves when a user purchases it and applies their own content. <em>These inputs only affect the live preview on the left and are NOT saved to the database.</em></p>

                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label for="test_card_title" class="form-label text-white-50">Test Card Title</label>
                        <input type="text" id="test_card_title" class="form-control bg-dark text-white border-secondary" placeholder="e.g. THE IMMORTAL ONE">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="test_quote" class="form-label text-white-50">Test Quote</label>
                        <textarea id="test_quote" class="form-control bg-dark text-white border-secondary" rows="1" placeholder="e.g. Victory is my only option."></textarea>
                    </div>
                    <div class="col-12">
                        <label for="test_photo" class="form-label text-white-50">Test Personal Photo</label>
                        <input type="file" id="test_photo" class="form-control bg-dark text-white border-secondary" accept="image/*">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('modals')
<div class="modal fade" id="fullscreenPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered d-flex justify-content-center" style="max-width: 95vw;">
        <div class="modal-content bg-transparent border-0 w-auto">
            <div class="modal-body p-0 shadow-lg">
                <canvas id="fullscreen-canvas" width="500" height="700" style="max-height: 85vh; width: auto; border-radius: 15px; border: 1px solid rgba(255,255,255,0.3); box-shadow: 0 0 30px rgba(0,0,0,0.8);"></canvas>
            </div>
            <div class="modal-footer border-0 justify-content-center mt-3">
                <button type="button" class="btn btn-neon-magenta px-5 rounded-pill fw-bold shadow" data-bs-dismiss="modal">CLOSE PREVIEW</button>
            </div>
        </div>
    </div>
</div>
@endpush

<style>
    .border-neon-magenta { border-color: #ff00ff !important; box-shadow: 0 0 15px rgba(255, 0, 255, 0.1); }
    .text-neon-magenta { color: #ff00ff; }
    .text-neon-cyan { color: #00f0ff; }
    .btn-neon-cyan { background: #00f0ff; color: black; border: none; }
    .btn-neon-cyan:hover { background: #00d9e6; box-shadow: 0 0 10px #00f0ff; }
    .text-neon-lime { color: #39ff14; }
</style>

@section('scripts')
<script>
    // Simplified StudioRenderer for Preview
    class PreviewRenderer {
        constructor(canvasId) {
            this.canvas = document.getElementById(canvasId);
            this.ctx = this.canvas.getContext('2d');
            this.width = this.canvas.width;
            this.height = this.canvas.height;
        }

        clear() {
            this.ctx.clearRect(0, 0, this.width, this.height);
        }

        async draw(config, testData) {
            this.clear();
            if (!config || !config.layers) return;
            // Iterate backwards for painter's algorithm
            for (let i = config.layers.length - 1; i >= 0; i--) {
                const layer = config.layers[i];
                if (layer.visible === false) continue;
                
                switch (layer.type) {
                    case 'image':
                        await this.drawAsset(layer);
                        break;
                    case 'photo':
                        await this.drawPhoto(layer, testData.photo);
                        break;
                    case 'text':
                        this.drawTextElements(layer, testData);
                        break;
                }
            }
        }

        async drawAsset(layer) {
            if (!layer.data && !layer.asset_path) return;
            
            return new Promise((resolve) => {
                const img = new Image();
                img.onload = () => {
                    const w = layer.width || this.width;
                    const h = layer.height || this.height;
                    this.ctx.drawImage(img, layer.x || 0, layer.y || 0, w, h);
                    resolve();
                };
                img.onerror = () => resolve();
                img.src = layer.data ? layer.data : `/storage/${layer.asset_path}`;
            });
        }

        async drawPhoto(layer, customPhoto) {
            const { x, y, width, height, shape } = layer;
            
            if (customPhoto) {
                return new Promise((resolve) => {
                    const img = new Image();
                    img.onload = () => {
                        this.ctx.save();
                        this.ctx.beginPath();
                        if (shape === 'circle') {
                            const radius = Math.min(width, height) / 2;
                            this.ctx.arc(x + width / 2, y + height / 2, radius, 0, Math.PI * 2);
                        } else {
                            this.ctx.rect(x, y, width, height);
                        }
                        this.ctx.clip();
                        this.ctx.drawImage(img, x, y, width, height);
                        this.ctx.restore();
                        resolve();
                    };
                    img.onerror = () => {
                        this.drawPhotoPlaceholder(layer);
                        resolve();
                    };
                    img.src = customPhoto;
                });
            } else {
                this.drawPhotoPlaceholder(layer);
            }
        }

        drawPhotoPlaceholder(layer) {
            const { x, y, width, height, shape } = layer;
            this.ctx.setLineDash([5, 5]);
            this.ctx.strokeStyle = '#00f0ff';
            this.ctx.lineWidth = 2;
            this.ctx.fillStyle = 'rgba(0, 240, 255, 0.1)';
            this.ctx.beginPath();
            if (shape === 'circle') {
                const radius = Math.min(width, height) / 2;
                this.ctx.arc(x + width / 2, y + height / 2, radius, 0, Math.PI * 2);
            } else {
                this.ctx.rect(x, y, width, height);
            }
            this.ctx.fill();
            this.ctx.stroke();
            this.ctx.setLineDash([]);
        }

        drawTextElements(layer, testData) {
            if (!layer.elements) return;
            Object.entries(layer.elements).forEach(([key, el]) => {
                if (!el.visible) return;
                this.ctx.fillStyle = el.color || '#ffffff';
                this.ctx.font = `${el.weight || 'normal'} ${el.size || 16}px Orbitron, sans-serif`;
                this.ctx.textAlign = el.align || 'left';
                
                let text = el.content || '';
                
                // Robust Placeholder Replacement
                if (key === 'title') {
                    text = testData.title || 'YOUR CARD TITLE';
                } else if (key === 'quote') {
                    text = testData.quote || 'YOUR QUOTE GOES HERE...';
                } else {
                    // Replace variables in curly braces
                    text = text.replace(/\{title\}/gi, testData.title || 'TITLE');
                    text = text.replace(/\{quote\}/gi, testData.quote || 'QUOTE');
                    text = text.replace(/\{(\w+)\}/g, (match, p1) => {
                        return p1.toUpperCase();
                    });
                }
                
                this.ctx.fillText(text.toUpperCase(), el.x, el.y);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const previewCanvas = document.getElementById('preview-canvas');
        const fullscreenCanvas = document.getElementById('fullscreen-canvas');
        
        const renderer = new PreviewRenderer('preview-canvas');
        const fsRenderer = new PreviewRenderer('fullscreen-canvas');
        
        const currentConfig = {!! json_encode($premiumTemplate->premium_config) !!};
        
        // UX Test State
        const uxTest = {
            title: '',
            quote: '',
            photo: null
        };

        const renderPreview = (level) => {
            if (currentConfig && currentConfig.levels && currentConfig.levels[level]) {
                renderer.draw(currentConfig.levels[level], uxTest);
                fsRenderer.draw(currentConfig.levels[level], uxTest);
            }
        };

        // Initial Draw
        renderPreview("1");

        // UI Listeners
        document.querySelectorAll('input[name="preview-level"]').forEach(radio => {
            radio.onchange = (e) => renderPreview(e.target.value);
        });

        document.getElementById('test_card_title').addEventListener('input', (e) => {
            uxTest.title = e.target.value;
            const currentLevel = document.querySelector('input[name="preview-level"]:checked').value;
            renderPreview(currentLevel);
        });

        document.getElementById('test_quote').addEventListener('input', (e) => {
            uxTest.quote = e.target.value;
            const currentLevel = document.querySelector('input[name="preview-level"]:checked').value;
            renderPreview(currentLevel);
        });

        document.getElementById('test_photo').addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    uxTest.photo = event.target.result;
                    const currentLevel = document.querySelector('input[name="preview-level"]:checked').value;
                    renderPreview(currentLevel);
                };
                reader.readAsDataURL(file);
            }
        });

        // Fullscreen Modal Logic
        const fsModal = new bootstrap.Modal(document.getElementById('fullscreenPreviewModal'));
        document.getElementById('btn-fullscreen-trigger').onclick = () => {
            fsModal.show();
            // Re-render specifically for FS to ensure dimensions are correct
            const currentLevel = document.querySelector('input[name="preview-level"]:checked').value;
            setTimeout(() => renderPreview(currentLevel), 150); 
        };
    });
</script>
@endsection
