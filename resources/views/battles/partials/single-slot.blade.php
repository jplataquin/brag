@php
    $cardClass = "";
    if ($isMe && $battle->status != 'completed') {
        $cardClass = "current-player-slot-" . strtolower($team);
    }
    
    $cardStyle = "";
    if ($isFinal) {
        if ($battle->winner_team == $team) {
            $cardStyle = "box-shadow: 0 0 30px rgba(255, 221, 0, 0.6); border-radius: 16px; transform: scale(1.02); transition: all 0.3s ease;";
        } else {
            $cardStyle = "opacity: 0.5; filter: grayscale(80%); transition: all 0.3s ease;";
        }
    }
    $i = $slot;
@endphp

@if($u && $c)
    <div class="mb-2 text-center text-truncate">
        <span class="fw-bold @if($isMe) text-cyan @endif">
            @if($i == 1)
            👑
            @endif
        
            {{ $isMe ? 'YOU' : $u->username }}

            @if($u->is_verified)
                <i class="bi bi-patch-check-fill text-primary me-1" title="Verified User"></i>
            @endif
        </span>
    </div>
    <div class="{{ $cardClass }}" style="{{ $cardStyle }}">
        <x-digital-card 
            id="card_{{ strtolower($team) }}_{{ $i }}_{{ $c->id ?? 'none' }}"
            mode="thumbnail"
            fullscreen="true"
            :title="$c->is_censored ? '[CENSORED]' : $c->template->card_title"
            :game="$c->template->gameTitle->title ?? 'GAME'"
            :creator="$c->originalOwner->username ?? 'Creator'"
            :isCreatorVerified="$c->originalOwner->is_verified ?? false"
            :isCreatorUntrustworthy="$c->originalOwner->is_untrustworthy ?? false"
            :quote="$c->is_censored ? '[Content hidden pending review]' : $c->template->quote"
            :image="$c->is_censored ? '' : $c->template->display_photo"
            :imagePositionY="$c->template->image_position_y ?? 50"
            :backgroundColor="$c->template->background_color"
            :borderColor="$c->template->border_color"
            :sectionColor="$c->template->section_color"
            :primaryTextColor="$c->template->primary_text_color"
            :secondaryTextColor="$c->template->secondary_text_color"
            :wins="$snapshot ? $snapshot['wins'] : $c->wins"
            :losses="$snapshot ? $snapshot['losses'] : $c->losses"
            :integrityStat="$snapshot ? $snapshot['integrity_stat'] : $c->integrity_stat"
            :lifePoints="$snapshot ? $snapshot['life_points'] : $c->life_points"
            :status="$snapshot ? $snapshot['status'] : $c->status"
            :rankLevel="$snapshot ? $snapshot['level'] : $c->level"
            :serialNumber="$c->serial_number"
            :rarity="$snapshot ? $snapshot['rarity'] : $c->rarity"
            :cardId="$c->id"
            :ownerId="$c->owner_id"
            :isCensored="$c->is_censored"
        />
    </div>
@else
    <div class="mb-2 text-center text-truncate">
        <span class="fw-bold text-muted">
            @if($i == 1)
            👑
            @endif
            ----</span>
    </div>
    <div class="empty-card-slot d-flex flex-column align-items-center justify-content-center p-3 rounded" style="border: 2px dashed rgba(0, 240, 255, 0.4); background: rgba(0, 240, 255, 0.05); aspect-ratio: 350 / 490; width: 100%;">
        <div class="orbitron text-muted mb-3 fs-5">SLOT {{ $i }}</div>
        @if($battle->status == 'pending' && Auth::id() != $battle->team_a_user_1)
            <button type="button" class="btn btn-outline-{{ $team == 'A' ? 'cyan' : 'magenta' }} btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal" onclick="document.getElementById('joiningTeam').value='{{ $team }}'; document.getElementById('pairingSlot').value={{ $i }}; document.getElementById('join_team_name').innerText='{{ $team }}';">JOIN</button>
        @endif
    </div>
@endif
