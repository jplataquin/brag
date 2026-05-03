<?php
$content = file_get_contents('app/Http/Controllers/BattleController.php');
$content = str_replace(
    "\$activities = \App\Models\BattleActivity::where('battle_id', \$battle->id)->latest()->take(50)->get();",
    '',
    $content
);
$content = str_replace(
    "'activities'",
    '',
    $content
);
$content = str_replace(
    ", ,",
    ",",
    $content
);
$content = str_replace(
    "compact('battle', 'myEligibleCards', 'isParticipant', )",
    "compact('battle', 'myEligibleCards', 'isParticipant')",
    $content
);

file_put_contents('app/Http/Controllers/BattleController.php', $content);
