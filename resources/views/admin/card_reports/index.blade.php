@extends('layouts.app')

@section('title', 'Moderation - Card Reports')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <h1 class="page-title mb-0">
        <span class="page-title-accent"><i class="bi bi-shield-exclamation"></i></span> MODERATION QUEUE
    </h1>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-info btn-sm">
        <i class="bi bi-arrow-left"></i> Back to Admin
    </a>
</div>

@if($reports->count() > 0)
    <div class="neon-card p-0" style="overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0" style="background: transparent;">
                <thead>
                    <tr style="border-bottom: 2px solid rgba(0, 240, 255, 0.2);">
                        <th class="orbitron py-3 ps-4" style="font-size: 0.8rem; color: #00f0ff;">ID</th>
                        <th class="orbitron py-3" style="font-size: 0.8rem; color: #00f0ff;">DATE & TIME</th>
                        <th class="orbitron py-3" style="font-size: 0.8rem; color: #00f0ff;">REPORTER</th>
                        <th class="orbitron py-3" style="font-size: 0.8rem; color: #00f0ff;">CARD</th>
                        <th class="orbitron py-3" style="font-size: 0.8rem; color: #00f0ff;">REASON</th>
                        <th class="orbitron py-3" style="font-size: 0.8rem; color: #00f0ff;">STATUS</th>
                        <th class="orbitron py-3 pe-4 text-end" style="font-size: 0.8rem; color: #00f0ff;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                        @php
                            $card = $report->digitalCard;
                        @endphp
                        <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); vertical-align: middle;">
                            <td class="ps-4">
                                <span class="text-info x-small orbitron">#{{ $report->id }}</span>
                            </td>
                            <td>
                                <div class="small text-white">{{ $report->created_at->format('M j, Y') }}</div>
                                <div class="x-small text-muted">{{ $report->created_at->format('H:i') }}</div>
                            </td>
                            <td>
                                <a href="{{ route('admin.users.edit', $report->user) }}" class="text-info text-decoration-none small">
                                    @<span>{{ $report->user->username }}</span>
                                </a>
                            </td>
                            <td>
                                @if($card)
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 32px; height: 45px; border-radius: 4px; overflow: hidden; border: 1px solid rgba(0,240,255,0.3);">
                                            <img src="{{ $card->template->display_photo }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div>
                                            <a href="{{ route('cards.show', $card) }}" target="_blank" class="text-white text-decoration-none small fw-bold d-block">
                                                {{ $card->template->card_title }}
                                            </a>
                                            <span class="x-small text-muted">#{{ str_pad($card->serial_number, 4, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-danger small italic">[Card Deleted]</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill x-small" style="background: rgba(0, 240, 255, 0.1); color: #00f0ff; border: 1px solid rgba(0, 240, 255, 0.2);">
                                    {{ strtoupper($report->reason) }}
                                </span>
                            </td>
                            <td>
                                @if($report->status === 'pending')
                                    <span class="badge bg-warning text-dark x-small">PENDING</span>
                                @elseif($report->status === 'resolved')
                                    <span class="badge bg-success x-small">CONFIRMED</span>
                                @else
                                    <span class="badge bg-secondary x-small">DISMISSED</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.card_reports.show', $report) }}" class="btn btn-outline-info btn-sm orbitron" style="font-size: 0.7rem; letter-spacing: 1px;">
                                    {{ $report->status === 'pending' ? 'REVIEW' : 'DETAILS' }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $reports->links() }}
    </div>
@else
    <div class="empty-state">
        <div class="empty-icon text-success"><i class="bi bi-shield-check"></i></div>
        <div class="empty-text">No reports pending. Everything is clean!</div>
    </div>
@endif
@endsection
