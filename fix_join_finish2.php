<?php
$content = file_get_contents('app/Livewire/BattleRoom.php');
$content = str_replace(
    '        Log::info("editTeamName triggered by user " . Auth::id());
',
    '',
    $content
);
file_put_contents('app/Livewire/BattleRoom.php', $content);
