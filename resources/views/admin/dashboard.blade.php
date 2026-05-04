@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5 fw-bold text-uppercase" style="color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                <i class="bi bi-speedometer2"></i> System Control
            </h1>
            <p class="text-secondary lead">Overview of platform metrics and administration links.</p>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row g-3 mb-5">
        <div class="col-md-3">
            <a href="{{ route('admin.settings.edit') }}" class="text-decoration-none">
                <div class="card bg-dark bg-opacity-75 border-cyan h-100 neon-hover" style="border-color: var(--neon-cyan);">
                    <div class="card-body text-center p-3">
                        <i class="bi bi-sliders display-6 mb-2 d-block" style="color: var(--neon-cyan);"></i>
                        <h6 class="text-white text-uppercase fw-bold mb-0">Platform Settings</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                <div class="card bg-dark bg-opacity-75 border-yellow h-100 neon-hover" style="border-color: var(--neon-yellow);">
                    <div class="card-body text-center p-3">
                        <i class="bi bi-people-fill display-6 mb-2 d-block" style="color: var(--neon-yellow);"></i>
                        <h6 class="text-white text-uppercase fw-bold mb-0">Manage Citizens</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.payments.index') }}" class="text-decoration-none">
                <div class="card bg-dark bg-opacity-75 border-green h-100 neon-hover" style="border-color: var(--neon-green);">
                    <div class="card-body text-center p-3">
                        <i class="bi bi-cash-coin display-6 mb-2 d-block" style="color: var(--neon-green);"></i>
                        <h6 class="text-white text-uppercase fw-bold mb-0">Sales Transactions</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.diamond-packages.index') }}" class="text-decoration-none">
                <div class="card bg-dark bg-opacity-75 border-magenta h-100 neon-hover" style="border-color: var(--neon-magenta);">
                    <div class="card-body text-center p-3">
                        <i class="bi bi-gem display-6 mb-2 d-block" style="color: var(--neon-magenta);"></i>
                        <h6 class="text-white text-uppercase fw-bold mb-0">Diamond Packages</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.game_titles.index') }}" class="text-decoration-none">
                <div class="card bg-dark bg-opacity-75 border-light h-100 neon-hover">
                    <div class="card-body text-center p-3">
                        <i class="bi bi-controller display-6 mb-2 d-block text-white"></i>
                        <h6 class="text-white text-uppercase fw-bold mb-0">Game Titles</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.templates.index') }}" class="text-decoration-none">
                <div class="card bg-dark bg-opacity-75 border-secondary h-100 neon-hover">
                    <div class="card-body text-center p-3">
                        <i class="bi bi-images display-6 mb-2 d-block text-white"></i>
                        <h6 class="text-white text-uppercase fw-bold mb-0">Templates</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.cards.index') }}" class="text-decoration-none">
                <div class="card bg-dark bg-opacity-75 border-secondary h-100 neon-hover">
                    <div class="card-body text-center p-3">
                        <i class="bi bi-cpu display-6 mb-2 d-block text-white"></i>
                        <h6 class="text-white text-uppercase fw-bold mb-0">Digital Cards</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <div class="dropdown h-100">
                <button class="btn p-0 border-0 w-100 h-100 text-decoration-none text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="card bg-dark bg-opacity-75 border-secondary h-100 neon-hover">
                        <div class="card-body text-center p-3">
                            <i class="bi bi-three-dots display-6 mb-2 d-block text-white"></i>
                            <h6 class="text-white text-uppercase fw-bold mb-0">More Options</h6>
                        </div>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-dark shadow-lg border-info">
                    <li><a class="dropdown-item" href="{{ route('admin.blog.index') }}"><i class="bi bi-megaphone-fill me-2 text-danger"></i> Manage Blog</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.terms.index') }}"><i class="bi bi-file-earmark-text me-2 text-info"></i> Manage Terms</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.privacy.index') }}"><i class="bi bi-shield-shaded me-2 text-info"></i> Manage Privacy Policy</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card h-100 bg-dark border-0 rounded-4 overflow-hidden position-relative" style="box-shadow: 0 0 15px rgba(0, 240, 255, 0.1);">
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(0, 240, 255, 0.1) 0%, transparent 100%); pointer-events: none;"></div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center position-relative z-1 py-5">
                    <i class="bi bi-people-fill display-4 mb-3 text-cyan" style="color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5);"></i>
                    <h2 class="display-5 fw-bold mb-0 text-white" style="font-family: 'Orbitron', sans-serif;">{{ number_format($stats['total_users']) }}</h2>
                    <p class="text-uppercase small fw-bold text-muted mt-2 tracking-wide mb-0">Total Citizens</p>
                </div>
                <div class="position-absolute bottom-0 start-0 w-100" style="height: 4px; background: var(--neon-cyan); box-shadow: 0 -2px 10px rgba(0, 240, 255, 0.5);"></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100 bg-dark border-0 rounded-4 overflow-hidden position-relative" style="box-shadow: 0 0 15px rgba(255, 0, 255, 0.1);">
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(255, 0, 255, 0.1) 0%, transparent 100%); pointer-events: none;"></div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center position-relative z-1 py-5">
                    <i class="bi bi-controller display-4 mb-3" style="color: var(--neon-magenta); text-shadow: 0 0 10px rgba(255, 0, 255, 0.5);"></i>
                    <h2 class="display-5 fw-bold mb-0 text-white" style="font-family: 'Orbitron', sans-serif;">
                        {{ number_format($stats['active_battles']) }} <span class="fs-5 text-muted fw-normal">/ {{ number_format($stats['total_battles']) }}</span>
                    </h2>
                    <p class="text-uppercase small fw-bold text-muted mt-2 tracking-wide mb-0">Active Battles</p>
                </div>
                <div class="position-absolute bottom-0 start-0 w-100" style="height: 4px; background: var(--neon-magenta); box-shadow: 0 -2px 10px rgba(255, 0, 255, 0.5);"></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100 bg-dark border-0 rounded-4 overflow-hidden position-relative" style="box-shadow: 0 0 15px rgba(57, 255, 20, 0.1);">
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(57, 255, 20, 0.1) 0%, transparent 100%); pointer-events: none;"></div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center position-relative z-1 py-5">
                    <i class="bi bi-images display-4 mb-3" style="color: var(--neon-green); text-shadow: 0 0 10px rgba(57, 255, 20, 0.5);"></i>
                    <h2 class="display-5 fw-bold mb-0 text-white" style="font-family: 'Orbitron', sans-serif;">{{ number_format($stats['total_templates']) }}</h2>
                    <p class="text-uppercase small fw-bold text-muted mt-2 tracking-wide mb-0">Total Templates</p>
                </div>
                <div class="position-absolute bottom-0 start-0 w-100" style="height: 4px; background: var(--neon-green); box-shadow: 0 -2px 10px rgba(57, 255, 20, 0.5);"></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100 bg-dark border-0 rounded-4 overflow-hidden position-relative" style="box-shadow: 0 0 15px rgba(255, 221, 0, 0.1);">
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(255, 221, 0, 0.1) 0%, transparent 100%); pointer-events: none;"></div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center position-relative z-1 py-5">
                    <i class="bi bi-cpu-fill display-4 mb-3" style="color: var(--neon-yellow); text-shadow: 0 0 10px rgba(255, 221, 0, 0.5);"></i>
                    <h2 class="display-5 fw-bold mb-0 text-white" style="font-family: 'Orbitron', sans-serif;">{{ number_format($stats['total_cards']) }}</h2>
                    <p class="text-uppercase small fw-bold text-muted mt-2 tracking-wide mb-0">Cards Forged</p>
                </div>
                <div class="position-absolute bottom-0 start-0 w-100" style="height: 4px; background: var(--neon-yellow); box-shadow: 0 -2px 10px rgba(255, 221, 0, 0.5);"></div>
            </div>
        </div>
    </div>

    <!-- Recent Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-dark bg-opacity-75 border-info rounded-4" style="backdrop-filter: blur(10px);">
                <div class="card-header border-info border-bottom p-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-uppercase fw-bold text-white" style="font-family: 'Orbitron', sans-serif;">
                        <i class="bi bi-person-plus-fill me-2" style="color: var(--neon-cyan);"></i> Recently Joined Citizens
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold px-4 py-3">Citizen</th>
                                    <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Joined Date</th>
                                    <th scope="col" class="bg-transparent text-muted small text-uppercase fw-bold py-3">Admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUsers as $user)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $user->avatar_url }}" alt="{{ $user->username }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid {{ $user->is_admin ? 'var(--neon-magenta)' : 'var(--neon-cyan)' }};">
                                                <div>
                                                    <div class="fw-bold text-white">{{ $user->username }}</div>
                                                    <div class="text-muted small">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 text-secondary">
                                            {{ $user->created_at->format('M j, Y H:i') }}
                                        </td>
                                        <td class="py-3">
                                            @if($user->is_admin)
                                                <span class="badge rounded-pill bg-danger text-uppercase tracking-wide" style="box-shadow: 0 0 5px rgba(255,0,0,0.5);">Yes</span>
                                            @else
                                                <span class="badge rounded-pill bg-secondary bg-opacity-50 text-uppercase tracking-wide">No</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">No citizens found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tracking-wide { letter-spacing: 1px; }
    .neon-hover {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .neon-hover:hover {
        transform: translateY(-5px);
        background-color: rgba(255, 255, 255, 0.05) !important;
        box-shadow: 0 5px 15px rgba(0, 240, 255, 0.2);
    }
</style>
@endsection
