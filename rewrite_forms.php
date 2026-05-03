<?php
$html = file_get_contents('resources/views/battles/room.blade.php');

// Start Match
$html = preg_replace(
    '/<button type="button" class="btn btn-neon-lime"(.*?)START MATCH\s*<\/button>/s',
    '<form action="{{ route(\'battles.action.start\', $battle) }}" method="POST" class="d-inline">@csrf <button type="submit" class="btn btn-neon-lime" style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);"><i class="bi bi-play-fill"></i> START MATCH</button></form>',
    $html
);

// Cancel Battle
$html = preg_replace(
    '/<button type="button" class="btn btn-neon-danger"(.*?)CANCEL BATTLE\s*<\/button>/s',
    '<form action="{{ route(\'battles.action.cancel\', $battle) }}" method="POST" class="d-inline">@csrf <button type="submit" class="btn btn-neon-danger"><i class="bi bi-x-circle"></i> CANCEL BATTLE</button></form>',
    $html
);

// Request Cancel
$html = preg_replace(
    '/<button type="button" class="btn btn-outline-danger btn-sm"(.*?)REQUEST CANCEL\s*<\/button>/s',
    '<form action="{{ route(\'battles.action.cancel\', $battle) }}" method="POST" class="d-inline">@csrf <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle"></i> REQUEST CANCEL</button></form>',
    $html
);

// Team B Ready
$html = preg_replace(
    '/<button type="button" class="btn btn-neon-lime"(.*?)READY\s*<\/button>/s',
    '<form action="{{ route(\'battles.action.ready\', $battle) }}" method="POST" class="d-inline">@csrf <button type="submit" class="btn btn-neon-lime" style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);"><i class="bi bi-check2-all"></i> READY</button></form>',
    $html
);

// Stand Up
$html = preg_replace(
    '/<button type="button" class="btn btn-outline-warning"(.*?)STAND UP\s*<\/button>/s',
    '<form action="{{ route(\'battles.action.standup\', $battle) }}" method="POST" class="d-inline">@csrf <button type="submit" class="btn btn-outline-warning"><i class="bi bi-box-arrow-right"></i> STAND UP</button></form>',
    $html
);

// Declare Win A / B / Cancel Match
$html = preg_replace(
    '/<button type="button" class="btn btn-neon btn-sm"(.*?)TEAM A WON<\/button>/s',
    '<form action="{{ route(\'battles.action.declare_win\', $battle) }}" method="POST" class="d-inline">@csrf <input type="hidden" name="team" value="A"><button type="submit" class="btn btn-neon btn-sm" onclick="return confirm(\'Declare TEAM A as the winner?\')">TEAM A WON</button></form>',
    $html
);
$html = preg_replace(
    '/<button type="button" class="btn btn-neon-magenta btn-sm"(.*?)TEAM B WON<\/button>/s',
    '<form action="{{ route(\'battles.action.declare_win\', $battle) }}" method="POST" class="d-inline">@csrf <input type="hidden" name="team" value="B"><button type="submit" class="btn btn-neon-magenta btn-sm" onclick="return confirm(\'Declare TEAM B as the winner?\')">TEAM B WON</button></form>',
    $html
);
$html = preg_replace(
    '/<button type="button" class="btn btn-neon-danger btn-sm"(.*?)CANCEL MATCH<\/button>/s',
    '<form action="{{ route(\'battles.action.cancel\', $battle) }}" method="POST" class="d-inline">@csrf <button type="submit" class="btn btn-neon-danger btn-sm" onclick="return confirm(\'CANCEL this match?\')">CANCEL MATCH</button></form>',
    $html
);

// Rename Team A
$html = preg_replace(
    '/<div class="mb-4">\s*<input type="text"\s*class="form-control bg-dark text-white border-cyan text-center orbitron" placeholder="Enter new team name">\s*<\/div>\s*<div class="d-flex gap-3">\s*<button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">CANCEL<\/button>\s*<button type="button" class="btn btn-neon w-50 orbitron" data-bs-dismiss="modal">SAVE<\/button>\s*<\/div>/s',
    '<form action="{{ route(\'battles.action.rename\', $battle) }}" method="POST">@csrf <input type="hidden" name="team" value="A"> <div class="mb-4"><input type="text" name="name" class="form-control bg-dark text-white border-cyan text-center orbitron" placeholder="Enter new team name" required></div> <div class="d-flex gap-3"><button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">CANCEL</button><button type="submit" class="btn btn-neon w-50 orbitron">SAVE</button></div></form>',
    $html
);

// Rename Team B
$html = preg_replace(
    '/<div class="mb-4">\s*<input type="text"\s*class="form-control bg-dark text-white border-magenta text-center orbitron" placeholder="Enter new team name">\s*<\/div>\s*<div class="d-flex gap-3">\s*<button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">CANCEL<\/button>\s*<button type="button" class="btn btn-neon-magenta w-50 orbitron" data-bs-dismiss="modal">SAVE<\/button>\s*<\/div>/s',
    '<form action="{{ route(\'battles.action.rename\', $battle) }}" method="POST">@csrf <input type="hidden" name="team" value="B"> <div class="mb-4"><input type="text" name="name" class="form-control bg-dark text-white border-magenta text-center orbitron" placeholder="Enter new team name" required></div> <div class="d-flex gap-3"><button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">CANCEL</button><button type="submit" class="btn btn-neon-magenta w-50 orbitron">SAVE</button></div></form>',
    $html
);

// Respond Cancellation Modal
$html = preg_replace(
    '/<button type="button" class="btn btn-neon-magenta w-100" >\s*<i class="bi bi-check-lg"><\/i> AGREE & CANCEL\s*<\/button>\s*<button type="button" class="btn btn-outline-secondary w-100" style="border-color: #555;" >\s*<i class="bi bi-x-lg"><\/i> REJECT\s*<\/button>/s',
    '<form action="{{ route(\'battles.action.respond_cancel\', $battle) }}" method="POST" class="w-100">@csrf <input type="hidden" name="agreed" value="1"><button type="submit" class="btn btn-neon-magenta w-100"><i class="bi bi-check-lg"></i> AGREE & CANCEL</button></form> <form action="{{ route(\'battles.action.respond_cancel\', $battle) }}" method="POST" class="w-100">@csrf <input type="hidden" name="agreed" value="0"><button type="submit" class="btn btn-outline-secondary w-100" style="border-color: #555;"><i class="bi bi-x-lg"></i> REJECT</button></form>',
    $html
);

// Join Modal form
$html = preg_replace(
    '/<div class="mb-4">\s*<label class="form-label small text-center w-100 mb-3"(.*?)<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<!-- Rename/s',
    '<form action="{{ route(\'battles.action.join\', $battle) }}" method="POST">@csrf <input type="hidden" name="joiningTeam" id="joiningTeam" value=""><input type="hidden" name="pairingSlot" id="pairingSlot" value=""><input type="hidden" name="selectedCardId" id="selectedCardId" value=""> <div class="mb-4"><label class="form-label small text-center w-100 mb-3"$1</div><div class="d-flex gap-3 mt-4"><button type="button" class="btn btn-outline-secondary w-50 py-2" data-bs-dismiss="modal">CANCEL</button><button type="submit" class="btn btn-neon w-50 py-2 orbitron">CONFIRM JOIN</button></div></div></form></div></div><!-- Rename',
    $html
);


file_put_contents('resources/views/battles/room.blade.php', $html);
