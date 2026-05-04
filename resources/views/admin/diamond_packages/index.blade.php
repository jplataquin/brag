@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-5 fw-bold text-uppercase mb-1" style="color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                <i class="bi bi-gem"></i> Diamond Packages
            </h1>
            <p class="text-secondary lead mb-0">Manage diamond purchasing packages and QR codes.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Actions & Alerts -->
    <div class="mb-4">
        <a href="{{ route('admin.diamond-packages.create') }}" class="btn btn-neon-cyan">
            <i class="bi bi-plus-lg"></i> Add New Package
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Packages Table -->
    <div class="card bg-dark bg-opacity-75 border-info rounded-4 shadow-lg" style="backdrop-filter: blur(10px);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">ID</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3 text-start">Package Name</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Diamonds</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Price</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Promo</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Status</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Methods</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">QR</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packages as $package)
                            <tr>
                                <td class="py-3 text-white-50">#{{ $package->id }}</td>
                                <td class="py-3 text-start">
                                    <span class="fw-bold text-white fs-6">{{ $package->name }}</span>
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-info rounded-pill px-3">{{ $package->diamonds }}</span>
                                </td>
                                <td class="py-3">
                                    {{ $package->currency }} {{ number_format($package->price, 2) }}
                                </td>
                                <td class="py-3">
                                    @if($package->promo_price)
                                        <span class="text-warning fw-bold">{{ $package->currency }} {{ number_format($package->promo_price, 2) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @if($package->is_active)
                                        <span class="badge bg-success rounded-pill px-3">Active</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-3">Hidden</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <div class="d-flex flex-column gap-1 align-items-center">
                                        @if($package->allow_hitpay)
                                            <span class="badge bg-primary small" style="font-size: 0.7em;">HitPay</span>
                                        @endif
                                        @if($package->allow_manual)
                                            <span class="badge bg-warning text-dark small" style="font-size: 0.7em;">Manual</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3">
                                    @if($package->qr_path)
                                        <i class="bi bi-qr-code text-success" title="QR Code Uploaded"></i>
                                    @else
                                        <i class="bi bi-x-circle text-danger" title="No QR Code"></i>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.diamond-packages.edit', $package->id) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.diamond-packages.destroy', $package->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this package?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-5 text-muted text-center">No diamond packages found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
