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

                    <form action="{{ route('payments.manual.proof', $package->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4 text-start">
                            <label for="proof" class="form-label text-muted small text-uppercase fw-bold">Upload Proof of Payment (Screenshot)</label>
                            <input type="file" name="proof" id="proof" class="form-control bg-dark text-white border-warning @error('proof') is-invalid @enderror" accept="image/*" required>
                            @error('proof')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-lg btn-warning fw-bold text-dark" style="box-shadow: 0 0 15px rgba(255, 221, 0, 0.4);">
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

<style>
    .border-dashed {
        border-style: dashed !important;
    }
</style>
@endsection
