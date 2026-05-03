<?php
$html = file_get_contents('resources/views/battles/room.blade.php');
$html = str_replace(
    'document.getElementById(\'renameTeamVal\').value=\'{{ $isLeaderA ? \\\'A\\\' : \\\'B\\\' }}\';',
    'document.getElementById(\'renameTeamVal\').value=\'{{ $isLeaderA ? "A" : "B" }}\';',
    $html
);
$html = str_replace(
    'document.getElementById(\'rename_team_name\').innerText=\'{{ $isLeaderA ? \\\'A\\\' : \\\'B\\\' }}\';',
    'document.getElementById(\'rename_team_name\').innerText=\'{{ $isLeaderA ? "A" : "B" }}\';',
    $html
);
file_put_contents('resources/views/battles/room.blade.php', $html);
