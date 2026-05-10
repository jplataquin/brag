@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-5 fw-bold text-uppercase mb-1" style="color: var(--neon-cyan); font-family: 'Orbitron', sans-serif;">
                <i class="bi bi-shield-check"></i> Identity Verifications
            </h1>
            <p class="text-secondary lead mb-0">Review user-submitted documents to verify their identity.</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.verifications.index', ['status' => 'pending']) }}" class="btn btn-{{ $status === 'pending' ? 'neon-cyan' : 'outline-info' }}">Pending</a>
            <a href="{{ route('admin.verifications.index', ['status' => 'approved']) }}" class="btn btn-{{ $status === 'approved' ? 'neon-cyan' : 'outline-info' }}">Approved</a>
            <a href="{{ route('admin.verifications.index', ['status' => 'rejected']) }}" class="btn btn-{{ $status === 'rejected' ? 'neon-cyan' : 'outline-info' }}">Rejected</a>
        </div>
    </div>

    <div class="neon-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead class="bg-black text-uppercase small fw-bold" style="letter-spacing: 1px;">
                    <tr>
                        <th class="ps-4">User</th>
                        <th>Submitted At</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($verifications as $v)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $v->user->avatar_url }}" class="rounded-circle me-3 border border-info border-opacity-25" style="width: 40px; height: 40px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold"><x-username :user="$v->user" /></div>
                                        <div class="small text-muted">{{ $v->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $v->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $v->status === 'pending' ? 'warning text-dark' : ($v->status === 'approved' ? 'success' : 'danger') }} text-uppercase">
                                    {{ $v->status }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.verifications.show', $v->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                    Review Application
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                No {{ $status }} verification requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $verifications->links() }}
    </div>
</div>
@endsection
