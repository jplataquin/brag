<?php
$html = file_get_contents('resources/views/battles/room.blade.php');
$html = str_replace(
    '@if($u)',
    '@if($u && $c)',
    $html
);
file_put_contents('resources/views/battles/room.blade.php', $html);
