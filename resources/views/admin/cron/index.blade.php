@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="display-5 fw-bold text-uppercase" style="color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                <i class="bi bi-clock-history"></i> Cron Jobs
            </h1>
            <p class="text-secondary lead">Monitor and execute scheduled system tasks.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.cron.logs') }}" class="btn btn-outline-cyan">
                <i class="bi bi-file-earmark-text"></i> View Execution Logs
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="card bg-dark bg-opacity-75 border-cyan shadow-lg mb-4" style="border-color: rgba(0, 240, 255, 0.3);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead style="background: rgba(0, 240, 255, 0.1);">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase" style="font-family: 'Orbitron', sans-serif; color: var(--neon-cyan); font-size: 0.8rem;">Command</th>
                            <th class="py-3 text-uppercase" style="font-family: 'Orbitron', sans-serif; color: var(--neon-cyan); font-size: 0.8rem;">Description</th>
                            <th class="py-3 text-uppercase" style="font-family: 'Orbitron', sans-serif; color: var(--neon-cyan); font-size: 0.8rem;">Schedule</th>
                            <th class="py-3 text-uppercase" style="font-family: 'Orbitron', sans-serif; color: var(--neon-cyan); font-size: 0.8rem;">Next Run</th>
                            <th class="pe-4 py-3 text-end text-uppercase" style="font-family: 'Orbitron', sans-serif; color: var(--neon-cyan); font-size: 0.8rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                            <tr>
                                <td class="ps-4">
                                    <code class="text-info fw-bold" style="font-size: 0.9rem;">{{ $event['command'] }}</code>
                                </td>
                                <td>
                                    <span class="text-secondary" style="font-size: 0.85rem;">{{ $event['description'] ?: 'No description provided.' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary opacity-75" title="Cron Expression">{{ $event['expression'] }}</span>
                                </td>
                                <td>
                                    <span class="text-light" style="font-size: 0.85rem;">{{ $event['next_run_at'] }}</span>
                                </td>
                                <td class="pe-4 text-end">
                                    <form action="{{ route('admin.cron.run') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="command" value="{{ $event['command'] }}">
                                        <button type="submit" class="btn btn-sm btn-cyan neon-hover px-3" onclick="return confirm('Execute this command now?')">
                                            <i class="bi bi-play-fill"></i> Run Now
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-secondary">
                                    <i class="bi bi-info-circle mb-2 d-block fs-2"></i>
                                    No scheduled tasks found in <code>routes/console.php</code>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-outline-cyan {
        color: var(--neon-cyan);
        border-color: var(--neon-cyan);
    }
    .btn-outline-cyan:hover {
        background-color: var(--neon-cyan);
        color: #000;
        box-shadow: 0 0 15px var(--neon-cyan);
    }
    .btn-cyan {
        background-color: var(--neon-cyan);
        color: #000;
        border: none;
        font-weight: bold;
    }
    .btn-cyan:hover {
        background-color: #00cce0;
        box-shadow: 0 0 15px var(--neon-cyan);
    }
    .border-cyan {
        border-color: var(--neon-cyan) !important;
    }
</style>
@endsection
