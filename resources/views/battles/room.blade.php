@extends('layouts.app')

@section('title', 'Battle Room #' . $battle->id)

@section('content')
<div class="mb-3">
    <a href="{{ route('battles.index') }}" style="color: #8888aa; font-size: 0.85rem; text-decoration: none;">
        <i class="bi bi-arrow-left"></i> Back to Arena
    </a>
</div>

    <livewire:battle-room :battle="$battle" />
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($showCancelModal)
            const initialCancelModal = new bootstrap.Modal(document.getElementById('cancellationRequestModal'));
            initialCancelModal.show();
        @endif

        const qrcodeContainer = document.getElementById('qrcode');
        if (qrcodeContainer) {
            new QRCode(qrcodeContainer, {
                text: "{{ url()->current() }}",
                width: 200,
                height: 200,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        }

        // Scroll activity log to bottom on load
        scrollLogToBottom();

        // Websocket Listener via Laravel Echo
        if (window.Echo) {
            window.Echo.channel('battle.{{ $battle->room_id }}')
                .listen('BattleUpdated', (e) => {
                    // Check for rejection
                    if (e.type && e.type.startsWith('reject_')) {
                        const rejectedId = e.type.split('_')[1];
                        if (rejectedId == {{ Auth::id() }}) {
                            // This user was rejected
                            const rejectModal = new bootstrap.Modal(document.getElementById('rejectedOpponentModal'));
                            rejectModal.show();
                            return; // Stop processing other updates for this user
                        } 
                    }

                    // Show Notification
                    if (e.message) {
                        showNeonNotification(e.message, e.type);
                    }

                    
                    // For major structural changes (like someone joining or the battle completing),
                    

                    // Handle cancel_request without reloading
                    if (e.type && e.type.startsWith('cancel_request_')) {
                        const requesterId = parseInt(e.type.split('_')[2]);
                        
                        // If this user is the one who requested it, don't show them the modal!
                        if (requesterId !== {{ Auth::id() }}) {
                            const messageText = e.message || '';
                            const requesterName = messageText.split(' ')[0] || 'Opponent';
                            const requesterNameEl = document.getElementById('cancel-requester-name');
                            if (requesterNameEl) {
                                requesterNameEl.innerText = requesterName;
                            }
                            const cancelModal = new bootstrap.Modal(document.getElementById('cancellationRequestModal'));
                            cancelModal.show();
                        }
                    }

                    // For lightweight changes, we just append to the activity log dynamically
                    // Exclude poke events from the activity log per requirement
                    if (!e.type.startsWith('poke') && !e.type.startsWith('cancel_request_')) {
                        appendActivity(e.message, e.type);
                    } else if (e.type.startsWith('cancel_request_')) {
                        appendActivity(e.message, 'cancel_request');
                    }
                });
        }
    });

    async function declareWinnerAjax(winnerId, btn) {
        const message = btn.getAttribute('data-message');
        const result = await window.neonConfirm(message);
        
        if (!result) return;
        
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<div class="text-center w-100 py-2"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> DECLARING...</div>';
        btn.disabled = true;

        fetch(`{{ route('battles.declareWinner', $battle) }}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ winner_id: winnerId })
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
                return null;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;
            if (data.success) {
                showNeonNotification(data.message, 'declare');
                
                // Reset button text and state
                btn.innerHTML = originalHtml;
                btn.disabled = false;

                // Update styling to add yellow highlight
                const btnLost = document.getElementById('btn-declare-lost');
                const btnWon = document.getElementById('btn-declare-won');
                
                if (btnLost && btnWon) {
                    btnLost.style.boxShadow = 'none';
                    btnLost.style.setProperty('border-color', '', 'important');
                    btnWon.style.boxShadow = 'none';
                    btnWon.style.setProperty('border-color', '', 'important');
                    
                    btn.style.boxShadow = '0 0 20px #ffdd00';
                    btn.style.setProperty('border-color', '#ffdd00', 'important');
                }

                const declareModal = bootstrap.Modal.getInstance(document.getElementById('declareWinnerModal'));
                if (declareModal) declareModal.hide();
            } else {
                showNeonNotification(data.message, 'conflict');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        })
        .catch(err => {
            showNeonNotification('An error occurred while declaring winner.', 'conflict');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }

    async function cancelBattle(btn) {
        const message = btn.getAttribute('data-message');
        const result = await window.neonConfirm(message);
        
        if (!result) return;
        
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <span class="btn-text">PROCESSING...</span>';
        btn.disabled = true;

        fetch(`{{ route('battles.cancel', $battle) }}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
                return null;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;
            if(data.success) {
                if (data.status === 'cancelled') {
                    showNeonNotification(data.message, 'cancel');
                    setTimeout(() => {
                        window.location.href = "{{ route('battles.index') }}";
                    }, 1000);
                } else {
                    showNeonNotification(data.message, 'cancel_request');
                    btn.style.display = 'none'; // hide the button since request was sent
                }
            } else {
                showNeonNotification(data.message, 'conflict');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        })
        .catch(err => {
            showNeonNotification('An error occurred while cancelling.', 'conflict');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }

    function pokePlayer(btn) {
        if(btn.disabled) return;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> POKING...';
        btn.disabled = true;

        fetch(`{{ route('battles.poke', $battle) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showNeonNotification(data.message, 'poke');
            } else {
                showNeonNotification(data.message, 'conflict');
            }
        })
        .catch(err => {
            showNeonNotification('An error occurred while poking.', 'conflict');
        })
        .finally(() => {
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }, 2000);
        });
    }

    function getIconForActivity(type) {
        if (type && type.startsWith('reject_')) return '<i class="bi bi-person-x-fill text-danger"></i>';
        switch(type) {
            case 'create': return '<i class="bi bi-plus-circle-fill text-info"></i>';
            case 'join': return '<i class="bi bi-person-check-fill text-success"></i>';
            case 'invite': return '<i class="bi bi-envelope-fill text-warning"></i>';
            case 'elect_marshall': return '<i class="bi bi-shield-fill text-warning"></i>';
            case 'marshall_election': return '<i class="bi bi-shield-check text-warning"></i>';
            case 'marshall_accepted': return '<i class="bi bi-shield-lock-fill text-warning"></i>';
            case 'marshall_rejected': return '<i class="bi bi-shield-x text-danger"></i>';
            case 'marshall_leave': return '<i class="bi bi-box-arrow-right text-danger"></i>';
            case 'declare': return '<i class="bi bi-megaphone-fill text-info"></i>';
            case 'conflict': return '<i class="bi bi-exclamation-triangle-fill text-danger"></i>';
            case 'marshall_decision': return '<i class="bi bi-shield-lock-fill text-warning"></i>';
            case 'consensus': return '<i class="bi bi-people-fill text-success"></i>';
            case 'winner': return '<i class="bi bi-trophy-fill text-success"></i>';
            case 'cancel': return '<i class="bi bi-x-circle-fill text-danger"></i>';
            case 'cancel_request': return '<i class="bi bi-exclamation-circle-fill text-warning"></i>';
            case 'cancel_agree': return '<i class="bi bi-check-circle-fill text-success"></i>';
            case 'cancel_reject': return '<i class="bi bi-x-circle text-danger"></i>';
            case 'start': return '<i class="bi bi-play-circle-fill text-success"></i>';
            case 'poke': return '<i class="bi bi-hand-index-thumb-fill text-info"></i>';
            default: return '<i class="bi bi-dot text-muted"></i>';
        }
    }

    function appendActivity(message, type) {
        const list = document.getElementById('activity-logs-list');
        const empty = document.getElementById('empty-activity');
        if (empty) empty.remove();

        const escapedMessage = message.replace(/</g, "&lt;").replace(/>/g, "&gt;");
        const formattedMessage = escapedMessage.replace(/@([\w\-.]+)/g, '<a href="/user/$1" target="_blank" style="color: #ffdd00; text-decoration: none; font-weight: bold;">@$1</a>');

        const item = document.createElement('div');
        item.className = 'activity-item mb-3 pb-3';
        item.style.borderBottom = '1px solid rgba(255, 255, 255, 0.05)';
        item.innerHTML = `
            <div class="d-flex gap-2 align-items-start">
                <div class="activity-icon-sm mt-1">
                    ${getIconForActivity(type)}
                </div>
                <div>
                    <div style="font-size: 0.85rem; color: #fff;">${formattedMessage}</div>
                    <div style="font-size: 0.7rem; color: #555577;">Just now</div>
                </div>
            </div>
        `;
        list.appendChild(item);
        scrollLogToBottom();
    }

    function scrollLogToBottom() {
        const container = document.querySelector('.activity-log-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    function showNeonNotification(message, type) {
        const toast = document.createElement('div');
        toast.className = `neon-notification neon-notification-${type}`;
        toast.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-lightning-charge-fill"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => toast.classList.add('show'), 100);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 500);
        }, 5000);
    }

    function copyBattleUrl() {
        const copyText = document.getElementById("battle-url");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);

        const btn = event.target;
        const originalText = btn.innerText;
        btn.innerText = "COPIED!";
        setTimeout(() => { btn.innerText = originalText; }, 2000);
    }

    // Elect Marshall Auto-Suggest
    const adjWrapper = document.getElementById('adj-input-wrapper');
    const adjInput = document.getElementById('elect-marshall-input');
    const adjHidden = document.getElementById('adj-hidden-user-id');
    const adjChip = document.getElementById('adj-selected-chip');
    const adjChipText = document.getElementById('adj-chip-text');
    const adjResults = document.getElementById('elect-marshall-results');
    let adjDebounce = null;

    if (adjInput && adjResults) {
        if (adjWrapper) {
            adjWrapper.addEventListener('click', () => {
                if (!adjHidden.value) adjInput.focus();
            });
        }

        adjInput.addEventListener('input', function() {
            clearTimeout(adjDebounce);
            const q = this.value.trim();

            if (q.length < 2) {
                adjResults.classList.add('d-none');
                adjResults.innerHTML = '';
                return;
            }

            adjDebounce = setTimeout(() => {
                fetch(`/search?q=${encodeURIComponent(q)}&battle_id={{$battle->room_id }}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(users => {
                    if (users.length === 0) {
                        adjInput.value = '';
                        adjResults.innerHTML = '<div class="p-2 text-center text-muted small">No players found</div>';
                    } else {
                        adjResults.innerHTML = users.map(u => `
                            <div class="adj-search-item p-2 d-flex align-items-center gap-2" onmousedown="selectMarshall(${u.id}, '${u.username}')" style="cursor: pointer; border-bottom: 1px solid rgba(255, 221, 0, 0.1);">
                                <img src="${u.avatar_url}" alt="${u.username}" style="width: 24px; height: 24px; border-radius: 50%; border: 1px solid #ffdd00;">
                                <span class="text-white">@${u.username}</span>
                            </div>
                        `).join('');
                    }
                    adjResults.classList.remove('d-none');
                });
            }, 300);
        });

        // Clear input on blur if no user is selected
        adjInput.addEventListener('blur', function() {
            setTimeout(() => {
                if (!adjHidden.value) {
                    adjInput.value = '';
                    adjResults.classList.add('d-none');
                }
            }, 150); // Delay to allow onmousedown selection to trigger first
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!adjWrapper.contains(e.target) && !adjResults.contains(e.target)) {
                adjResults.classList.add('d-none');
            }
        });
    }

    window.selectMarshall = function(userId, username) {
        adjHidden.value = userId;
        adjInput.value = '';
        adjInput.classList.add('d-none');
        adjChipText.innerText = username;
        adjChip.classList.remove('d-none');
        adjResults.classList.add('d-none');
    }

    window.clearMarshallSelection = function() {
        adjHidden.value = '';
        adjChip.classList.add('d-none');
        adjInput.classList.remove('d-none');
        adjInput.focus();
    }

    // Invite Player Auto-Suggest
    const inviteWrapper = document.getElementById('invite-input-wrapper');
    const inviteInput = document.getElementById('invite-player-input');
    const inviteHidden = document.getElementById('invite-hidden-username');
    const inviteChip = document.getElementById('invite-selected-chip');
    const inviteChipText = document.getElementById('invite-chip-text');
    const inviteResults = document.getElementById('invite-player-results');
    let inviteDebounce = null;

    if (inviteInput && inviteResults) {
        if (inviteWrapper) {
            inviteWrapper.addEventListener('click', () => {
                if (!inviteHidden.value) inviteInput.focus();
            });
        }

        inviteInput.addEventListener('input', function() {
            clearTimeout(inviteDebounce);
            const q = this.value.trim();

            if (q.length < 2) {
                inviteResults.classList.add('d-none');
                inviteResults.innerHTML = '';
                return;
            }

            inviteDebounce = setTimeout(() => {
                fetch(`/search?q=${encodeURIComponent(q)}&battle_id={{$battle->room_id }}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(users => {
                    if (users.length === 0) {
                        inviteInput.value = '';
                        inviteResults.innerHTML = '<div class="p-2 text-center text-muted small">No players found</div>';
                    } else {
                        inviteResults.innerHTML = users.map(u => `
                            <div class="adj-search-item p-2 d-flex align-items-center gap-2" onmousedown="selectInvitePlayer('${u.username}')" style="cursor: pointer; border-bottom: 1px solid rgba(0, 240, 255, 0.1);">
                                <img src="${u.avatar_url}" alt="${u.username}" style="width: 24px; height: 24px; border-radius: 50%; border: 1px solid #00f0ff;">
                                <span class="text-white">@${u.username}</span>
                            </div>
                        `).join('');
                    }
                    inviteResults.classList.remove('d-none');
                });
            }, 300);
        });

        // Clear input on blur if no user is selected
        inviteInput.addEventListener('blur', function() {
            setTimeout(() => {
                if (!inviteHidden.value) {
                    inviteInput.value = '';
                    inviteResults.classList.add('d-none');
                }
            }, 150); // Delay to allow onmousedown selection to trigger first
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!inviteWrapper.contains(e.target) && !inviteResults.contains(e.target)) {
                inviteResults.classList.add('d-none');
            }
        });
    }

    window.selectInvitePlayer = function(username) {
        inviteHidden.value = username;
        inviteInput.value = '';
        inviteInput.classList.add('d-none');
        inviteChipText.innerText = username;
        inviteChip.classList.remove('d-none');
        inviteResults.classList.add('d-none');
    }

    window.clearInviteSelection = function() {
        inviteHidden.value = '';
        inviteChip.classList.add('d-none');
        inviteInput.classList.remove('d-none');
        inviteInput.focus();
    }

</script>

<style>
    .neon-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: rgba(10, 10, 30, 0.95);
        border: 1px solid #00f0ff;
        border-radius: 12px;
        color: #fff;
        font-family: 'Orbitron', sans-serif;
        font-size: 0.85rem;
        z-index: 9999;
        box-shadow: 0 0 20px rgba(0, 240, 255, 0.3);
        transform: translateX(120%);
        transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        backdrop-filter: blur(10px);
    }
    .neon-notification.show {
        transform: translateX(0);
    }
    .neon-notification-join { border-color: #39ff14; box-shadow: 0 0 20px rgba(57, 255, 20, 0.3); }
    .neon-notification-start { border-color: #ff00ff; box-shadow: 0 0 20px rgba(255, 0, 255, 0.3); }
    .neon-notification-winner { border-color: #ffdd00; box-shadow: 0 0 20px rgba(255, 221, 0, 0.3); }
    .neon-notification-cancel { border-color: #ff0055; box-shadow: 0 0 20px rgba(255, 0, 85, 0.3); }
    .neon-notification-cancel_request { border-color: #ffdd00; box-shadow: 0 0 20px rgba(255, 221, 0, 0.3); }
    .neon-notification-cancel_reject { border-color: #ff0055; box-shadow: 0 0 20px rgba(255, 0, 85, 0.3); }
    .neon-notification-declare { border-color: #00f0ff; box-shadow: 0 0 20px rgba(0, 240, 255, 0.3); }
    .neon-notification-conflict { border-color: #ff0055; box-shadow: 0 0 20px rgba(255, 0, 85, 0.3); }
    .neon-notification-marshall_decision { border-color: #ffdd00; box-shadow: 0 0 20px rgba(255, 221, 0, 0.3); }
    .neon-notification-consensus { border-color: #39ff14; box-shadow: 0 0 20px rgba(57, 255, 20, 0.3); }
    .neon-notification-poke { border-color: #00f0ff; box-shadow: 0 0 20px rgba(0, 240, 255, 0.3); }

    @keyframes pulse-yellow {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.7; transform: scale(0.98); }
        100% { opacity: 1; transform: scale(1); }
    }

    .adj-search-item {
        transition: background 0.2s ease;
    }
    .adj-search-item:hover {
        background: rgba(255, 221, 0, 0.15) !important;
    }
</style>
@endsection