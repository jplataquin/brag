<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

// Let's replace the inline script string generation with addslashes to prevent single quotes in team names breaking the JS string.
$old = <<<'HTML'
onclick="document.getElementById('renameTeamInput').value='{{ $isLeaderA ? $battle->team_name_a : $battle->team_name_b }}'; document.getElementById('renameTeamVal').value='{{ $isLeaderA ? "A" : "B" }}'; document.getElementById('rename_team_name').innerText='{{ $isLeaderA ? "A" : "B" }}';"
HTML;

$new = <<<'HTML'
onclick="document.getElementById('renameTeamInput').value='{{ addslashes($isLeaderA ? $battle->team_name_a : $battle->team_name_b) }}'; document.getElementById('renameTeamVal').value='{{ $isLeaderA ? "A" : "B" }}'; document.getElementById('rename_team_name').innerText='{{ $isLeaderA ? "A" : "B" }}';"
HTML;

$content = str_replace($old, $new, $content);
file_put_contents('resources/views/battles/room.blade.php', $content);
