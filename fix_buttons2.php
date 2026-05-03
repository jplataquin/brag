<?php
$html = file_get_contents('resources/views/battles/room.blade.php');

$html = str_replace(
    '<button type="button" class="btn btn-outline-cyan btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal">JOIN</button>',
    '<button type="button" class="btn btn-outline-cyan btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal" onclick="document.getElementById(\'joiningTeam\').value=\'A\'; document.getElementById(\'pairingSlot\').value={{ $i }}; document.getElementById(\'join_team_name\').innerText=\'A\';">JOIN</button>',
    $html
);

$html = str_replace(
    '<button type="button" class="btn btn-outline-magenta btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal">JOIN</button>',
    '<button type="button" class="btn btn-outline-magenta btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal" onclick="document.getElementById(\'joiningTeam\').value=\'B\'; document.getElementById(\'pairingSlot\').value={{ $i }}; document.getElementById(\'join_team_name\').innerText=\'B\';">JOIN</button>',
    $html
);

// Join Modal form
$html = preg_replace(
    '/<h4 class="orbitron text-cyan mb-4 text-center">JOIN TEAM<\/h4>.*?<label class="form-label small text-center w-100 mb-3" style="color: #39ff14;">/s',
    '<h4 class="orbitron text-cyan mb-4 text-center">JOIN TEAM <span id="join_team_name"></span></h4> <form action="{{ route(\'battles.action.join\', $battle) }}" method="POST">@csrf <input type="hidden" name="joiningTeam" id="joiningTeam" value=""><input type="hidden" name="pairingSlot" id="pairingSlot" value=""><input type="hidden" name="selectedCardId" id="selectedCardId" value=""> <div class="mb-4"><label class="form-label small text-center w-100 mb-3" style="color: #39ff14;">',
    $html
);

$html = str_replace(
    '<button type="button" class="btn btn-neon w-50 py-2 orbitron" data-bs-dismiss="modal" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="confirmJoin">CONFIRM JOIN</span>
                        <span wire:loading wire:target="confirmJoin"><i class="bi bi-hourglass-split"></i> JOINING...</span>
                    </button>',
    '<button type="submit" class="btn btn-neon w-50 py-2 orbitron">CONFIRM JOIN</button></form>',
    $html
);

file_put_contents('resources/views/battles/room.blade.php', $html);
