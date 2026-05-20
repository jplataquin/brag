@extends('layouts.app')

@section('title', $gameTitle->title . ' Leaderboard')

@section('content')
<div class="container py-5">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('game_titles.index') }}" class="text-info text-decoration-none">Games</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('game_titles.show', $gameTitle->id) }}" class="text-info text-decoration-none">{{ $gameTitle->title }}</a></li>
                    <li class="breadcrumb-item active text-white-50" aria-current="page">Leaderboard</li>
                </ol>
            </nav>
            <h1 class="orbitron fw-bold text-white mb-0">
                <i class="bi bi-trophy-fill text-warning me-2"></i> {{ strtoupper($gameTitle->title) }} LEADERBOARD
            </h1>
        </div>
        <div class="text-end">
            <span class="badge bg-dark border border-warning text-warning p-2 orbitron">
                CRITERIA: LEVEL + WIN RATE + INTEGRITY
            </span>
        </div>
    </div>

    <!-- Leaderboard Table -->
    <div class="neon-card p-0 overflow-hidden" style="border-color: rgba(0, 240, 255, 0.3);">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead class="orbitron text-info border-bottom border-info" style="background: rgba(0, 240, 255, 0.05);">
                    <tr>
                        <th class="ps-4 py-3 text-center" style="width: 80px;">RANK</th>
                        <th class="py-3">CARD / TEMPLATE</th>
                        <th class="py-3">OWNER</th>
                        <th class="py-3 text-center">LEVEL</th>
                        <th class="py-3 text-center">WIN RATE</th>
                        <th class="py-3 text-center">INTEGRITY</th>
                        <th class="py-3 text-center pe-4" style="width: 120px;">SCORE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cards as $index => $card)
                        <tr class="border-bottom border-white-10">
                            <td class="ps-4 text-center">
                                @php $rank = ($cards->currentPage() - 1) * $cards->perPage() + $index + 1; @endphp
                                <span class="fs-4 fw-bold orbitron {{ $rank <= 3 ? 'text-warning' : 'text-white-50' }}">
                                    #{{ $rank }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3 position-relative" style="width: 45px; height: 60px; border: 2px solid {{ $card->template->border_color }}; border-radius: 4px; overflow: hidden;">
                                        <img src="{{ $card->template->display_photo }}" alt="Card Image" class="w-100 h-100 object-fit-cover">
                                    </div>
                                    <div>
                                        <div class="fw-bold text-white">{{ $card->template->card_title }}</div>
                                        <small class="text-white-50">S/N: #{{ $card->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($card->owner)
                                    <a href="{{ route('profile.show', $card->owner->username) }}" class="text-info text-decoration-none d-flex align-items-center">
                                        <i class="bi bi-person-circle me-2"></i>
                                        {{ $card->owner->username }}
                                    </a>
                                @else
                                    <span class="text-white-50">Unknown</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill" style="background: rgba(57, 255, 20, 0.1); border: 1px solid #39ff14; color: #39ff14;">
                                    LV. {{ $card->level }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="orbitron text-cyan">{{ number_format($card->win_rate, 1) }}%</div>
                                <div class="progress mt-1" style="height: 4px; background: rgba(255, 255, 255, 0.1);">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $card->win_rate }}%"></div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="orbitron text-magenta">{{ number_format($card->integrity_stat, 1) }}%</div>
                                <div class="progress mt-1" style="height: 4px; background: rgba(255, 255, 255, 0.1);">
                                    <div class="progress-bar bg-magenta" role="progressbar" style="width: {{ $card->integrity_stat }}%"></div>
                                </div>
                            </td>
                            <td class="text-center pe-4">
                                <div class="orbitron fw-bold text-warning fs-5" style="text-shadow: 0 0 10px rgba(255, 221, 0, 0.3);">
                                    {{ number_format($card->leaderboard_score, 2) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center text-white-50 fs-5">
                                <i class="bi bi-info-circle me-2"></i> No cards found for this game title yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $cards->links() }}
    </div>
</div>

<style>
    .bg-magenta { background-color: #ff00ff; }
    .text-magenta { color: #ff00ff; }
    .text-cyan { color: #00f0ff; }
    .border-white-10 { border-color: rgba(255, 255, 255, 0.05) !important; }
</style>
@endsection
