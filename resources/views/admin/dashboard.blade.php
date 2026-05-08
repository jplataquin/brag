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
            <a href="{{ route('admin.cron.index') }}" class="text-decoration-none">
                <div class="card bg-dark bg-opacity-75 border-cyan h-100 neon-hover" style="border-color: var(--neon-cyan);">
                    <div class="card-body text-center p-3">
                        <i class="bi bi-clock-history display-6 mb-2 d-block" style="color: var(--neon-cyan);"></i>
                        <h6 class="text-white text-uppercase fw-bold mb-0">Cron Jobs</h6>
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
            <a href="{{ route('admin.card_reports.index') }}" class="text-decoration-none">
                <div class="card bg-dark bg-opacity-75 border-danger h-100 neon-hover" style="border-color: #ff4444;">
                    <div class="card-body text-center p-3">
                        <div class="position-relative d-inline-block">
                            <i class="bi bi-shield-exclamation display-6 mb-2 d-block" style="color: #ff4444;"></i>
                            @if($stats['pending_reports'] > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                    {{ $stats['pending_reports'] }}
                                </span>
                            @endif
                        </div>
                        <h6 class="text-white text-uppercase fw-bold mb-0">Card Reports</h6>
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
                    <li><a class="dropdown-item" href="{{ route('admin.manual-payment-agreements.index') }}"><i class="bi bi-file-earmark-lock me-2 text-warning"></i> Manual Agreements</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.terms.index') }}"><i class="bi bi-file-earmark-text me-2 text-info"></i> Manage Terms</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.privacy.index') }}"><i class="bi bi-shield-shaded me-2 text-info"></i> Manage Privacy Policy</a></li>
                    <li><hr class="dropdown-divider border-secondary"></li>
                    <li>
                        <button type="button" class="dropdown-item text-warning fw-bold" data-bs-toggle="modal" data-bs-target="#systemUpdateModal">
                            <i class="bi bi-cloud-arrow-up-fill me-2"></i> System Update
                        </button>
                    </li>
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

    <!-- Recent Users -->
    <div class="neon-card p-4">
        <h5 class="section-header">
            <i class="bi bi-clock-history section-icon"></i> RECENT CITIZENS
        </h5>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr>
                        <th class="border-secondary text-muted small">USER</th>
                        <th class="border-secondary text-muted small">JOINED</th>
                        <th class="border-secondary text-muted small text-end">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentUsers as $user)
                    <tr class="align-middle">
                        <td class="border-secondary py-3">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $user->avatar_url }}" alt="Avatar" style="width: 35px; height: 35px; border-radius: 50%; border: 1px solid rgba(0, 240, 255, 0.3);">
                                <div>
                                    <div class="fw-bold text-white">{{ $user->username }}</div>
                                    <div class="small text-muted">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="border-secondary text-white-50">{{ $user->created_at->diffForHumans() }}</td>
                        <td class="border-secondary text-end">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-info btn-sm rounded-pill px-3">
                                <i class="bi bi-pencil-square me-1"></i> Edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('modals')
    <!-- System Update Modal -->
    <div class="modal fade" id="systemUpdateModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="systemUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark border-warning shadow-lg">
                <div class="modal-header border-warning border-opacity-25">
                    <h5 class="modal-title orbitron text-warning" id="systemUpdateModalLabel">
                        <i class="bi bi-terminal-fill me-2"></i> SYSTEM DEPLOYMENT TERMINAL
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="closeDeploymentBtn"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="deployment-initial">
                        <p class="text-white-50">This action will execute the following commands on the server sequentially:</p>
                        <ol class="text-info x-small orbitron">
                            <li>git pull</li>
                            <li>composer install (optimizing)</li>
                            <li>php artisan migrate (force)</li>
                            <li>npm update</li>
                            <li>npm install</li>
                            <li>npm run build</li>
                        </ol>
                        <div class="alert alert-warning x-small border-warning border-opacity-25 bg-warning bg-opacity-10 py-2">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>WARNING:</strong> Ensure no one is currently performing sensitive operations. The site may experience brief downtime during build.
                        </div>
                        <button type="button" class="btn btn-warning w-100 orbitron fw-bold mt-3" id="startDeploymentBtn">
                            INITIATE DEPLOYMENT SEQUENCE
                        </button>
                    </div>

                    <div id="deployment-progress" class="d-none">
                        <div class="terminal-window bg-black p-3 rounded mb-3 border border-secondary border-opacity-25" style="height: 300px; overflow-y: auto; font-family: 'Courier New', Courier, monospace;">
                            <div id="terminal-output" class="x-small">
                                <div class="text-success mb-2">> System update initialized...</div>
                            </div>
                        </div>
                        
                        <div id="deployment-status" class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="spinner-border spinner-border-sm text-info" role="status" id="deployment-spinner">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span class="orbitron x-small text-info tracking-wide" id="status-text">EXECUTING GIT PULL...</span>
                            </div>
                            <span class="badge bg-dark border border-secondary text-muted x-small" id="step-counter">STEP 1/6</span>
                        </div>
                    </div>

                    <div id="deployment-complete" class="d-none text-center py-4">
                        <div class="display-1 text-success mb-3"><i class="bi bi-check-circle-fill"></i></div>
                        <h4 class="orbitron text-success mb-2">DEPLOYMENT SUCCESSFUL</h4>
                        <p class="text-white-50">All systems updated and assets recompiled.</p>
                        <button type="button" class="btn btn-outline-success orbitron mt-3" onclick="window.location.reload()">
                            RELOAD SYSTEM
                        </button>
                    </div>

                    <div id="deployment-failed" class="d-none text-center py-4">
                        <div class="display-1 text-danger mb-3"><i class="bi bi-x-circle-fill"></i></div>
                        <h4 class="orbitron text-danger mb-2">DEPLOYMENT FAILED</h4>
                        <p class="text-white-50 mb-4" id="fail-message">Check the terminal log above for details.</p>
                        
                        <div id="git-fix-suggestion" class="d-none mb-4">
                            <div class="alert alert-info x-small border-info border-opacity-25 bg-info bg-opacity-10 py-3 text-start mb-3">
                                <i class="bi bi-info-circle-fill me-2"></i> <strong>GIT PERMISSIONS DETECTED:</strong> The server reported issues accessing the .git directory or dubious ownership.
                            </div>
                            <button type="button" class="btn btn-outline-info orbitron w-100" id="fixGitConfigBtn">
                                REPAIR GIT CONFIG & PERMISSIONS
                            </button>
                        </div>

                        <button type="button" class="btn btn-outline-danger orbitron mt-2" data-bs-dismiss="modal">
                            CLOSE TERMINAL
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('styles')
<style>
    .neon-hover:hover {
        transform: translateY(-5px);
        background-color: rgba(255, 255, 255, 0.05) !important;
        box-shadow: 0 5px 15px rgba(0, 240, 255, 0.2);
    }

    /* Terminal Styles */
    .terminal-window::-webkit-scrollbar { width: 8px; }
    .terminal-window::-webkit-scrollbar-track { background: rgba(0,0,0,0.2); }
    .terminal-window::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }
    .terminal-window::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
    #terminal-output div { margin-bottom: 5px; word-break: break-all; white-space: pre-wrap; color: #aaffaa; }
    #terminal-output .error { color: #ff5555; }
    #terminal-output .command { color: #55ffff; font-weight: bold; }
