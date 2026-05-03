<?php
$html = file_get_contents('resources/views/battles/room.blade.php');

// Add extends and section
$html = "@extends('layouts.app')\n@section('title', 'Battle Room #' . \$battle->id)\n@section('content')\n" . $html . "\n@endsection\n";

// Remove the top <div> we added for Livewire root
$html = preg_replace('/<div>\n<style>/', '<style>', $html, 1);
$html = preg_replace('/<\/div>\n@endsection$/', '@endsection', $html, 1);

// Remove wire: properties
$html = preg_replace('/\s*wire:key="[^"]+"/', '', $html);
$html = preg_replace('/\s*wire:ignore\.self/', '', $html);
$html = preg_replace('/\s*wire:ignore/', '', $html);
$html = preg_replace('/\s*wire:model(\.live\.debounce\.[0-9]+ms)?="[^"]+"/', '', $html);

// Remove the Livewire Status and Activity Log (safely)
// We will find the exact blocks and replace them.

$startStatus = strpos($html, '<div class="neon-card p-4 mb-4">');
$endStatus = strpos($html, '<!-- Participant Battle Actions -->');
if ($startStatus !== false && $endStatus !== false) {
    $statusBlock = substr($html, $startStatus, $endStatus - $startStatus);
    $html = str_replace($statusBlock, '<livewire:battle-status :battle="$battle" />' . "\n\n", $html);
}

$startLog = strpos($html, '<!-- Activity Log -->');
$endLog = strpos($html, '<!-- Join Modal (Simulated) -->');
if ($startLog !== false && $endLog !== false) {
    $logBlock = substr($html, $startLog, $endLog - $startLog);
    $html = str_replace($logBlock, '<div class="col-lg-3 mt-4 mt-lg-0"><livewire:battle-activity-log :battle="$battle" /></div>' . "\n\n", $html);
}

// Convert Buttons to Forms
function replaceButtonWithForm($html, $searchPattern, $formAction, $method="POST", $btnClasses="", $btnText="", $innerHtml="", $onclick="") {
    $formHtml = '<form action="' . $formAction . '" method="' . $method . '" class="d-inline">@csrf ';
    if ($innerHtml) $formHtml .= $innerHtml;
    $onclickAttr = $onclick ? ' onclick="' . $onclick . '"' : '';
    $formHtml .= '<button type="submit" class="' . $btnClasses . '"' . $onclickAttr . '>' . $btnText . '</button></form>';
    return preg_replace($searchPattern, $formHtml, $html);
}

// 1. Start Match
$html = preg_replace('/<button[^>]*wire:click\.prevent="startBattle"[^>]*>.*?START MATCH\s*<\/button>/s', 
    '<form action="{{ route(\'battles.action.start\', $battle) }}" method="POST" class="d-inline w-100">@csrf <button type="submit" class="btn btn-neon-lime w-100" style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);"><i class="bi bi-play-fill"></i> START MATCH</button></form>', $html);

// 2. Cancel Battle
$html = preg_replace('/<button[^>]*wire:click\.prevent="cancelBattle"[^>]*>.*?CANCEL BATTLE\s*<\/button>/s', 
    '<form action="{{ route(\'battles.action.cancel\', $battle) }}" method="POST" class="d-inline w-100">@csrf <button type="submit" class="btn btn-neon-danger w-100"><i class="bi bi-x-circle"></i> CANCEL BATTLE</button></form>', $html);
$html = preg_replace('/<button[^>]*wire:click\.prevent="cancelBattle"[^>]*>.*?REQUEST CANCEL\s*<\/button>/s', 
    '<form action="{{ route(\'battles.action.cancel\', $battle) }}" method="POST" class="d-inline">@csrf <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle"></i> REQUEST CANCEL</button></form>', $html);

// 3. Team B Ready
$html = preg_replace('/<button[^>]*wire:click\.prevent="teamBReady"[^>]*>.*?READY\s*<\/button>/s', 
    '<form action="{{ route(\'battles.action.ready\', $battle) }}" method="POST" class="d-inline w-100">@csrf <button type="submit" class="btn btn-neon-lime w-100" style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);"><i class="bi bi-check2-all"></i> READY</button></form>', $html);

