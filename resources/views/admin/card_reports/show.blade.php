@extends('layouts.app')

@section('title', 'Review Report #' . $report->id)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <h1 class="page-title mb-0">
        <span class="page-title-accent"><i class="bi bi-shield-exclamation"></i></span> REVIEW REPORT #{{ $report->id }}
    </h1>
    <a href="{{ route('admin.card_reports.index') }}" class="btn btn-outline-info btn-sm">
        <i class="bi bi-arrow-left"></i> Back to Queue
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Report Details -->
        <div class="neon-card p-4 mb-4">
            <h3 class="orbitron h5 mb-4 text-info"><i class="bi bi-info-circle me-2"></i> REPORT INFORMATION</h3>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="x-small text-muted orbitron d-block mb-1">REPORTER</label>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-white fw-bold">{{ $report->user->username }}</span>
                        <span class="x-small text-muted">({{ $report->user->email }})</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="x-small text-muted orbitron d-block mb-1">REPORTED ON</label>
                    <div class="text-white">{{ $report->created_at->format('M j, Y H:i:s') }}</div>
                </div>
                <div class="col-12">
                    <label class="x-small text-muted orbitron d-block mb-1">REASON</label>
                    <span class="badge rounded-pill" style="background: rgba(0, 240, 255, 0.1); color: #00f0ff; border: 1px solid rgba(0, 240, 255, 0.2); padding: 0.5rem 1rem;">
                        {{ strtoupper($report->reason) }}
                    </span>
                </div>
                <div class="col-12">
                    <label class="x-small text-muted orbitron d-block mb-1">REPORTER NOTES</label>
                    <div class="p-3 rounded bg-dark bg-opacity-50 text-white-50 border border-secondary border-opacity-25">
                        {{ $report->notes ?: 'No notes provided by reporter.' }}
                    </div>
                </div>
            </div>
        </div>

        @if($report->status === 'pending')
            <!-- Action Form -->
            <div class="neon-card p-4 border-warning shadow-lg">
                <h3 class="orbitron h5 mb-4 text-warning"><i class="bi bi-hammer me-2"></i> TAKE ACTION</h3>
                
                <form action="{{ route('admin.card_reports.resolve', $report) }}" method="POST">
                    @csrf @method('PATCH')
                    
                    <div class="mb-4">
                        <label class="form-label orbitron x-small text-muted">SELECT OUTCOME</label>
                        <div class="d-flex gap-3">
                            <div class="flex-grow-1">
                                <input type="radio" class="btn-check" name="status" id="outcome_resolve" value="resolved" required>
                                <label class="btn btn-outline-success w-100 py-3 d-flex flex-column align-items-center gap-2" for="outcome_resolve">
                                    <i class="bi bi-check-circle fs-4"></i>
                                    <span class="orbitron x-small">CONFIRMED</span>
                                    <small class="x-small opacity-50">Censors the card</small>
                                </label>
                            </div>
                            <div class="flex-grow-1">
                                <input type="radio" class="btn-check" name="status" id="outcome_dismiss" value="dismissed" required>
                                <label class="btn btn-outline-danger w-100 py-3 d-flex flex-column align-items-center gap-2" for="outcome_dismiss">
                                    <i class="bi bi-x-circle fs-4"></i>
                                    <span class="orbitron x-small">DISMISS</span>
                                    <small class="x-small opacity-50">Keep card as is</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="admin_notes" class="form-label orbitron x-small text-muted">MODERATOR NOTES (SENT TO REPORTER)</label>
                        <textarea name="admin_notes" id="admin_notes" rows="4" class="form-control bg-dark text-white border-secondary focus-info" placeholder="Explain your decision to the reporter..." required minlength="5"></textarea>
                        <div class="form-text x-small text-muted mt-2">
                            <i class="bi bi-info-circle me-1"></i> These notes will be included in the email sent to the reporter.
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-neon-cyan py-3 orbitron fw-bold">
                            SUBMIT DECISION & NOTIFY REPORTER
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- Resolution Info -->
            <div class="neon-card p-4 border-info">
                <h3 class="orbitron h5 mb-4 text-info"><i class="bi bi-check2-all me-2"></i> RESOLUTION</h3>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="x-small text-muted orbitron d-block mb-1">STATUS</label>
                        <span class="badge {{ $report->status === 'resolved' ? 'bg-success' : 'bg-secondary' }}">
                            {{ strtoupper($report->status === 'resolved' ? 'confirmed' : $report->status) }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <label class="x-small text-muted orbitron d-block mb-1">RESOLVED BY</label>
                        <div class="text-white">{{ $report->resolvedBy ? $report->resolvedBy->username : 'System' }}</div>
                    </div>
                    <div class="col-12">
                        <label class="x-small text-muted orbitron d-block mb-1">MODERATOR NOTES</label>
                        <div class="p-3 rounded bg-info bg-opacity-10 text-white border border-info border-opacity-25">
                            {{ $report->admin_notes }}
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="x-small text-muted orbitron d-block mb-1">RESOLVED AT</label>
                        <div class="text-white-50 small">{{ $report->resolved_at ? $report->resolved_at->format('M j, Y H:i:s') : '—' }}</div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Reported Card Preview -->
        <div class="neon-card p-4 sticky-top" style="top: 2rem;">
            <h3 class="orbitron h5 mb-4 text-magenta"><i class="bi bi-card-image me-2"></i> REPORTED CARD</h3>
            
            @if($report->digitalCard)
                <div class="text-center mb-4">
                    <div class="mx-auto mb-3" style="width: 200px; height: 280px; border-radius: 12px; overflow: hidden; border: 2px solid rgba(0,240,255,0.5); box-shadow: 0 0 20px rgba(0,240,255,0.2);">
                        <img src="{{ $report->digitalCard->template->display_photo }}" class="w-100 h-100" style="object-fit: cover;">
                    </div>
                    <h4 class="text-white mb-1">{{ $report->digitalCard->template->card_title }}</h4>
                    <div class="text-info x-small orbitron mb-3">#{{ str_pad($report->digitalCard->serial_number, 4, '0', STR_PAD_LEFT) }}</div>
                    
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <span class="badge" style="background: {{ $report->digitalCard->rarity_color }}; color: #000;">
                            {{ $report->digitalCard->level_name }}
                        </span>
                        @if($report->digitalCard->is_censored)
                            <span class="badge bg-danger">CENSORED</span>
                        @endif
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('cards.show', $report->digitalCard) }}" target="_blank" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-eye"></i> View Public Page
                    </a>
                    <a href="{{ route('admin.cards.edit', $report->digitalCard) }}" class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-pencil"></i> Edit Card Details
                    </a>
                    
                    <form action="{{ route('admin.cards.censor', $report->digitalCard) }}" method="POST" class="d-grid mt-2">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $report->digitalCard->is_censored ? 'btn-danger' : 'btn-outline-warning' }}">
                            <i class="bi {{ $report->digitalCard->is_censored ? 'bi-eye-fill' : 'bi-eye-slash-fill' }}"></i>
                            {{ $report->digitalCard->is_censored ? 'Un-censor Card' : 'Censor Card' }}
                        </button>
                    </form>
                </div>

                <hr class="border-secondary opacity-25 my-4">

                <div class="x-small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Owner:</span>
                        <span class="text-white">{{ $report->digitalCard->owner ? '@' . $report->digitalCard->owner->username : 'None' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Original Owner:</span>
                        <span class="text-white">{{ $report->digitalCard->originalOwner ? '@' . $report->digitalCard->originalOwner->username : 'None' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Created:</span>
                        <span class="text-white">{{ $report->digitalCard->created_at->format('M j, Y') }}</span>
                    </div>
                </div>
            @else
                <div class="text-center py-5 text-danger">
                    <i class="bi bi-trash fs-1 d-block mb-2"></i>
                    <p class="orbitron x-small">CARD HAS BEEN DELETED</p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .focus-info:focus {
        border-color: #00f0ff !important;
        box-shadow: 0 0 0 0.25rem rgba(0, 240, 255, 0.25) !important;
    }
</style>
@endpush
