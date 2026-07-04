@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-5 fw-bold text-uppercase mb-1" style="color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                <i class="bi bi-pencil-square"></i> Edit Package
            </h1>
            <p class="text-secondary lead mb-0">Update diamond purchasing package details.</p>
        </div>
        <a href="{{ route('admin.diamond-packages.index') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left"></i> Back to Packages
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-dark bg-opacity-75 border-info rounded-4 p-4 shadow-lg" style="backdrop-filter: blur(10px);">
                <div class="card-body">
                    <form action="{{ route('admin.diamond-packages.update', $diamondPackage->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="name" class="form-label text-muted small text-uppercase fw-bold">Package Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control form-control-lg bg-dark text-white border-info @error('name') is-invalid @enderror" value="{{ old('name', $diamondPackage->name) }}" required autofocus placeholder="e.g. Starter Pack">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="diamonds" class="form-label text-muted small text-uppercase fw-bold">Diamonds Amount <span class="text-danger">*</span></label>
                                <input type="number" name="diamonds" id="diamonds" class="form-control form-control-lg bg-dark text-white border-info @error('diamonds') is-invalid @enderror" value="{{ old('diamonds', $diamondPackage->diamonds) }}" required min="1">
                                @error('diamonds')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="currency" class="form-label text-muted small text-uppercase fw-bold">Currency <span class="text-danger">*</span></label>
                                <input type="text" name="currency" id="currency" class="form-control form-control-lg bg-dark text-white border-info @error('currency') is-invalid @enderror" value="{{ old('currency', $diamondPackage->currency) }}" required>
                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="price" class="form-label text-muted small text-uppercase fw-bold">Regular Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="price" id="price" class="form-control form-control-lg bg-dark text-white border-info @error('price') is-invalid @enderror" value="{{ old('price', $diamondPackage->price) }}" required min="0">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="promo_price" class="form-label text-muted small text-uppercase fw-bold">Promo Price (Optional)</label>
                                <input type="number" step="0.01" name="promo_price" id="promo_price" class="form-control form-control-lg bg-dark text-white border-info @error('promo_price') is-invalid @enderror" value="{{ old('promo_price', $diamondPackage->promo_price) }}" min="0">
                                @error('promo_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="qr_code" class="form-label text-muted small text-uppercase fw-bold">Manual Payment QR Code</label>
                            @if($diamondPackage->qr_path)
                                <div class="mb-3">
                                    <div class="text-secondary small mb-1">Current QR Code:</div>
                                    <img src="{{ asset('storage/' . $diamondPackage->qr_path) }}" alt="QR Code" class="img-thumbnail bg-dark border-info" style="max-height: 150px;">
                                </div>
                            @endif
                            <x-file-input 
                                id="qr_code" 
                                name="qr_code" 
                                accept="image/*" 
                                placeholder="Choose New QR Image" 
                                color="lime" 
                            />
                            @error('qr_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <input type="hidden" name="temporary_qr_path" id="temporary_qr_path">
                            
                            <!-- Progress Bar -->
                            <div id="upload-progress-container" class="mt-3" style="display: none;">
                                <div class="progress bg-dark border border-info" style="height: 10px;">
                                    <div id="upload-progress-bar" class="progress-bar bg-info progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                                </div>
                                <div id="upload-status" class="small text-info mt-1 text-center">Uploading: 0%</div>
                            </div>
                            
                            <div class="form-text text-secondary mt-2">Upload a new QR code image to replace the current one.</div>
                        </div>

                        <div class="mb-4">
                            <label for="ocr_match_string" class="form-label text-muted small text-uppercase fw-bold">Required OCR Pattern (Regex) (Optional)</label>
                            <input type="text" name="ocr_match_string" id="ocr_match_string" class="form-control form-control-lg bg-dark text-white border-info @error('ocr_match_string') is-invalid @enderror" value="{{ old('ocr_match_string', $diamondPackage->ocr_match_string) }}" placeholder="e.g. (1234|234) or gcash.*1234">
                            @error('ocr_match_string')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-secondary mt-2">
                                <i class="bi bi-info-circle me-1"></i> Supports Regular Expressions. The system will verify this pattern against the text found in the screenshot (spaces and commas are removed from the image text before checking).<br>
                                <strong>Examples:</strong> <code>(1234|234)</code> for partial account numbers, <code>bdo.*1234</code> to require both a bank name and account. Use <code>&&</code> to require multiple distinct patterns (e.g., <code>gcash&&Ref No.</code>).
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-check form-switch custom-switch">
                                    <input class="form-check-input bg-dark border-info" type="checkbox" name="is_active" id="is_active" {{ old('is_active', $diamondPackage->is_active) ? 'checked' : '' }} value="1">
                                    <label class="form-check-label text-white small text-uppercase fw-bold" for="is_active">Active Package</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch custom-switch">
                                    <input class="form-check-input bg-dark border-info" type="checkbox" name="allow_hitpay" id="allow_hitpay" {{ old('allow_hitpay', $diamondPackage->allow_hitpay) ? 'checked' : '' }} value="1">
                                    <label class="form-check-label text-white small text-uppercase fw-bold" for="allow_hitpay">Allow HitPay</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch custom-switch">
                                    <input class="form-check-input bg-dark border-info" type="checkbox" name="allow_manual" id="allow_manual" {{ old('allow_manual', $diamondPackage->allow_manual) ? 'checked' : '' }} value="1">
                                    <label class="form-check-label text-white small text-uppercase fw-bold" for="allow_manual">Allow Manual</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-5">
                            <button type="submit" id="btn-submit-package" class="btn btn-lg btn-neon-cyan fw-bold">
                                <i class="bi bi-check-lg"></i> Update Package
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
    const qrInput = document.getElementById('qr_code');
    const btnSubmit = document.getElementById('btn-submit-package');
    const progressContainer = document.getElementById('upload-progress-container');
    const progressBar = document.getElementById('upload-progress-bar');
    const statusText = document.getElementById('upload-status');
    const tempInput = document.getElementById('temporary_qr_path');

    qrInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            btnSubmit.disabled = true;
            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';
            statusText.innerText = 'Uploading: 0%';
            statusText.style.color = '#00f0ff';

            const CHUNK_SIZE = 256 * 1024; // 256KB
            const fileId = 'qr_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
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

<style>
    .custom-switch .form-check-input {
        width: 3em;
        height: 1.5em;
        cursor: pointer;
    }
    .custom-switch .form-check-input:checked {
        background-color: var(--neon-cyan);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 10px rgba(0, 240, 255, 0.5);
    }
</style>
@endsection
