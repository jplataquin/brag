<div>
    <h5 class="orbitron text-cyan">BATTLE STATUS: <span class="text-white">{{ strtoupper($battle->status) }}</span></h5>
    <p class="small text-muted">{{ $battle->battle_terms }}</p>
    
    @if($battle->status === 'failed')
        <div id="marshall-countdown" class="mt-3 p-3 text-center" style="background: rgba(255, 221, 0, 0.1); border: 1px solid #ffdd00; border-radius: 8px;">
            <p style="color: #ffdd00; font-family: 'Orbitron', sans-serif; font-size: 1rem; margin-bottom: 0.5rem;">
                <i class="bi bi-clock-history me-2"></i> MARSHALL RESOLUTION WINDOW
            </p>
            <div id="countdown-timer" class="display-6 fw-bold" style="color: #fff; font-family: 'Orbitron', sans-serif; text-shadow: 0 0 10px #ffdd00;">--:--</div>
            <p class="text-warning small mt-2 mb-0">
                <i class="bi bi-exclamation-triangle-fill"></i> Conflict detected. Marshall must resolve this within 1 hour.
            </p>

            <script>
                (function() {
                    const targetDate = new Date({{ $battle->updated_at->addHour()->timestamp * 1000 }});
                    const timerEl = document.getElementById('countdown-timer');
                    const countdownContainer = document.getElementById('marshall-countdown');
                    
                    function updateTimer() {
                        const now = new Date();
                        const diff = targetDate - now;
                        
                        if (diff <= 0) {
                            timerEl.innerText = "EXPIRED";
                            timerEl.style.color = "#ff4444";
                            timerEl.style.textShadow = "0 0 10px #ff4444";
                            countdownContainer.style.borderColor = "#ff4444";
                            countdownContainer.style.background = "rgba(255, 68, 68, 0.1)";
                            
                            // Disable marshall buttons if they exist in the DOM
                            document.querySelectorAll('.marshall-btn').forEach(btn => btn.disabled = true);
                            return;
                        }
                        
                        const minutes = Math.floor(diff / 60000);
                        const seconds = Math.floor((diff % 60000) / 1000);
                        timerEl.innerText = `${minutes}m ${seconds.toString().padStart(2, '0')}s`;
                        
                        if (diff < 300000) { // Less than 5 mins
                            countdownContainer.classList.add('animate-pulse-marshall');
                        }
                    }
                    
                    setInterval(updateTimer, 1000);
                    updateTimer();
                })();
            </script>
            <style>
                @keyframes pulse-marshall {
                    0% { box-shadow: 0 0 0 0 rgba(255, 221, 0, 0.4); }
                    70% { box-shadow: 0 0 0 15px rgba(255, 221, 0, 0); }
                    100% { box-shadow: 0 0 0 0 rgba(255, 221, 0, 0); }
                }
                .animate-pulse-marshall {
                    animation: pulse-marshall 2s infinite;
                }
            </style>
        </div>
    @endif

    @if($battle->marshall_id)
        <div class="mt-3">
            <span class="badge bg-warning text-dark">
                <i class="bi bi-shield-check"></i> MARSHALL: 
                @if($battle->marshall->is_verified)
                    <i class="bi bi-patch-check-fill text-primary me-1" title="Verified User"></i>
                @endif
                {{ $battle->marshall->username }}
            </span>
        </div>
    @elseif($battle->team_a_marshall_elect && $battle->team_a_marshall_elect == $battle->team_b_marshall_elect && !in_array($battle->status, ['completed', 'cancelled']))
        @if(Auth::id() == $battle->team_a_marshall_elect)
            <div class="mt-3 p-3" style="background: rgba(255, 221, 0, 0.1); border: 1px solid #ffdd00; border-radius: 8px;">
                <p style="color: #ffdd00; font-family: 'Orbitron', sans-serif; font-size: 0.9rem; margin-bottom: 0.5rem;"><i class="bi bi-shield-exclamation"></i> MARSHALL ELECTION</p>
                <p class="small text-light mb-3">Both team leaders have elected you to adjudicate this team battle. Do you accept this responsibility?</p>
                <div class="d-flex gap-2">
                    <form action="{{ route('battles.action.accept_marshall', $battle) }}" method="POST" class="d-inline w-50">@csrf <button type="submit" class="btn btn-sm btn-neon w-100" style="border-color: #ffdd00; color: #ffdd00;"><i class="bi bi-check-lg"></i> ACCEPT</button></form>
                    <form action="{{ route('battles.action.reject_marshall', $battle) }}" method="POST" class="d-inline w-50">@csrf <button type="submit" class="btn btn-sm btn-outline-light w-100"><i class="bi bi-x-lg"></i> REJECT</button></form>
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
