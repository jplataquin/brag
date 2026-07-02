@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-5 fw-bold text-uppercase mb-1" style="color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                <i class="bi bi-file-earmark-lock"></i> Manual Agreements
            </h1>
            <p class="text-secondary lead mb-0">Manage the disclaimers users must agree to for manual payments.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Actions & Alerts -->
    <div class="mb-4">
        <a href="{{ route('admin.manual-payment-agreements.create') }}" class="btn btn-neon-cyan">
            <i class="bi bi-plus-lg"></i> Create New Agreement
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card bg-dark bg-opacity-75 border-info rounded-4 shadow-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3 px-4">Created Date</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Status</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Snippet</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3 text-end px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agreements as $index => $agreement)
                            <tr>
                                <td class="py-3 px-4 text-white-50">
                                    {{ $agreement->created_at->format('M d, Y h:i A') }}
                                </td>
                                <td class="py-3">
                                    @if($index === 0 && $agreements->currentPage() === 1)
                                        <span class="badge bg-success rounded-pill px-3"><i class="bi bi-check-circle me-1"></i> ACTIVE</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-3">PREVIOUS</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <div class="text-truncate text-white" style="max-width: 400px;">
                                        {{ Str::limit(html_entity_decode(strip_tags($agreement->content), ENT_QUOTES, 'UTF-8'), 80) }}
                                    </div>
                                </td>
                                <td class="py-3 text-end px-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.manual-payment-agreements.edit', $agreement->id) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        @if(!$agreement->payments()->exists())
                                            <form action="{{ route('admin.manual-payment-agreements.destroy', $agreement->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this agreement?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-5 text-muted text-center">No manual agreements found. The first one you create will be active.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="mt-4 d-flex justify-content-center">
        {{ $agreements->links() }}
    </div>
</div>
@endsection
