<div>
    <h5 class="orbitron text-cyan">BATTLE STATUS: <span class="text-white">{{ strtoupper($battle->status) }}</span></h5>
    <p class="small text-muted">{{ $battle->battle_terms }}</p>
    
    @if($battle->marshall_id)
        <div class="mt-3">
            <span class="badge bg-warning text-dark"><i class="bi bi-shield-check"></i> MARSHALL: {{ $battle->marshall->username }}</span>
        </div>
    @elseif($battle->team_a_marshall_elect && $battle->team_a_marshall_elect == $battle->team_b_marshall_elect && !in_array($battle->status, ['completed', 'cancelled']))
        @if(Auth::id() == $battle->team_a_marshall_elect)
            <div class="mt-3 p-3" style="background: rgba(255, 221, 0, 0.1); border: 1px solid #ffdd00; border-radius: 8px;">
                <p style="color: #ffdd00; font-family: 'Orbitron', sans-serif; font-size: 0.9rem; margin-bottom: 0.5rem;"><i class="bi bi-shield-exclamation"></i> MARSHALL ELECTION</p>
                <p class="small text-light mb-3">Both team leaders have elected you to adjudicate this team battle. Do you accept this responsibility?</p>
                <div class="d-flex gap-2">
                    <button wire:click="$parent.acceptMarshall" class="btn btn-sm btn-neon w-50" style="border-color: #ffdd00; color: #ffdd00;"><i class="bi bi-check-lg"></i> ACCEPT</button>
                    <button wire:click="$parent.rejectMarshall" class="btn btn-sm btn-outline-light w-50"><i class="bi bi-x-lg"></i> REJECT</button>
                </div>
            </div>
        @else
            <div class="mt-3">
                <span class="badge" style="background: rgba(255, 221, 0, 0.2); color: #ffdd00; border: 1px solid #ffdd00;">
                    <i class="bi bi-hourglass-split"></i> WAITING FOR MARSHALL TO ACCEPT
                </span>
            </div>
        @endif
    @else
        <div class="mt-3">
            @if($battle->status == 'pending')
                @if(!$battle->is_full)
                    <div class="alert alert-info py-2 small">Waiting for players to join...</div>
                @elseif(!$battle->team_b_ready)
                    <div class="alert alert-warning py-2 small">Waiting for Team B to ready up...</div>
                @elseif(Auth::id() != $battle->team_a_user_1)
                    <div class="alert alert-info py-2 small">Waiting for Team A Leader to start...</div>
                @endif
            @endif
        </div>
    @endif
</div>
