@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-5 fw-bold text-uppercase mb-1" style="color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                <i class="bi bi-plus-circle"></i> New Agreement
            </h1>
            <p class="text-secondary lead mb-0">Create a new disclaimer/terms for manual payments.</p>
        </div>
        <a href="{{ route('admin.manual-payment-agreements.index') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card bg-dark bg-opacity-75 border-info rounded-4 p-4 shadow-lg" style="backdrop-filter: blur(10px);">
                <div class="card-body">
                    <form action="{{ route('admin.manual-payment-agreements.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="content" class="form-label text-muted small text-uppercase fw-bold">Agreement Content <span class="text-danger">*</span></label>
                            <textarea name="content" id="content" rows="12" class="form-control bg-dark text-white border-info @error('content') is-invalid @enderror" required placeholder="Enter the terms, conditions, and disclaimers users must agree to before submitting manual payments...">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-secondary mt-2">
                                <i class="bi bi-info-circle me-1"></i> Once saved, this agreement becomes the <strong>active</strong> version displayed to all users.
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-5">
                            <button type="submit" class="btn btn-lg btn-neon-cyan fw-bold">
                                <i class="bi bi-check-lg"></i> Save & Activate Agreement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
