<div>
    <div class="neon-card p-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1 class="page-title mb-1">
                    <span class="page-title-accent"><i class="bi bi-crosshair"></i></span> BATTLE #{{ $battle->id }}
                </h1>
                <div class="mb-2">
                    @if($battle->challengerCard && $battle->challengerCard->template && $battle->challengerCard->template->gameTitle)
                        <span style="color: #00f0ff; font-family: 'Orbitron', sans-serif; font-size: 1.1rem; letter-spacing: 2px; text-transform: uppercase; font-weight: 700;">
                            {{ $battle->challengerCard->template->gameTitle->title }}
                        </span>
                    @endif
                </div>
                <div class="d-flex gap-3 align-items-center flex-wrap">
                    <span id="battle-status-badge" class="status-badge status-{{ $battle->status }}">{{ strtoupper($battle->status) }}</span>
                    @if($battle->marshall)
                        <span style="color: #ffdd00; font-size: 0.9rem;">
                            <i class="bi bi-shield-shaded"></i> MARSHALL: <strong>{{ $battle->marshall->username }}</strong>
                        </span>
                    @else
                        <span class="text-muted" style="font-size: 0.9rem;">
                            <i class="bi bi-shield"></i> NO MARSHALL ELECTED
                        </span>
                    @endif
                </div>
                
                @if(!$battle->marshall_id && ($battle->challenger_marshall_id || $battle->opponent_marshall_id))
                    <div class="mt-3 p-2 rounded" style="background: rgba(255, 221, 0, 0.05); border: 1px solid rgba(255, 221, 0, 0.1); display: inline-block;">
                        <div class="d-flex gap-3 align-items-center" style="font-size: 0.8rem;">
                            <div style="color: #8888aa;">ELECTION STATUS:</div>
                            <div style="color: #00f0ff;">
                                CHALLENGER: <strong>{{ $battle->challengerMarshall ? $battle->challengerMarshall->username : 'NONE' }}</strong>
                            </div>
                            <div style="color: #ff00ff;">
                                OPPONENT: <strong>{{ $battle->opponentMarshall ? $battle->opponentMarshall->username : 'NONE' }}</strong>
                            </div>
                            @if($battle->challenger_marshall_id && $battle->opponent_marshall_id && $battle->challenger_marshall_id === $battle->opponent_marshall_id)
                                <div class="neon-text-yellow px-2" style="animation: pulse-yellow 2s infinite;">
                                    <i class="bi bi-hourglass-split"></i> AWAITING ACCEPTANCE
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="battle-terms-box p-3" style="background: rgba(0, 240, 255, 0.05); border: 1px solid rgba(0, 240, 255, 0.1); border-radius: 12px; min-width: 250px;">
                <div style="font-family: 'Orbitron', sans-serif; font-size: 0.7rem; color: #00f0ff; margin-bottom: 0.5rem; letter-spacing: 1px;">BATTLE TERMS</div>
                <div style="font-size: 0.9rem; color: #8888aa; font-style: italic;">
                    {{ $battle->terms ?: 'No specific terms defined for this match.' }}
                </div>
            </div>
        </div>
    </div>
</div>