// 4. Stand Up
$html = preg_replace('/<button[^>]*wire:click\.prevent="standUp"[^>]*>.*?STAND UP\s*<\/button>/s', 
    '<form action="{{ route(\'battles.action.standup\', $battle) }}" method="POST" class="d-inline w-100">@csrf <button type="submit" class="btn btn-outline-warning w-100"><i class="bi bi-box-arrow-right"></i> STAND UP</button></form>', $html);

// 5. Declare Win / Cancel Match (Marshall)
$html = preg_replace('/<button[^>]*wire:click\.prevent="declareWin\(\'A\'\)"[^>]*>.*?TEAM A WON\s*<\/button>/s', 
    '<form action="{{ route(\'battles.action.declare_win\', $battle) }}" method="POST" class="d-inline">@csrf <input type="hidden" name="team" value="A"><button type="submit" class="btn btn-neon btn-sm" onclick="return confirm(\'Declare TEAM A as the winner?\')">TEAM A WON</button></form>', $html);
$html = preg_replace('/<button[^>]*wire:click\.prevent="declareWin\(\'B\'\)"[^>]*>.*?TEAM B WON\s*<\/button>/s', 
    '<form action="{{ route(\'battles.action.declare_win\', $battle) }}" method="POST" class="d-inline">@csrf <input type="hidden" name="team" value="B"><button type="submit" class="btn btn-neon-magenta btn-sm" onclick="return confirm(\'Declare TEAM B as the winner?\')">TEAM B WON</button></form>', $html);

// 6. Join Modal Button Openers
$html = str_replace('wire:click.prevent="joinTeam(\'A\', {{ $i }})"', 'onclick="document.getElementById(\'joiningTeam\').value=\'A\'; document.getElementById(\'pairingSlot\').value={{ $i }}; document.getElementById(\'join_team_name\').innerText=\'A\';"', $html);
$html = str_replace('wire:click.prevent="joinTeam(\'B\', {{ $i }})"', 'onclick="document.getElementById(\'joiningTeam\').value=\'B\'; document.getElementById(\'pairingSlot\').value={{ $i }}; document.getElementById(\'join_team_name\').innerText=\'B\';"', $html);

// 7. Rename Modal Button Openers
$html = str_replace('data-bs-target="#renameTeamModal"', 'data-bs-target="#renameTeamModal" onclick="document.getElementById(\'renameTeamInput\').value=\'{{ $isLeaderA ? $battle->team_name_a : $battle->team_name_b }}\'; document.getElementById(\'renameTeamVal\').value=\'{{ $isLeaderA ? \\\'A\\\' : \\\'B\\\' }}\'; document.getElementById(\'rename_team_name\').innerText=\'{{ $isLeaderA ? \\\'A\\\' : \\\'B\\\' }}\';"', $html);

// Now fix the modals themselves.
// Rename Modal
$renameModal = <<<HTML
    <div class="modal fade" id="renameTeamModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" style="background: rgba(0, 0, 0, 0.8);">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('battles.action.rename', \$battle) }}" method="POST" class="w-100">
            @csrf
            <input type="hidden" name="team" id="renameTeamVal" value="">
            <div class="modal-content p-4 neon-card" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #00f0ff; backdrop-filter: blur(20px);">
                <h5 class="orbitron text-cyan mb-4 text-center">RENAME TEAM <span id="rename_team_name"></span></h5>
                <div class="mb-4">
                    <input type="text" name="name" id="renameTeamInput" class="form-control bg-dark text-white border-cyan text-center orbitron" placeholder="Enter new team name" required>
                </div>
                <div class="d-flex gap-3">
                    <button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-neon w-50 orbitron">SAVE</button>
                </div>
            </div>
            </form>
        </div>
    </div>
HTML;
$html = preg_replace('/<!-- Rename Team Modal -->.*?<!-- Elect Marshall Modal -->/s', "<!-- Rename Team Modal -->\n" . $renameModal . "\n\n    <!-- Elect Marshall Modal -->", $html);


