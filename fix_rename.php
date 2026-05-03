<?php
$html = file_get_contents('resources/views/battles/room.blade.php');
$html = preg_replace(
    '/<input type="text"\s*class="form-control bg-dark text-white border-cyan text-center orbitron" placeholder="Enter new team name">\s*<\/div>\s*<div class="d-flex gap-3">\s*<button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">CANCEL<\/button>\s*<button type="button" class="btn btn-neon w-50 orbitron" data-bs-dismiss="modal">SAVE<\/button>/',
    '<form action="{{ route(\'battles.action.rename\', $battle) }}" method="POST">@csrf <input type="hidden" name="team" value="A"><input type="text" name="name" class="form-control bg-dark text-white border-cyan text-center orbitron" placeholder="Enter new team name" required></div><div class="d-flex gap-3"><button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">CANCEL</button><button type="submit" class="btn btn-neon w-50 orbitron">SAVE</button></form>',
    $html
);
$html = preg_replace(
    '/<input type="text"\s*class="form-control bg-dark text-white border-magenta text-center orbitron" placeholder="Enter new team name">\s*<\/div>\s*<div class="d-flex gap-3">\s*<button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">CANCEL<\/button>\s*<button type="button" class="btn btn-neon-magenta w-50 orbitron" data-bs-dismiss="modal">SAVE<\/button>/',
    '<form action="{{ route(\'battles.action.rename\', $battle) }}" method="POST">@csrf <input type="hidden" name="team" value="B"><input type="text" name="name" class="form-control bg-dark text-white border-magenta text-center orbitron" placeholder="Enter new team name" required></div><div class="d-flex gap-3"><button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">CANCEL</button><button type="submit" class="btn btn-neon-magenta w-50 orbitron">SAVE</button></form>',
    $html
);
file_put_contents('resources/views/battles/room.blade.php', $html);
