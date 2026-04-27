@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-5 fw-bold text-uppercase mb-1" style="color: var(--neon-magenta); text-shadow: 0 0 10px rgba(255, 0, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                <i class="bi bi-person-gear"></i> Manage Citizen
            </h1>
            <p class="text-secondary lead mb-0">Update details and account status for {{ $user->username }}.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left"></i> Back to Registry
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <!-- User Summary Sidebar -->
            <div class="card bg-dark bg-opacity-75 border-info rounded-4 shadow-lg h-100" style="backdrop-filter: blur(10px);">
                <div class="card-body text-center p-4">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->username }}" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid {{ $user->is_admin ? 'var(--neon-magenta)' : 'var(--neon-cyan)' }}; box-shadow: 0 0 15px {{ $user->is_admin ? 'rgba(255,0,255,0.5)' : 'rgba(0,240,255,0.5)' }};">
                    <h3 class="text-white fw-bold mb-1">{{ $user->username }}</h3>
                    <p class="text-muted mb-3">{{ $user->email }}</p>

                    @if($user->is_admin)
                        <span class="badge bg-danger rounded-pill px-3 py-2 mb-3" style="box-shadow: 0 0 5px rgba(255,0,0,0.5);">Administrator</span>
                    @else
                        <span class="badge bg-secondary rounded-pill px-3 py-2 mb-3">Citizen</span>
                    @endif

                    @if($user->isSuspended())
                        <div class="alert alert-warning border-warning p-2 small mt-2">
                            <i class="bi bi-exclamation-triangle-fill"></i> Suspended until<br>
                            <strong>{{ $user->suspended_until->format('M j, Y g:i A') }}</strong>
                        </div>
                    @else
                        <div class="alert alert-success border-success p-2 small mt-2 bg-success bg-opacity-10">
                            <i class="bi bi-check-circle-fill"></i> Account Active
                        </div>
                    @endif
                    
                    <hr class="border-info my-4">

                    <div class="text-start">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Joined:</span>
                            <span class="text-white small">{{ $user->created_at->format('M j, Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Shards:</span>
                            <span class="text-white fw-bold" style="color: var(--neon-magenta) !important;">
                                <i class="bi bi-gem"></i> {{ number_format($user->shards_balance) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card bg-dark bg-opacity-75 border-info rounded-4 shadow-lg" style="backdrop-filter: blur(10px);">
                <div class="card-body p-4">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <h5 class="text-uppercase fw-bold text-white mb-4" style="font-family: 'Orbitron', sans-serif;">
                            <i class="bi bi-person-lines-fill" style="color: var(--neon-cyan);"></i> Profile Details
                        </h5>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small text-uppercase fw-bold">First Name</label>
                                <input type="text" name="firstname" class="form-control bg-dark text-white border-info @error('firstname') is-invalid @enderror" value="{{ old('firstname', $user->firstname) }}">
                                @error('firstname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small text-uppercase fw-bold">Last Name</label>
                                <input type="text" name="lastname" class="form-control bg-dark text-white border-info @error('lastname') is-invalid @enderror" value="{{ old('lastname', $user->lastname) }}">
                                @error('lastname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small text-uppercase fw-bold">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control bg-dark text-white border-info @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required>
                                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small text-uppercase fw-bold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control bg-dark text-white border-info @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <hr class="border-info my-4">

                        <h5 class="text-uppercase fw-bold text-white mb-4" style="font-family: 'Orbitron', sans-serif;">
                            <i class="bi bi-shield-lock-fill text-danger"></i> Administrative Controls
                        </h5>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small text-uppercase fw-bold">Account Role</label>
                                <select name="is_admin" class="form-select bg-dark text-white border-info" {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                                    <option value="0" {{ old('is_admin', $user->is_admin) == 0 ? 'selected' : '' }}>Citizen (Standard User)</option>
                                    <option value="1" {{ old('is_admin', $user->is_admin) == 1 ? 'selected' : '' }}>Administrator</option>
                                </select>
                                @if(auth()->id() === $user->id)
                                    <div class="form-text text-warning mt-1">You cannot change your own role.</div>
                                    <input type="hidden" name="is_admin" value="{{ $user->is_admin }}">
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small text-uppercase fw-bold">Suspend Account Until</label>
                                <input type="datetime-local" name="suspended_until" class="form-control bg-dark text-white border-info @error('suspended_until') is-invalid @enderror" value="{{ old('suspended_until', $user->suspended_until ? $user->suspended_until->format('Y-m-d\TH:i') : '') }}" {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                                @error('suspended_until')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text text-secondary mt-1">Clear this field to lift a suspension immediately.</div>
                                @if(auth()->id() === $user->id)
                                    <div class="form-text text-warning mt-1">You cannot suspend your own account.</div>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="submit" class="btn btn-lg fw-bold text-white px-5" style="background-color: var(--neon-magenta); border-color: var(--neon-magenta); box-shadow: 0 0 15px rgba(255, 0, 255, 0.5);">
                                <i class="bi bi-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
