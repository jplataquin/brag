<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$old = "@if(\$battle->status != 'completed' && \$battle->status != 'cancelled')";
$new = "@if(\$battle->status == 'pending')";
$content = str_replace($old, $new, $content);

file_put_contents('resources/views/battles/room.blade.php', $content);
