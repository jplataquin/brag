<?php
$content = file_get_contents('app/Http/Controllers/BattleActionController.php');

$old = "return view('battles.room', compact('battle'));";
$new = "return view('battles.room', compact('battle', 'myEligibleCards', 'isParticipant'));";

$content = str_replace($old, $new, $content);
file_put_contents('app/Http/Controllers/BattleActionController.php', $content);
