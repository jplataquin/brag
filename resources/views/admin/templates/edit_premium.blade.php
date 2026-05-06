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
                    <div class="preview-container bg-black border border-secondary rounded overflow-hidden shadow-sm mb-3" style="width: 250px; height: 350px; position: relative;">
                        <canvas id="preview-canvas" width="500" height="700" style="width: 100%; height: 100%;"></canvas>
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
                        <button type="submit" class="btn btn-neon-cyan px-5 fw-bold">SAVE CHANGES</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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

        async draw(config) {
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
                        this.drawPhotoPlaceholder(layer);
                        break;
                    case 'text':
                        this.drawTextElements(layer);
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

        drawTextElements(layer) {
            if (!layer.elements) return;
            Object.entries(layer.elements).forEach(([key, el]) => {
                if (!el.visible) return;
                this.ctx.fillStyle = el.color || '#ffffff';
                this.ctx.font = `${el.weight || 'normal'} ${el.size || 16}px Orbitron, sans-serif`;
                this.ctx.textAlign = el.align || 'left';
                const text = el.content ? el.content.replace(/\{(\w+)\}/g, '$1').toUpperCase() : key.toUpperCase();
                this.ctx.fillText(text, el.x, el.y);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const renderer = new PreviewRenderer('preview-canvas');
        const currentConfig = {!! json_encode($premiumTemplate->premium_config) !!};

        const renderPreview = (level) => {
            if (currentConfig && currentConfig.levels && currentConfig.levels[level]) {
                renderer.draw(currentConfig.levels[level]);
            }
        };

        // Initial Draw
        renderPreview("1");

        document.querySelectorAll('input[name="preview-level"]').forEach(radio => {
            radio.onchange = (e) => renderPreview(e.target.value);
        });
    });
</script>
@endsection
@endsection
