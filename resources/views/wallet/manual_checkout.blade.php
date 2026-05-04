@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="display-6 fw-bold text-uppercase mb-1" style="color: var(--neon-yellow); text-shadow: 0 0 10px rgba(255, 221, 0, 0.5); font-family: 'Orbitron', sans-serif;">
                        <i class="bi bi-qr-code"></i> Manual Payment
                    </h1>
                    <p class="text-secondary mb-0">Scan the QR code to pay for your diamonds.</p>
                </div>
                <a href="{{ route('wallet.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i> Cancel
                </a>
            </div>

            <div class="card bg-dark bg-opacity-75 border-warning rounded-4 shadow-lg mb-4" style="backdrop-filter: blur(10px);">
                <div class="card-body p-4 text-center">
                    <h4 class="text-white mb-3">{{ $package->name }}</h4>
                    <div class="mb-4">
                        <span class="badge bg-info rounded-pill px-3 py-2 fs-6">
                            <i class="bi bi-gem me-1"></i> {{ $package->diamonds }} DIAMONDS
                        </span>
                        <div class="text-white mt-2 fs-4 fw-bold" style="font-family: 'Orbitron', sans-serif; letter-spacing: 1px;">
                            {{ $package->currency }} {{ number_format($package->final_price, 2) }}
                        </div>
                    </div>
                    
                    <div class="mb-4 p-3 bg-white rounded-3 d-inline-block shadow-sm">
                        @if($package->qr_path)
                            <img src="{{ asset('storage/' . $package->qr_path) }}" alt="Payment QR Code" class="img-fluid" style="max-width: 250px;">
                        @else
                            <div class="p-5 text-dark">
                                <i class="bi bi-qr-code text-muted" style="font-size: 5rem;"></i>
                                <p class="mt-2 mb-0 fw-bold">QR Code Not Available</p>
                                <p class="small text-muted">Please contact support.</p>
                            </div>
                        @endif
                    </div>

                    <div class="alert alert-warning bg-warning bg-opacity-10 border-warning text-warning small mb-4">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Please pay exactly <strong>{{ $package->currency }} {{ number_format($package->final_price, 2) }}</strong> using the QR code above.
                    </div>

                    @if($agreement)
                        <div class="text-start mb-4">
                            <label class="form-label text-muted small text-uppercase fw-bold">Manual Payment Agreement</label>
                            <div class="bg-dark bg-opacity-50 border border-secondary rounded-3 p-3 mb-3 text-secondary small scrollable-agreement" style="max-height: 150px; overflow-y: auto; line-height: 1.6; border-style: dashed !important;">
                                {!! nl2br(e($agreement->content)) !!}
                            </div>
                            <div class="form-check custom-checkbox">
                                <input class="form-check-input bg-dark border-warning" type="checkbox" name="i_agree" id="i_agree" required form="proof-upload-form">
                                <label class="form-check-label text-white small" for="i_agree">
                                    I have read and agree to the manual payment terms and conditions above.
                                </label>
                            </div>
                        </div>
                    @endif

                    <form id="proof-upload-form" action="{{ route('payments.manual.proof', $package->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="proof_temp_path" id="proof_temp_path">
                        
                        <div class="mb-4 text-start">
                            <label class="form-label text-muted small text-uppercase fw-bold">Upload Proof of Payment (Screenshot)</label>
                            
                            <!-- Custom Dropzone -->
                            <div class="position-relative" id="proof-upload-wrapper">
                                <input type="file" class="position-absolute w-100 h-100 opacity-0"
                                       style="z-index: 2; cursor: pointer; top: 0; left: 0;"
                                       id="proof" accept="image/*" required>
                                <div id="proof-dropzone" class="d-flex flex-column align-items-center justify-content-center p-5 text-center neon-card" 
                                     style="border: 2px dashed rgba(255, 221, 0, 0.4); background: rgba(255, 221, 0, 0.02); transition: all 0.3s ease;">
                                    <i class="bi bi-cloud-arrow-up-fill mb-2" style="font-size: 3rem; color: var(--neon-yellow); text-shadow: 0 0 10px rgba(255, 221, 0, 0.4);"></i>
                                    <span class="text-warning fw-bold" style="font-family: 'Orbitron', sans-serif; letter-spacing: 1px;">CLICK OR DRAG SCREENSHOT HERE</span>
                                    <small class="mt-2 text-secondary" style="font-size: 0.8rem;">Supports JPEG, PNG, GIF, WebP</small>
                                </div>
                            </div>

                            <!-- Photo Preview -->
                            <div id="proof-preview" class="mt-3 text-center" style="display: none;">
                                <div class="text-secondary small mb-2 text-uppercase fw-bold">Selected Image:</div>
                                <img id="preview-img" src="" alt="Preview" class="img-fluid rounded-4 border border-warning shadow-sm" style="max-height: 200px;">
                                <div class="mt-2">
                                    <button type="button" id="btn-reset-proof" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                        <i class="bi bi-trash me-1"></i> Remove & Choose Another
                                    </button>
                                </div>
                            </div>
                            
                            @error('proof')
                                <div class="text-danger mt-2 small">{{ $message }}</div>
                            @enderror
                            
                            <!-- Progress Bar -->
                            <div id="upload-progress-container" class="mt-4" style="display: none;">
                                <div class="progress bg-dark border border-warning" style="height: 12px; border-radius: 6px;">
                                    <div id="upload-progress-bar" class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                                </div>
                                <div id="upload-status" class="small text-warning mt-2 text-center fw-bold">Uploading: 0%</div>
                            </div>
                        </div>

                        <div class="d-grid mt-5">
                            <button type="submit" id="btn-submit-proof" class="btn btn-lg btn-warning fw-bold text-dark py-3" style="box-shadow: 0 0 15px rgba(255, 221, 0, 0.4);">
                                <i class="bi bi-check2-circle me-2"></i> Confirm Submission
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card bg-dark bg-opacity-50 border-info rounded-4 border-dashed mt-4">
                <div class="card-body p-3 text-center small text-secondary">
                    <i class="bi bi-clock me-1"></i> After submission, our team has 10 minutes to validate your payment. If not reviewed, it will be automatically approved.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const proofInput = document.getElementById('proof');
    const proofDropzone = document.getElementById('proof-dropzone');
    const btnSubmit = document.getElementById('btn-submit-proof');
    const progressContainer = document.getElementById('upload-progress-container');
    const progressBar = document.getElementById('upload-progress-bar');
    const statusText = document.getElementById('upload-status');
    const tempInput = document.getElementById('proof_temp_path');
    const uploadForm = document.getElementById('proof-upload-form');
    const previewContainer = document.getElementById('proof-preview');
    const previewImg = document.getElementById('preview-img');
    const btnReset = document.getElementById('btn-reset-proof');
    const proofUploadWrapper = document.getElementById('proof-upload-wrapper');

    const expectedAmount = "{{ number_format($package->final_price, 2, '.', '') }}";
    const requiredOcrText = "{{ $package->ocr_match_string }}";

    // Reset function
    btnReset.addEventListener('click', function() {
        proofInput.value = '';
        tempInput.value = '';
        previewImg.src = '';
        previewContainer.style.display = 'none';
        progressContainer.style.display = 'none';
        proofUploadWrapper.style.display = 'block';
        proofInput.setAttribute('required', 'required');
        proofDropzone.style.borderColor = 'rgba(255, 221, 0, 0.4)';
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="bi bi-check2-circle me-2"></i> Confirm Submission';
    });

    // Drag and drop feedback
    proofInput.addEventListener('dragenter', () => proofDropzone.style.borderColor = '#ffdd00');
    proofInput.addEventListener('dragleave', () => proofDropzone.style.borderColor = 'rgba(255, 221, 0, 0.4)');
    proofInput.addEventListener('drop', () => proofDropzone.style.borderColor = 'rgba(255, 221, 0, 0.4)');

    proofInput.addEventListener('change', async function() {
        const file = this.files[0];
        if (file) {
            // Hide upload area
            proofUploadWrapper.style.display = 'none';

            // Local Preview
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);

            btnSubmit.disabled = true;
            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';
            statusText.innerText = 'Initializing scanner...';
            statusText.style.color = '#ffdd00';

            // Load image for analysis
            const img = new Image();
            img.src = URL.createObjectURL(file);
            await img.decode();

            const canvas = document.createElement('canvas');
            const maxAnalysisSize = 600;
            const scale = Math.min(maxAnalysisSize / img.width, maxAnalysisSize / img.height, 1);
            canvas.width = img.width * scale;
            canvas.height = img.height * scale;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            // OCR Check
            let scanMessage = 'Scanning for payment amount: {{ $package->currency }} ' + expectedAmount;
            if (requiredOcrText) {
                scanMessage += ' and key information...';
            } else {
                scanMessage += '...';
            }
            statusText.innerText = scanMessage;

            try {
                const { data: { text } } = await Tesseract.recognize(canvas, 'eng');
                console.log('Extracted Text:', text);
                
                const sanitizedText = text.replace(/[\s,]/g, '').toLowerCase();
                
                // Amount Check
                const searchAmount = expectedAmount.replace(/[\s,]/g, '');
                const searchAmountInt = parseInt(searchAmount);

                if (!sanitizedText.includes(searchAmount) && !sanitizedText.includes(searchAmountInt.toString())) {
                    statusText.innerHTML = 'Error: Could not find amount <strong>' + expectedAmount + '</strong> in screenshot. <br><small class="text-secondary">Please ensure the image is not blurry and the amount is clearly visible.</small>';
                    statusText.style.color = '#ff4444';
                    return;
                }

                // Custom Regex Check
                if (requiredOcrText) {
                    try {
                        const regex = new RegExp(requiredOcrText.replace(/[\s,]/g, ''), 'i');
                        if (!regex.test(sanitizedText)) {
                            statusText.innerHTML = 'Error: Required information matching pattern <strong>"' + requiredOcrText + '"</strong> not found. <br><small class="text-secondary">Please ensure the image is not blurry and all details are clearly visible.</small>';
                            statusText.style.color = '#ff4444';
                            return;
                        }
                    } catch (regexErr) {
                        console.error('Invalid OCR Regex:', regexErr);
                        // Fallback to simple inclusion if regex is invalid
                        const searchString = requiredOcrText.replace(/[\s,]/g, '').toLowerCase();
                        if (!sanitizedText.includes(searchString)) {
                            statusText.innerHTML = 'Error: Required information <strong>"' + requiredOcrText + '"</strong> not found. <br><small class="text-secondary">Please ensure the image is not blurry and all details are clearly visible.</small>';
                            statusText.style.color = '#ff4444';
                            return;
                        }
                    }
                }
            } catch (err) {
                console.error('OCR Error:', err);
                // Fail silently on OCR error to avoid blocking users if Tesseract fails
            }

            // Start Chunked Upload
            statusText.innerText = 'Verification passed! Starting upload...';
            statusText.style.color = '#39ff14';

            const CHUNK_SIZE = 256 * 1024;
            const fileId = 'proof_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
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
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route("upload.chunk") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        statusText.innerText = 'Upload failed!';
                        statusText.style.color = '#ff4444';
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
                        statusText.innerText = 'Upload complete! You can now submit.';
                        statusText.style.color = '#39ff14';
                        btnSubmit.disabled = false;
                        proofInput.removeAttribute('required');
                        proofDropzone.style.borderColor = '#39ff14';
                    }
                })
                .catch(err => {
                    statusText.innerText = 'Upload error!';
                    statusText.style.color = '#ff4444';
                    btnSubmit.disabled = false;
                });
            }
            uploadNextChunk();
        }
    });

    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const agreeCheckbox = document.getElementById('i_agree');
        if (agreeCheckbox && !agreeCheckbox.checked) {
            window.neonAlert("You must agree to the manual payment terms before submitting.");
            return;
        }

        window.neonConfirm("Are you sure you want to submit this proof of payment? Please ensure the screenshot clearly shows the transaction reference and amount paid.").then(confirmed => {
            if (confirmed) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> SUBMITTING...';
                uploadForm.submit();
            }
        });
    });
});
</script>

<style>
    .border-dashed {
        border-style: dashed !important;
    }
    .scrollable-agreement::-webkit-scrollbar {
        width: 6px;
    }
    .scrollable-agreement::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.2);
    }
    .scrollable-agreement::-webkit-scrollbar-thumb {
        background: var(--neon-yellow);
        border-radius: 10px;
    }
    .custom-checkbox .form-check-input:checked {
        background-color: var(--neon-yellow);
        border-color: var(--neon-yellow);
        box-shadow: 0 0 10px rgba(255, 221, 0, 0.4);
    }
</style>
@endsection