</style>
@endpush

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startBtn = document.getElementById('startDeploymentBtn');
    const closeBtn = document.getElementById('closeDeploymentBtn');
    const terminal = document.getElementById('terminal-output');
    const terminalWindow = document.querySelector('.terminal-window');
    const statusText = document.getElementById('status-text');
    const stepCounter = document.getElementById('step-counter');
    
    const steps = [
        { key: 'git_pull', label: 'GIT PULL', cmd: 'git pull' },
        { key: 'composer_install', label: 'COMPOSER INSTALL', cmd: 'composer install --optimize-autoloader --no-dev' },
        { key: 'migrate', label: 'PHP ARTISAN MIGRATE', cmd: 'php artisan migrate --force' },
        { key: 'npm_update', label: 'NPM UPDATE', cmd: 'npm update' },
        { key: 'npm_install', label: 'NPM INSTALL', cmd: 'npm install' },
        { key: 'npm_build', label: 'NPM BUILD', cmd: 'npm run build' }
    ];

    let isRunning = false;

    startBtn.addEventListener('click', async function() {
        if (isRunning) return;
        isRunning = true;
        
        // UI Transitions
        document.getElementById('deployment-initial').classList.add('d-none');
        document.getElementById('deployment-progress').classList.remove('d-none');
        closeBtn.classList.add('d-none'); // Prevent accidental closure

        for (let i = 0; i < steps.length; i++) {
            const step = steps[i];
            
            // Update UI for current step
            statusText.innerText = `EXECUTING ${step.label}...`;
            stepCounter.innerText = `STEP ${i + 1}/${steps.length}`;
            
            logToTerminal(`Executing: ${step.cmd}`, 'command');

            try {
                const response = await fetch("{{ route('admin.system.update') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ step: step.key })
                });

                const result = await response.json();
                
                if (result.output) {
                    logToTerminal(result.output);
                }

                if (!result.success) {
                    showFailure(result.output || "Step failed without specific output.");
                    return;
                }

                logToTerminal(`✓ ${step.label} completed.`, 'success');

            } catch (error) {
                showFailure(`System error: ${error.message}`);
                return;
            }
        }

        // All steps completed
        showSuccess();
    });

    function logToTerminal(text, type = '') {
        const div = document.createElement('div');
        if (type) div.classList.add(type);
        div.innerText = type === 'command' ? `\n# ${text}` : text;
        terminal.appendChild(div);
        terminalWindow.scrollTop = terminalWindow.scrollHeight;

        // Auto-detect Git Ownership/Permission Error
        if (text.includes('dubious ownership') || text.includes('Permission denied')) {
            document.getElementById('git-fix-suggestion').classList.remove('d-none');
        }
    }

    document.getElementById('fixGitConfigBtn').addEventListener('click', async function() {
        const btn = this;
        btn.disabled = true;
        btn.innerText = "FIXING...";

        try {
            // 1. Fix Config
            logToTerminal("Repairing Git Config...", "command");
            const configResp = await fetch("{{ route('admin.system.update') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ step: 'fix_git_config' })
            });
            const configResult = await configResp.json();
            logToTerminal(configResult.output, configResult.success ? 'success' : 'error');

            // 2. Fix Permissions
            logToTerminal("Repairing Git Permissions...", "command");
            const permResp = await fetch("{{ route('admin.system.update') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ step: 'fix_git_permissions' })
            });
            const permResult = await permResp.json();
            logToTerminal(permResult.output, permResult.success ? 'success' : 'error');

            if (configResult.success || permResult.success) {
                // Hide fix button and retry deployment automatically if at least one succeeded
                document.getElementById('git-fix-suggestion').classList.add('d-none');
                document.getElementById('deployment-failed').classList.add('d-none');
                document.getElementById('deployment-status').classList.remove('d-none');
                startBtn.click(); // Re-trigger the whole process
            } else {
                btn.disabled = false;
                btn.innerText = "REPAIR FAILED - TRY AGAIN";
            }
        } catch (e) {
            logToTerminal(`Fix failed: ${e.message}`, 'error');
            btn.disabled = false;
            btn.innerText = "SYSTEM ERROR";
        }
    });

    function showFailure(msg) {
        isRunning = false;
        document.getElementById('deployment-status').classList.add('d-none');
        document.getElementById('deployment-failed').classList.remove('d-none');
        closeBtn.classList.remove('d-none');
        logToTerminal(`\n!!! DEPLOYMENT ABORTED DUE TO ERROR !!!`, 'error');
    }

    function showSuccess() {
        isRunning = false;
        document.getElementById('deployment-status').classList.add('d-none');
        document.getElementById('deployment-complete').classList.remove('d-none');
        closeBtn.classList.remove('d-none');
        statusText.innerText = "DEPLOYMENT COMPLETE";
    }
});
</script>
@endsection
