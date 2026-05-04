@extends('layouts.app')

@section('content')
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

                    <form id="proof-upload-form" action="{{ route('payments.manual.proof', $package->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="proof_temp_path" id="proof_temp_path">
                        
                        <div class="mb-4 text-start">
                            <label for="proof" class="form-label text-muted small text-uppercase fw-bold">Upload Proof of Payment (Screenshot)</label>
                            <input type="file" name="proof" id="proof" class="form-control bg-dark text-white border-warning @error('proof') is-invalid @enderror" accept="image/*" required>
                            @error('proof')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <!-- Progress Bar -->
                            <div id="upload-progress-container" class="mt-3" style="display: none;">
                                <div class="progress bg-dark border border-warning" style="height: 10px;">
                                    <div id="upload-progress-bar" class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                                </div>
                                <div id="upload-status" class="small text-warning mt-1 text-center">Uploading: 0%</div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" id="btn-submit-proof" class="btn btn-lg btn-warning fw-bold text-dark" style="box-shadow: 0 0 15px rgba(255, 221, 0, 0.4);">
                                <i class="bi bi-cloud-upload me-2"></i> Submit Proof
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card bg-dark bg-opacity-50 border-info rounded-4 border-dashed">
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
    const btnSubmit = document.getElementById('btn-submit-proof');
    const progressContainer = document.getElementById('upload-progress-container');
    const progressBar = document.getElementById('upload-progress-bar');
    const statusText = document.getElementById('upload-status');
    const tempInput = document.getElementById('proof_temp_path');
    const uploadForm = document.getElementById('proof-upload-form');

    proofInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            btnSubmit.disabled = true;
            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';
            statusText.innerText = 'Uploading: 0%';
            statusText.style.color = '#ffdd00';

            const CHUNK_SIZE = 256 * 1024; // 256KB
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
                        // Once we have a temp path, the actual file upload isn't required by the backend
                        proofInput.removeAttribute('required');
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

    uploadForm.addEventListener('submit', function() {
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> SUBMITTING...';
    });
});
</script>

<style>
    .border-dashed {
        border-style: dashed !important;
    }
</style>
@endsection
