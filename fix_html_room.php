<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

// Remove $joiningTeam condition
$content = preg_replace('/@if\(\!\$joiningTeam\) @endif/', '', $content);
$content = preg_replace('/@if\(\!\$joiningTeam\)/', '', $content);
$content = preg_replace('/@if\(\$joiningTeam\)/', '', $content);
$content = str_replace('JOIN TEAM {{ $joiningTeam }}', 'JOIN TEAM', $content);

// For action buttons, we must wrap them in <form> instead of just having buttons.
// Also we need to pass CSRF token.
file_put_contents('resources/views/battles/room.blade.php', $content);
