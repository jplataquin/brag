@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="display-5 fw-bold text-uppercase" style="color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                <i class="bi bi-file-earmark-text"></i> Cron Logs
            </h1>
            <p class="text-secondary lead">View execution history from <code>storage/logs/cron.log</code>.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.cron.index') }}" class="btn btn-outline-cyan">
                <i class="bi bi-arrow-left"></i> Back to Cron Jobs
            </a>
        </div>
    </div>

    <div class="card bg-dark bg-opacity-75 border-cyan shadow-lg" style="border-color: rgba(0, 240, 255, 0.3);">
        <div class="card-body p-0">
            <pre class="m-0 p-4 text-light bg-black bg-opacity-50" style="font-family: 'Courier New', Courier, monospace; font-size: 0.9rem; max-height: 700px; overflow-y: auto; white-space: pre-wrap;">{{ $logs }}</pre>
        </div>
        <div class="card-footer bg-transparent border-top border-cyan border-opacity-25 py-3">
            <small class="text-secondary">
                <i class="bi bi-info-circle"></i> Logs are appended automatically on each task execution. This view shows up to the last 50,000 characters.
            </small>
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
    .border-cyan {
        border-color: var(--neon-cyan) !important;
    }
</style>
@endsection
