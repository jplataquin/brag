@extends('layouts.app')

@section('title', 'Edit Digital Card #' . $card->id)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title m-0">
            <span class="page-title-accent"><i class="bi bi-pencil-square"></i></span> EDIT DIGITAL CARD <span class="text-muted fs-4">#{{ $card->id }}</span>
        </h1>
        <a href="{{ route('admin.cards.index') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left"></i> Back to Cards
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" style="background: rgba(255, 0, 0, 0.1); border: 1px solid rgba(255, 0, 0, 0.3); color: #ff4444;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Edit Form -->
        <div class="col-lg-8 mb-4">
            <div class="card bg-dark bg-opacity-75 border-secondary rounded-4 p-4 shadow-lg" style="backdrop-filter: blur(10px);">
                <form action="{{ route('admin.cards.update', $card->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h4 class="text-neon-cyan mb-4" style="font-family: 'Orbitron', sans-serif;">Card Configuration</h4>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="template_id" class="form-label text-white-50">Design (Template)</label>
                            <select name="template_id" id="template_id" class="form-select bg-dark text-white border-secondary" required>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" {{ $card->template_id == $template->id ? 'selected' : '' }}>
                                        {{ $template->card_title }} (ID: {{ $template->id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="level" class="form-label text-white-50">Level</label>
                            <select name="level" id="level" class="form-select bg-dark text-white border-secondary" required>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ $card->level == $i ? 'selected' : '' }}>
                                        Level {{ $i }} - {{ config("leveling.conditions.{$i}.name", "Lvl {$i}") }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="owner_id" class="form-label text-white-50">Current Owner</label>
                            <select name="owner_id" id="owner_id" class="form-select bg-dark text-white border-secondary">
                                <option value="">None (Burned/Unassigned)</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $card->owner_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->username }} (ID: {{ $user->id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="original_owner_id" class="form-label text-white-50">Original Owner (Forger)</label>
                            <select name="original_owner_id" id="original_owner_id" class="form-select bg-dark text-white border-secondary" required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $card->original_owner_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->username }} (ID: {{ $user->id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label text-white-50">Status</label>
                            <select name="status" id="status" class="form-select bg-dark text-white border-secondary" required>
                                <option value="Maintained" {{ $card->status === 'Maintained' ? 'selected' : '' }}>Maintained</option>
                                <option value="Discontinued" {{ $card->status === 'Discontinued' ? 'selected' : '' }}>Discontinued</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <div class="form-check w-100 p-2 border border-secondary rounded text-center">
                                <input class="form-check-input float-none ms-0 me-2" type="checkbox" name="is_trophy" id="is_trophy" value="1" {{ $card->is_trophy ? 'checked' : '' }}>
                                <label class="form-check-label text-warning" for="is_trophy">
                                    <i class="bi bi-trophy-fill"></i> Is Trophy Card?
                                </label>
                                <!-- Hidden input to ensure false is sent if unchecked -->
                                <input type="hidden" name="is_trophy" value="0" {{ $card->is_trophy ? 'disabled' : '' }} id="is_trophy_hidden">
                            </div>
                        </div>
                    </div>

                    <hr class="border-secondary my-4">
                    <h4 class="text-neon-cyan mb-4" style="font-family: 'Orbitron', sans-serif;">Combat Stats</h4>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="wins" class="form-label text-white-50">Wins</label>
                            <input type="number" name="wins" id="wins" class="form-control bg-dark text-white border-secondary" value="{{ $card->wins }}" min="0" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="losses" class="form-label text-white-50">Losses</label>
                            <input type="number" name="losses" id="losses" class="form-control bg-dark text-white border-secondary" value="{{ $card->losses }}" min="0" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="life_points" class="form-label text-white-50">Life Points</label>
                            <input type="number" name="life_points" id="life_points" class="form-control bg-dark text-white border-secondary" value="{{ $card->life_points }}" min="0" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-neon-warning w-100 py-3 mt-4" style="font-family: 'Orbitron', sans-serif; letter-spacing: 2px;">
                        SAVE CARD CHANGES
                    </button>
                </form>
            </div>
        </div>

        <!-- Tracking Info & Preview -->
        <div class="col-lg-4">
            <div class="card bg-dark bg-opacity-75 border-secondary rounded-4 p-4 shadow-lg mb-4" style="backdrop-filter: blur(10px);">
                <h5 class="text-neon-yellow mb-3" style="font-family: 'Orbitron', sans-serif;"><i class="bi bi-clock-history"></i> Tracking Info</h5>
                
                <ul class="list-group list-group-flush bg-transparent">
                    <li class="list-group-item bg-transparent text-white-50 px-0 border-secondary">
                        <strong>Forged At:</strong><br>
                        <span class="text-white">{{ $card->forged_at->format('M j, Y H:i:s') }}</span>
                    </li>
                    <li class="list-group-item bg-transparent text-white-50 px-0 border-secondary">
                        <strong>Last Edited By Admin:</strong><br>
                        @if($card->adminEditor)
                            <span class="text-warning">{{ $card->adminEditor->username }}</span><br>
                            <span class="text-white small">{{ $card->admin_edited_at->format('M j, Y H:i:s') }}</span>
                        @else
                            <span class="text-muted">Never edited manually</span>
                        @endif
                    </li>
                    @if($card->burned_at)
                    <li class="list-group-item bg-transparent text-white-50 px-0 border-secondary">
                        <strong>Burned At:</strong><br>
                        <span class="text-danger">{{ $card->burned_at->format('M j, Y H:i:s') }}</span>
                        @if($card->burnedBy)
                            <br><small>by {{ $card->burnedBy->username }}</small>
                        @endif
                    </li>
                    @endif
                </ul>
            </div>

            <!-- Digital Card Preview Thumbnail (Optional visual context) -->
            <div class="card bg-dark border-secondary rounded-4 shadow-lg overflow-hidden">
                 <div class="card-header border-secondary bg-dark text-center">
                     <span class="text-muted small text-uppercase">Current Design Preview</span>
                 </div>
                 <div class="card-body p-0 d-flex justify-content-center align-items-center" style="background: url('{{ asset('storage/' . ($card->template->image_path ?? '')) }}') center/cover; height: 250px;">
                     @if(!$card->template || !$card->template->image_path)
                         <span class="text-muted"><i class="bi bi-image"></i> No Image Available</span>
                     @endif
                 </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isTrophyCheckbox = document.getElementById('is_trophy');
        const hiddenInput = document.getElementById('is_trophy_hidden');

        isTrophyCheckbox.addEventListener('change', function() {
            hiddenInput.disabled = this.checked;
        });
    });
</script>
@endsection
