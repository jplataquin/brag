@extends('layouts.app')

@section('title', 'Manage Templates')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title m-0">
            <span class="page-title-accent"><i class="bi bi-images"></i></span> MANAGE TEMPLATES
        </h1>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-neon-magenta" data-bs-toggle="modal" data-bs-target="#premiumUploadModal">
                <i class="bi bi-upload"></i> Upload Premium
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-info">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Search Form -->
    <div class="card bg-dark border-info rounded-4 p-4 mb-4" style="background: rgba(10, 10, 30, 0.8);">
        <form action="{{ route('admin.templates.index') }}" method="GET" class="d-flex gap-2">
            <div class="flex-grow-1">
                <input type="text" name="search" class="form-control bg-dark text-white border-info" placeholder="Search by Template ID, Title, User Username, or Game Title" value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-neon-cyan px-4">
                <i class="bi bi-search"></i> Search
            </button>
            @if(request()->has('search'))
                <a href="{{ route('admin.templates.index') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </form>
    </div>

    <!-- Templates Table -->
    <div class="card bg-dark bg-opacity-75 border-info rounded-4 shadow-lg" style="backdrop-filter: blur(10px);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th scope="col" class="py-3 px-4 text-start">ID</th>
                            <th scope="col" class="py-3">Card Title</th>
                            <th scope="col" class="py-3">Type</th>
                            <th scope="col" class="py-3">Game Title</th>
                            <th scope="col" class="py-3">Status</th>
                            <th scope="col" class="py-3 text-end px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td class="px-4 text-start">
                                    <span class="fw-bold" style="color: var(--neon-cyan);">#{{ $template->id }}</span>
                                </td>
                                <td>
                                    {{ $template->card_title }}
                                </td>
                                <td>
                                    @if($template->is_premium)
                                        <span class="badge bg-neon-magenta text-white">PREMIUM</span>
                                    @else
                                        <span class="badge bg-secondary">Standard</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $template->gameTitle->title ?? 'Deleted Game' }}
                                </td>
                                <td>
                                    @if($template->trashed())
                                        <span class="badge bg-danger">Deleted</span>
                                    @elseif($template->is_premium)
                                        @if($template->status === 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Inactive</span>
                                        @endif
                                    @else
                                        <span class="badge bg-success">Active</span>
                                    @endif
                                </td>
                                <td class="px-4 text-end">
                                    <div class="btn-group">
                                        @if($template->is_premium && !$template->trashed())
                                            <form action="{{ route('admin.templates.toggle_status', $template->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $template->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}" title="Toggle Status">
                                                    <i class="bi {{ $template->status === 'active' ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.templates.edit', $template->id) }}" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                    No templates found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($templates->hasPages())
            <div class="card-footer border-info border-top p-3 d-flex justify-content-center">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('modals')
<!-- Premium Upload Modal -->
<div class="modal fade" id="premiumUploadModal" tabindex="-1" aria-labelledby="premiumUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark border-neon-magenta text-white shadow-lg">
            <form action="{{ route('admin.templates.store_premium') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fs-4" id="premiumUploadModalLabel" style="font-family: 'Orbitron', sans-serif;">
                        <i class="bi bi-upload me-2 text-neon-magenta"></i> UPLOAD PREMIUM TEMPLATE
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <!-- Left side: Preview -->
                        <div class="col-md-5 d-flex flex-column align-items-center">
                            <label class="form-label text-secondary small fw-bold mb-2">LIVE PREVIEW</label>
                            <div class="preview-container bg-black border border-secondary rounded overflow-hidden shadow-sm" style="width: 250px; height: 350px; position: relative;">
                                <canvas id="preview-canvas" width="500" height="700" style="width: 100%; height: 100%;"></canvas>
                                <div id="preview-placeholder" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center text-center p-3 text-muted" style="background: rgba(0,0,0,0.8); z-index: 5;">
                                    <i class="bi bi-file-earmark-code display-4 mb-2"></i>
                                    <small>Select a JSON file to see a preview</small>
                                </div>
                            </div>
                            <div class="btn-group btn-group-sm mt-3 w-100 shadow-sm" id="level-toggle-group" style="display: none;">
                                @foreach([1,2,3,4,5] as $lv)
                                    <input type="radio" class="btn-check" name="preview-level" id="p-lv{{ $lv }}" value="{{ $lv }}" {{ $lv == 1 ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary py-1" for="p-lv{{ $lv }}">{{ $lv }}</label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Right side: Details -->
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label for="config_file" class="form-label text-neon-cyan small fw-bold">1. TEMPLATE JSON FILE</label>
                                <input type="file" id="config_file" class="form-control bg-dark text-white border-secondary" accept=".json">
                                <input type="hidden" name="temporary_json_path" id="temporary_json_path">
                                
                                <div class="progress mt-2" style="height: 6px; display: none; background: rgba(255,255,255,0.1);" id="json-progress-wrapper">
                                    <div id="json-progress-bar" class="progress-bar bg-neon-magenta" role="progressbar" style="width: 0%"></div>
                                </div>
                                <div id="json-status" class="small mt-1" style="font-size: 0.7rem;"></div>
                                
                                <div class="form-text text-muted small">Exported from the Card Designer Studio.</div>
                            </div>

                            <div class="mb-3">
                                <label for="card_title" class="form-label text-neon-cyan small fw-bold">2. CARD TITLE</label>
                                <input type="text" name="card_title" id="card_title" class="form-control bg-dark text-white border-secondary" placeholder="e.g. Neon Dragon" required>
                            </div>

                            <div class="mb-3">
                                <label for="game_title_id" class="form-label text-neon-cyan small fw-bold">3. LOCK TO GAME TITLE</label>
                                <select name="game_title_id" id="game_title_id" class="form-select bg-dark text-white border-secondary" required>
                                    <option value="">Select a Game...</option>
                                    @foreach($gameTitles as $game)
                                        <option value="{{ $game->id }}">{{ $game->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="price" class="form-label text-neon-cyan small fw-bold">4. PRICE (DIAMONDS)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-dark border-secondary text-neon-lime"><i class="bi bi-gem"></i></span>
                                        <input type="number" name="price" id="price" class="form-control bg-dark text-white border-secondary" value="50" min="0" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="status" class="form-label text-neon-cyan small fw-bold">5. INITIAL STATUS</label>
                                    <select name="status" id="status" class="form-select bg-dark text-white border-secondary" required>
                                        <option value="inactive" selected>Inactive (Testing)</option>
                                        <option value="active">Active (Public)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="designer_name" class="form-label text-neon-cyan small fw-bold">6. DESIGNER NAME</label>
                                <input type="text" name="designer_name" id="designer_name" class="form-control bg-dark text-white border-secondary" placeholder="Credit the artist">
                            </div>

                            <div class="mb-0">
                                <label for="description" class="form-label text-neon-cyan small fw-bold">7. DESCRIPTION</label>
                                <textarea name="description" id="description" class="form-control bg-dark text-white border-secondary" rows="2" placeholder="Marketing description for the shop"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary bg-black bg-opacity-25">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="btn-submit-premium" class="btn btn-neon-magenta px-4 fw-bold" disabled>
                        <i class="bi bi-check2-circle"></i> PROCESS & UPLOAD
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

<style>
    .border-neon-magenta { border-color: #ff00ff !important; box-shadow: 0 0 15px rgba(255, 0, 255, 0.2); }
    .text-neon-magenta { color: #ff00ff; }
    .btn-neon-magenta { background: #ff00ff; color: white; border: none; }
    .btn-neon-magenta:hover { background: #d900d9; box-shadow: 0 0 10px #ff00ff; color: white; }
    .bg-neon-magenta { background-color: #ff00ff; }
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
        const fileInput = document.getElementById('config_file');
        const placeholder = document.getElementById('preview-placeholder');
        const levelGroup = document.getElementById('level-toggle-group');
        const tempPathInput = document.getElementById('temporary_json_path');
        const progressWrapper = document.getElementById('json-progress-wrapper');
        const progressBar = document.getElementById('json-progress-bar');
        const statusText = document.getElementById('json-status');
        const btnSubmit = document.getElementById('btn-submit-premium');
        
        let currentConfig = null;

        fileInput.onchange = (e) => {
            const file = e.target.files[0];
            if (!file) return;

            // 1. Preview Logic
            const reader = new FileReader();
            reader.onload = (event) => {
                try {
                    currentConfig = JSON.parse(event.target.result);
                    if (currentConfig.levels) {
                        placeholder.classList.add('d-none');
                        placeholder.classList.remove('d-flex');
                        levelGroup.style.display = 'flex';
                        renderPreview("1");
                    }
                } catch (err) {
                    alert('Invalid JSON file.');
                }
            };
            reader.readAsText(file);

            // 2. Chunk Upload Logic
            uploadFileInChunks(file);
        };

        function uploadFileInChunks(file) {
            const chunkSize = 1 * 1024 * 1024; // 1MB chunks
            const totalChunks = Math.ceil(file.size / chunkSize);
            const fileId = 'json_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            const extension = 'json';
            let chunkIndex = 0;

            progressWrapper.style.display = 'block';
            progressBar.style.width = '0%';
            statusText.innerText = 'Starting upload...';
            statusText.style.color = '#00f0ff';
            btnSubmit.disabled = true;

            const uploadNextChunk = () => {
                const start = chunkIndex * chunkSize;
                const end = Math.min(start + chunkSize, file.size);
                const chunk = file.slice(start, end);

                const formData = new FormData();
                formData.append('file', chunk);
                formData.append('file_id', fileId);
                formData.append('chunk_index', chunkIndex);
                formData.append('total_chunks', totalChunks);
                formData.append('extension', extension);
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route("upload.chunk") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(async response => {
                    const isJson = response.headers.get('content-type')?.includes('application/json');
                    const data = isJson ? await response.json() : null;

                    if (!response.ok) {
                        const errorMsg = data?.error || data?.message || `Server error (${response.status})`;
                        throw new Error(errorMsg);
                    }
                    return data;
                })
                .then(data => {
                    chunkIndex++;
                    const percent = Math.round((chunkIndex / totalChunks) * 100);
                    progressBar.style.width = percent + '%';
                    statusText.innerText = 'Uploading: ' + percent + '%';

                    if (chunkIndex < totalChunks) {
                        uploadNextChunk();
                    } else if (data.success && data.path) {
                        tempPathInput.value = data.path;
                        statusText.innerText = 'Upload complete!';
                        statusText.style.color = '#39ff14';
                        btnSubmit.disabled = false;
                    }
                })
                .catch(err => {
                    console.error('Upload Error:', err);
                    statusText.innerText = 'Upload failed: ' + err.message;
                    statusText.style.color = 'red';
                });
            };

            uploadNextChunk();
        }

        const renderPreview = (level) => {
            if (currentConfig && currentConfig.levels[level]) {
                renderer.draw(currentConfig.levels[level]);
            }
        };

        document.querySelectorAll('input[name="preview-level"]').forEach(radio => {
            radio.onchange = (e) => renderPreview(e.target.value);
        });
    });
</script>
@endsection
