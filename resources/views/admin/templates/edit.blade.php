@extends('layouts.app')

@section('title', 'Edit Template #' . $template->id)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title m-0">
            <span class="page-title-accent"><i class="bi bi-images"></i></span> EDIT TEMPLATE <span class="text-muted fs-4">#{{ $template->id }}</span>
        </h1>
        <a href="{{ route('admin.templates.index') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left"></i> Back to Templates
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
                <form action="{{ route('admin.templates.update', $template->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h4 class="text-neon-cyan mb-4" style="font-family: 'Orbitron', sans-serif;">Template Details</h4>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label text-white-50">Creator (User)</label>
                            <select name="user_id" id="user_id" class="form-select bg-dark text-white border-secondary" required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $template->user_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->username }} (ID: {{ $user->id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="game_title_id" class="form-label text-white-50">Game Title</label>
                            <select name="game_title_id" id="game_title_id" class="form-select bg-dark text-white border-secondary" required>
                                @foreach($gameTitles as $game)
                                    <option value="{{ $game->id }}" {{ $template->game_title_id == $game->id ? 'selected' : '' }}>
                                        {{ $game->name }} (ID: {{ $game->id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="card_title" class="form-label text-white-50">Card Title</label>
                        <input type="text" name="card_title" id="card_title" class="form-control bg-dark text-white border-secondary" value="{{ $template->card_title }}" required maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label for="quote" class="form-label text-white-50">Quote</label>
                        <textarea name="quote" id="quote" class="form-control bg-dark text-white border-secondary" rows="3" required maxlength="500">{{ $template->quote }}</textarea>
                    </div>

                    <hr class="border-secondary my-4">
                    <h4 class="text-neon-magenta mb-4" style="font-family: 'Orbitron', sans-serif;">Design Settings</h4>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="background_color" class="form-label text-white-50">Background Color</label>
                            <input type="color" name="background_color" id="background_color" class="form-control form-control-color bg-dark border-secondary w-100" value="{{ $template->background_color ?? '#000000' }}" title="Choose Background color">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="border_color" class="form-label text-white-50">Border Color</label>
                            <input type="color" name="border_color" id="border_color" class="form-control form-control-color bg-dark border-secondary w-100" value="{{ $template->border_color ?? '#ffffff' }}" title="Choose Border color">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="section_color" class="form-label text-white-50">Section Color</label>
                            <input type="color" name="section_color" id="section_color" class="form-control form-control-color bg-dark border-secondary w-100" value="{{ $template->section_color ?? '#222222' }}" title="Choose Section color">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="primary_text_color" class="form-label text-white-50">Primary Text Color</label>
                            <input type="color" name="primary_text_color" id="primary_text_color" class="form-control form-control-color bg-dark border-secondary w-100" value="{{ $template->primary_text_color ?? '#ffffff' }}" title="Choose Primary Text color">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="secondary_text_color" class="form-label text-white-50">Secondary Text Color</label>
                            <input type="color" name="secondary_text_color" id="secondary_text_color" class="form-control form-control-color bg-dark border-secondary w-100" value="{{ $template->secondary_text_color ?? '#aaaaaa' }}" title="Choose Secondary Text color">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="image_position_y" class="form-label text-white-50">Image Y Position (%)</label>
                            <input type="number" name="image_position_y" id="image_position_y" class="form-control bg-dark text-white border-secondary" value="{{ $template->image_position_y ?? 50 }}" min="0" max="100">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-neon-warning w-100 py-3 mt-4" style="font-family: 'Orbitron', sans-serif; letter-spacing: 2px;">
                        SAVE TEMPLATE CHANGES
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
                        <strong>Created At:</strong><br>
                        <span class="text-white">{{ $template->created_at->format('M j, Y H:i:s') }}</span>
                    </li>
                    <li class="list-group-item bg-transparent text-white-50 px-0 border-secondary">
                        <strong>Status:</strong><br>
                        @if($template->trashed())
                            <span class="text-danger">Deleted ({{ $template->deleted_at->format('M j, Y H:i') }})</span>
                        @else
                            <span class="text-success">Active</span>
                        @endif
                    </li>
                    <li class="list-group-item bg-transparent text-white-50 px-0 border-secondary">
                        <strong>Last Edited By Admin:</strong><br>
                        @if($template->adminEditor)
                            <span class="text-warning">{{ $template->adminEditor->username }}</span><br>
                            <span class="text-white small">{{ $template->admin_edited_at->format('M j, Y H:i:s') }}</span>
                        @else
                            <span class="text-muted">Never edited manually</span>
                        @endif
                    </li>
                </ul>
            </div>

            <!-- Preview Thumbnail -->
            <div class="card bg-dark border-secondary rounded-4 shadow-lg overflow-hidden">
                 <div class="card-header border-secondary bg-dark text-center">
                     <span class="text-muted small text-uppercase">Current Image</span>
                 </div>
                 <div class="card-body p-0 d-flex justify-content-center align-items-center" style="background: url('{{ $template->display_photo }}') center/cover; height: 250px; background-position-y: {{ $template->image_position_y ?? 50 }}%;">
                     @if(!$template->photo && !$template->ai_photo)
                         <span class="text-muted"><i class="bi bi-image"></i> No Custom Image</span>
                     @endif
                 </div>
            </div>
        </div>
    </div>
</div>

@endsection