// Cancel Modal
$cancelModal = <<<HTML
    <!-- Cancellation Request Modal -->
    @php
        \$showCancelModal = false;
        \$requesterName = '';
        if (\$battle->status !== 'cancelled' && \$battle->status !== 'completed') {
            if (\$battle->team_a_cancel_flag && Auth::id() == \$battle->team_b_user_1) {
                \$showCancelModal = true;
                \$requesterName = \App\Models\User::find(\$battle->team_a_user_1)?->username ?? 'Team A Leader';
            } elseif (\$battle->team_b_cancel_flag && Auth::id() == \$battle->team_a_user_1) {
                \$showCancelModal = true;
                \$requesterName = \App\Models\User::find(\$battle->team_b_user_1)?->username ?? 'Team B Leader';
            }
        }
    @endphp

    @if(\$showCancelModal)
    <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0, 0, 0, 0.8);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #ff00ff; backdrop-filter: blur(20px); box-shadow: 0 0 30px rgba(255, 0, 255, 0.2);">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title neon-text-magenta">CANCELLATION REQUEST</h5>
                </div>
                <div class="modal-body py-4 text-center">
                    <div class="mb-4">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem; color: #ff00ff; opacity: 0.8;"></i>
                    </div>
                    <p class="mb-4" style="font-size: 1.1rem;">
                        <strong id="cancel-requester-name">{{ \$requesterName }}</strong> has requested to cancel this battle. 
                        Do you agree to cancel the match?
                    </p>
                    <p class="text-muted small mb-4">
                        If you agree, the battle will be cancelled and no cards will be transferred.
                        If you reject, the battle will continue.
                    </p>
                    
                    <div class="d-flex gap-3">
                        <form action="{{ route('battles.action.respond_cancel', \$battle) }}" method="POST" class="w-100">
                            @csrf <input type="hidden" name="agreed" value="1">
                            <button type="submit" class="btn btn-neon-magenta w-100"><i class="bi bi-check-lg"></i> AGREE & CANCEL</button>
                        </form>
                        <form action="{{ route('battles.action.respond_cancel', \$battle) }}" method="POST" class="w-100">
                            @csrf <input type="hidden" name="agreed" value="0">
                            <button type="submit" class="btn btn-outline-secondary w-100" style="border-color: #555;"><i class="bi bi-x-lg"></i> REJECT</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
HTML;
$html = preg_replace('/<!-- Cancellation Request Modal -->.*?<style>/s', $cancelModal . "\n\n    <style>", $html);


// Join Modal
$joinStart = strpos($html, '<h4 class="orbitron text-cyan mb-4 text-center">JOIN TEAM');
$joinEnd = strpos($html, '<label class="form-label small text-center w-100 mb-3" style="color: #39ff14;">');
if ($joinStart !== false && $joinEnd !== false) {
    $html = substr_replace($html, 
        '<h4 class="orbitron text-cyan mb-4 text-center">JOIN TEAM <span id="join_team_name"></span></h4>' . "\n" .
        '<form action="{{ route(\'battles.action.join\', $battle) }}" method="POST" id="joinForm">@csrf <input type="hidden" name="joiningTeam" id="joiningTeam" value=""><input type="hidden" name="pairingSlot" id="pairingSlot" value=""><input type="hidden" name="selectedCardId" id="selectedCardId" value="">' . "\n",
        $joinStart, $joinEnd - $joinStart);
}

// Selectable card Javascript
$html = str_replace(
    'class="selectable-card {{ (int)$selectedCardId === (int)$card->id ? \'selected\' : \'\' }}" 
                                                         wire:click="selectCard({{ $card->id }})"
                                                         style="cursor: pointer;"',
    'class="selectable-card" onclick="document.querySelectorAll(\'.selectable-card\').forEach(e=>e.classList.remove(\'selected\')); this.classList.add(\'selected\'); document.getElementById(\'selectedCardId\').value=\'{{$card->id}}\';" style="cursor: pointer;"',
    $html
);
// Remove livewire check for card
$html = preg_replace('/@if\(\$selectedCardId == \$card->id\).*?@endif/s', '', $html);

// Join confirm button
$html = preg_replace(
    '/<button type="button" class="btn btn-neon w-50 py-2 orbitron" wire:click\.prevent="confirmJoin".*?<\/button>/s',
    '<button type="submit" class="btn btn-neon w-50 py-2 orbitron">CONFIRM JOIN</button></form>',
    $html
);

// Add Echo Listener
$script = <<<HTML
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if(window.Echo) {
            window.Echo.channel('battle.{{ \$battle->id }}')
                .listen('BattleUpdated', (e) => {
                    window.location.reload();
                });
        }
    });
</script>
HTML;

$html = str_replace('</script>', "</script>\n" . $script, $html);

file_put_contents('resources/views/battles/room.blade.php', $html);
