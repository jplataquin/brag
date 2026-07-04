@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="display-6 fw-bold text-uppercase mb-1" style="color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                        <i class="bi bi-receipt"></i> Payment Details
                    </h1>
                    <p class="text-secondary mb-0">Ref: {{ $payment->reference }}</p>
                </div>
                <a href="{{ route('wallet.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Wallet
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success p-2 small mb-4"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger p-2 small mb-4"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
            @endif

            <!-- Payment Status Card -->
            <div class="card bg-dark border-info rounded-4 overflow-hidden mb-4 shadow-lg">
                <div class="card-header border-info p-3 bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-uppercase fw-bold text-white" style="font-family: 'Orbitron', sans-serif;">Request Summary</h5>
                    @if($payment->status === 'completed')
                        <span class="badge bg-success rounded-pill px-3">Completed</span>
                    @elseif($payment->status === 'pending')
                        <span class="badge bg-warning text-dark rounded-pill px-3">Pending Review</span>
                    @elseif($payment->status === 'flagged')
                        <span class="badge bg-danger rounded-pill px-3" style="background-color: #ff8c00 !important; border-color: #ff8c00 !important; color: #fff !important;">Flagged for Review</span>
                    @else
                        <span class="badge bg-danger rounded-pill px-3">{{ strtoupper($payment->status) }}</span>
                    @endif
                </div>
                <div class="card-body p-4 text-center">
                    <div class="row g-3">
                        <div class="col-sm-6 text-start">
                            <div class="text-muted small text-uppercase fw-bold">Package</div>
                            <div class="text-white fs-5 fw-bold">{{ $payment->package->name ?? 'Custom Package' }}</div>
                        </div>
                        <div class="col-sm-6 text-end">
                            <div class="text-muted small text-uppercase fw-bold">Amount Paid</div>
                            <div class="text-info fs-5 fw-bold">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</div>
                        </div>
                    </div>

                    @if($payment->proof_path)
                        <div class="mt-4 pt-4 border-top border-secondary">
                            <div class="text-muted small text-uppercase fw-bold mb-2">Current Uploaded Proof</div>
                            <img src="{{ asset('storage/' . $payment->proof_path) }}" alt="Proof" class="img-fluid rounded-3 border border-secondary shadow-sm" style="max-height: 300px;">
                        </div>
                    @endif
                </div>
            </div>

            <!-- Agreement Summary -->
            @if($payment->agreement)
                <div class="card bg-dark border-secondary rounded-4 overflow-hidden mb-4 opacity-75">
                    <div class="card-body p-3 small text-secondary">
                        <i class="bi bi-info-circle me-1"></i> You agreed to the manual payment terms on {{ $payment->created_at->format('M d, Y') }}.
                    </div>
                </div>
            @endif

            <!-- Discussion Thread -->
            <div class="card bg-dark border-info rounded-4 overflow-hidden mb-5 shadow-lg">
                <div class="card-header border-info p-3 bg-transparent">
                    <h5 class="mb-0 text-uppercase fw-bold text-white" style="font-family: 'Orbitron', sans-serif;">
                        <i class="bi bi-chat-dots me-2 text-info"></i> Discussion Thread
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="comments-list mb-4" style="max-height: 500px; overflow-y: auto;">
                        @forelse($payment->comments as $comment)
                            <div class="mb-3 p-3 rounded-3 {{ $comment->user->is_admin ? 'bg-info bg-opacity-10 border border-info me-5' : 'bg-secondary bg-opacity-10 border border-secondary ms-5' }}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold {{ $comment->user->is_admin ? 'text-info' : 'text-white' }}">
                                        <i class="bi {{ $comment->user->is_admin ? 'bi-shield-check' : 'bi-person' }} me-1"></i>
                                        {{ $comment->user->is_admin ? 'Brag Administrator' : 'You' }}
                                    </span>
                                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="text-white">{{ $comment->comment }}</div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">No messages yet. Our team will comment here if there are issues with your proof.</div>
                        @endforelse
                    </div>

                    @if($payment->status !== 'completed')
                        <!-- Re-upload Section for Flagged Payments -->
                        @if($payment->status === 'flagged')
                            <div class="mt-2 mb-5 p-4 border border-warning rounded-4 bg-warning bg-opacity-5">
                                <h5 class="text-warning text-uppercase fw-bold mb-3" style="font-family: 'Orbitron', sans-serif;">
                                    <i class="bi bi-cloud-arrow-up me-2"></i> Action Required: Re-upload Proof
                                </h5>
                                <p class="text-secondary small mb-4">Please upload a clearer screenshot based on the administrator's feedback above.</p>
                                
                                <form id="proof-upload-form" action="{{ route('payments.reupload', $payment->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="proof_temp_path" id="proof_temp_path">
                                    
                                    <div class="mb-0 text-start">
                                        <div id="proof-upload-wrapper">
                                            <x-file-input 
                                                id="proof" 
                                                name="proof"
                                                accept="image/*" 
                                                placeholder="Select New Screenshot" 
                                                color="warning" 
                                                icon="bi-receipt-cutoff" 
                                                required 
                                            />
                                        </div>

                                        <!-- Photo Preview -->
                                        <div id="proof-preview" class="mt-3 text-center" style="display: none;">
                                            <img id="preview-img" src="" alt="Preview" class="img-fluid rounded-3 border border-warning" style="max-height: 150px;">
                                            <div class="mt-2">
                                                <button type="button" id="btn-reset-proof" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-0" style="font-size: 0.7rem;">
                                                    <i class="bi bi-trash"></i> Reset
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Progress Bar -->
                                        <div id="upload-progress-container" class="mt-3" style="display: none;">
                                            <div class="progress bg-dark border border-warning" style="height: 10px; border-radius: 5px;">
                                                <div id="upload-progress-bar" class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                                            </div>
                                            <div id="upload-status" class="x-small text-warning mt-1 text-center fw-bold" style="line-height: 1.2;">Uploading: 0%</div>
                                        </div>
                                    </div>

                                    <div class="d-grid mt-4">
                                        <button type="submit" id="btn-submit-proof" class="btn btn-warning fw-bold text-dark" disabled>
                                            <i class="bi bi-check2-circle me-1"></i> Confirm Re-upload
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif

                        <form action="{{ route('payments.comments.store', $payment->id) }}" method="POST" id="replyForm">
                            @csrf
                            <div class="mb-3">
                                <label for="comment" class="form-label text-muted small text-uppercase fw-bold">Post a Reply</label>
                                <textarea name="comment" id="comment" rows="3" class="form-control bg-dark text-white border-info" required placeholder="Write your message to the administrator..."></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" id="submitBtn" class="btn btn-neon fw-bold">
                                    <i class="bi bi-send me-1"></i> Send Reply
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('replyForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> SENDING...';
        });
    }

    // Re-upload Logic (Only if element exists)
    const proofInput = document.getElementById('proof');
    if (proofInput) {
        const proofDropzone = document.getElementById('proof-dropzone');
        const btnSubmitProof = document.getElementById('btn-submit-proof');
        const progressContainer = document.getElementById('upload-progress-container');
        const progressBar = document.getElementById('upload-progress-bar');
        const statusText = document.getElementById('upload-status');
        const tempInput = document.getElementById('proof_temp_path');
        const uploadForm = document.getElementById('proof-upload-form');
        const previewContainer = document.getElementById('proof-preview');
        const previewImg = document.getElementById('preview-img');
        const btnReset = document.getElementById('btn-reset-proof');
        const proofUploadWrapper = document.getElementById('proof-upload-wrapper');

        const expectedAmount = "{{ number_format($payment->amount, 2, '.', '') }}";
        const requiredOcrPatterns = @json(array_values(array_filter(array_map('trim', explode('&&', $payment->package->ocr_match_string ?? '')))));

        btnReset.addEventListener('click', function() {
            proofInput.value = '';
            tempInput.value = '';
            previewImg.src = '';
            previewContainer.style.display = 'none';
            progressContainer.style.display = 'none';
            proofUploadWrapper.style.display = 'block';
            proofInput.setAttribute('required', 'required');
            btnSubmitProof.disabled = true;
        });

        proofInput.addEventListener('change', async function() {
            const file = this.files[0];
            if (file) {
                proofUploadWrapper.style.display = 'none';
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImg.src = e.target.result;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);

                btnSubmitProof.disabled = true;
                progressContainer.style.display = 'block';
                progressBar.style.width = '0%';
                statusText.innerText = 'Initializing scanner...';
                statusText.style.color = '#ffdd00';

                const img = new Image();
                img.src = URL.createObjectURL(file);
                await img.decode();

                const canvas = document.createElement('canvas');
                const maxAnalysisSize = 600;
                const scale = Math.min(maxAnalysisSize / img.width, maxAnalysisSize / img.height, 1);
                canvas.width = img.width * scale;
                canvas.height = img.height * scale;
                canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);

                // OCR Check
                statusText.innerText = 'Scanning receipt...';
                try {
                    const { data: { text } } = await Tesseract.recognize(canvas, 'eng');
                    console.log('[OCR Debug] Raw Extracted Text:', text);
                    
                    const sanitizedText = text.replace(/[\s,]/g, '').toLowerCase();
                    console.log('[OCR Debug] Sanitized Extracted Text:', sanitizedText);
                    
                    const searchAmount = expectedAmount.replace(/[\s,]/g, '');
                    const searchAmountInt = parseInt(searchAmount);
                    console.log('[OCR Debug] Amount Check - Expected:', searchAmount, 'or integer:', searchAmountInt.toString());

                    const hasAmount = sanitizedText.includes(searchAmount) || sanitizedText.includes(searchAmountInt.toString());
                    console.log('[OCR Debug] Amount Check Result:', hasAmount ? 'PASSED' : 'FAILED');

                    if (!hasAmount) {
                        statusText.innerHTML = 'Error: Payment details or amount could not be verified in the screenshot. <br><small class="text-secondary">Please ensure the image is clear and clearly shows the payment confirmation and transaction amount.</small>';
                        statusText.style.color = '#ff4444';
                        return;
                    }

                    if (requiredOcrPatterns && requiredOcrPatterns.length > 0) {
                        console.log('[OCR Debug] Required Patterns list:', requiredOcrPatterns);
                        for (let currentPattern of requiredOcrPatterns) {
                            const processedPattern = currentPattern.replace(/[\s,]/g, '');
                            console.log('[OCR Debug] Pattern Check - Original:', currentPattern, '| Processed (no spaces/commas):', processedPattern);

                            let patternPassed = false;
                            try {
                                const regex = new RegExp(processedPattern, 'i');
                                patternPassed = regex.test(sanitizedText);
                                console.log('[OCR Debug] Regex verification used:', regex, '| Result:', patternPassed ? 'PASSED' : 'FAILED');
                                
                                if (!patternPassed) {
                                    statusText.innerHTML = 'Error: Required pattern <strong>"' + currentPattern + '"</strong> not found. <br><small class="text-secondary">Please ensure all details are visible.</small>';
                                    statusText.style.color = '#ff4444';
                                    return;
                                }
                            } catch (e) {
                                console.warn('[OCR Debug] Invalid Regex - Falling back to text inclusion check', e);
                                const searchString = processedPattern.toLowerCase();
                                patternPassed = sanitizedText.includes(searchString);
                                console.log('[OCR Debug] Fallback text inclusion check for:', searchString, '| Result:', patternPassed ? 'PASSED' : 'FAILED');
                                
                                if (!patternPassed) {
                                    statusText.innerHTML = 'Error: Required text <strong>"' + currentPattern + '"</strong> not found.';
                                    statusText.style.color = '#ff4444';
                                    return;
                                }
                            }
                        }
                    }

                    // OCR successfully verified the details!
                    statusText.innerText = 'Verification passed! Uploading...';
                    statusText.style.color = '#39ff14';

                } catch (err) { 
                    console.error(err); 
                    statusText.innerText = 'OCR scanner offline. Bypassing check... Starting upload...';
                    statusText.style.color = '#ffaa00';
                }

                const CHUNK_SIZE = 256 * 1024;
                const fileId = 'proof_re_' + Date.now();
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

                    fetch('{{ route("upload.chunk") }}', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        chunkIndex++;
                        const percent = Math.round((chunkIndex / totalChunks) * 100);
                        progressBar.style.width = percent + '%';
                        statusText.innerText = 'Uploading: ' + percent + '%';

                        if (chunkIndex < totalChunks) {
                            uploadNextChunk();
                        } else {
                            tempInput.value = data.path;
                            statusText.innerText = 'Upload complete!';
                            btnSubmitProof.disabled = false;
                        }
                    })
                    .catch(() => {
                        statusText.innerText = 'Upload failed!';
                        statusText.style.color = '#ff4444';
                    });
                }
                uploadNextChunk();
            }
        });

        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            window.neonConfirm("Are you sure you want to RE-SUBMIT this proof? This will replace your previous upload.").then(confirmed => {
                if (confirmed) {
                    btnSubmitProof.disabled = true;
                    btnSubmitProof.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> SUBMITTING...';
                    uploadForm.submit();
                }
            });
        });
    }
});
</script>
@endsection
