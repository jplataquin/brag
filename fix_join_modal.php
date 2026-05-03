<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$content = preg_replace(
    '/<form action="\{\{ route\(\'battles\.action\.declare_win\', \$battle\) \}\}" method="POST" class="d-inline" id="marshallDeclareWinAForm">@csrf <input type="hidden" name="team" value="A"><button type="button" class="btn btn-outline-cyan btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal" wire:click\.prevent="\$set\(\'joiningTeam\', \'A\'\); \$set\(\'pairingSlot\', \{\{ \$i \}\}\)">JOIN<\/button>/s',
    '<button type="button" class="btn btn-outline-cyan btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal" onclick="document.getElementById(\'joiningTeam\').value=\'A\'; document.getElementById(\'pairingSlot\').value={{ $i }}; document.getElementById(\'join_team_name\').innerText=\'A\';">JOIN</button>',
    $content
);

$content = preg_replace(
    '/<button type="button" class="btn btn-outline-magenta btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal" wire:click\.prevent="\$set\(\'joiningTeam\', \'B\'\); \$set\(\'pairingSlot\', \{\{ \$i \}\}\)">JOIN<\/button>/s',
    '<button type="button" class="btn btn-outline-magenta btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal" onclick="document.getElementById(\'joiningTeam\').value=\'B\'; document.getElementById(\'pairingSlot\').value={{ $i }}; document.getElementById(\'join_team_name\').innerText=\'B\';">JOIN</button>',
    $content
);

file_put_contents('resources/views/battles/room.blade.php', $content);
