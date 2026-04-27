@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-5 fw-bold text-uppercase mb-1" style="color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                <i class="bi bi-people-fill"></i> Citizen Registry
            </h1>
            <p class="text-secondary lead mb-0">Manage platform users, roles, and suspensions.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Actions & Alerts -->
    <div class="d-flex justify-content-end mb-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex gap-2 w-50">
            <input type="text" name="search" class="form-control bg-dark text-white border-info" placeholder="Search Username, Email, First/Last Name..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-info px-4">
                <i class="bi bi-search"></i>
            </button>
            @if(request()->has('search'))
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </form>
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

    <!-- Users Table -->
    <div class="card bg-dark bg-opacity-75 border-info rounded-4 shadow-lg" style="backdrop-filter: blur(10px);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">ID</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3 text-start">Citizen</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Role</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Status</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Joined</th>
                            <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="py-3 text-white-50">#{{ $user->id }}</td>
                                <td class="py-3 text-start">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $user->avatar_url }}" alt="{{ $user->username }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid {{ $user->is_admin ? 'var(--neon-magenta)' : 'var(--neon-cyan)' }};">
                                        <div>
                                            <div class="fw-bold text-white fs-6">{{ $user->username }}</div>
                                            <div class="text-muted small">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    @if($user->is_admin)
                                        <span class="badge bg-danger rounded-pill px-3" style="box-shadow: 0 0 5px rgba(255,0,0,0.5);">Admin</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-3">Citizen</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @if($user->isSuspended())
                                        <span class="badge bg-warning text-dark rounded-pill px-3">
                                            Suspended until {{ $user->suspended_until->format('M j, Y') }}
                                        </span>
                                    @else
                                        <span class="badge bg-success rounded-pill px-3">Active</span>
                                    @endif
                                </td>
                                <td class="py-3 text-white-50">
                                    {{ $user->created_at->format('M j, Y') }}
                                </td>
                                <td class="py-3">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                        <i class="bi bi-pencil"></i> Manage
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-5 text-muted text-center">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($users->hasPages())
            <div class="card-footer border-info border-top p-3 d-flex justify-content-center bg-transparent">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
