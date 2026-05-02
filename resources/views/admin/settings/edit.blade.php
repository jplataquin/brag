@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-5 fw-bold text-uppercase mb-1" style="color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                <i class="bi bi-sliders"></i> Platform Features
            </h1>
            <p class="text-secondary lead mb-0">Toggle core platform features on or off globally.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-dark bg-opacity-75 border-info rounded-4 p-4 shadow-lg" style="backdrop-filter: blur(10px);">
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="list-group list-group-flush mb-4 bg-transparent">
                            <!-- Maintenance Mode -->
                            <div class="list-group-item bg-transparent text-white border-danger border-2 rounded-3 mb-4 py-3 px-3 d-flex justify-content-between align-items-center" style="background: rgba(255, 0, 0, 0.05) !important;">
                                <div>
                                    <h5 class="mb-1 text-uppercase fw-bold text-danger" style="font-family: 'Orbitron', sans-serif;">
                                        <i class="bi bi-exclamation-octagon-fill"></i> Maintenance Mode
                                    </h5>
                                    <p class="mb-0 text-muted small">When active, only administrators can access the site. Standard users will see a maintenance page.</p>
                                </div>
                                <div class="form-check form-switch fs-3 mb-0">
                                    <input class="form-check-input bg-danger border-danger" type="checkbox" role="switch" name="is_maintenance_mode" id="is_maintenance_mode" style="cursor: pointer;" {{ $settings->is_maintenance_mode ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- Template Creation -->
                            <div class="list-group-item bg-transparent text-white border-info py-3 px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1 text-uppercase fw-bold" style="font-family: 'Orbitron', sans-serif;">
                                        <i class="bi bi-images" style="color: var(--neon-cyan);"></i> Template Creation
                                    </h5>
                                    <p class="mb-0 text-muted small">Allow users to create new card templates.</p>
                                </div>
                                <div class="form-check form-switch fs-3 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="allow_template_creation" id="allow_template_creation" style="cursor: pointer;" {{ $settings->allow_template_creation ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- Card Forging -->
                            <div class="list-group-item bg-transparent text-white border-info py-3 px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1 text-uppercase fw-bold" style="font-family: 'Orbitron', sans-serif;">
                                        <i class="bi bi-hammer" style="color: var(--neon-yellow);"></i> Card Forging
                                    </h5>
                                    <p class="mb-0 text-muted small">Allow users to forge new digital cards from existing templates.</p>
                                </div>
                                <div class="form-check form-switch fs-3 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="allow_card_forging" id="allow_card_forging" style="cursor: pointer;" {{ $settings->allow_card_forging ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- Battle Rooms -->
                            <div class="list-group-item bg-transparent text-white border-info py-3 px-0 d-flex justify-content-between align-items-center border-bottom-0">
                                <div>
                                    <h5 class="mb-1 text-uppercase fw-bold" style="font-family: 'Orbitron', sans-serif;">
                                        <i class="bi bi-controller" style="color: var(--neon-magenta);"></i> Battle Rooms
                                    </h5>
                                    <p class="mb-0 text-muted small">Allow users to create new PvP battle rooms.</p>
                                </div>
                                <div class="form-check form-switch fs-3 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="allow_battle_creation" id="allow_battle_creation" style="cursor: pointer;" {{ $settings->allow_battle_creation ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-lg btn-neon-cyan fw-bold">
                                <i class="bi bi-save"></i> Save Platform Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom switch styling to match neon theme */
    .form-switch .form-check-input {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: rgba(0, 240, 255, 0.3);
    }
    .form-switch .form-check-input:focus {
        border-color: var(--neon-cyan);
        box-shadow: 0 0 10px rgba(0, 240, 255, 0.5);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba(0, 240, 255, 0.5)'/%3e%3c/svg%3e");
    }
    .form-switch .form-check-input:checked {
        background-color: var(--neon-cyan);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 10px rgba(0, 240, 255, 0.5);
    }
</style>
@endsection
