<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$content = preg_replace(
    '/document\.addEventListener\(\'livewire:initialized\', \(\) => \{\s*Livewire\.on\(\'show-join-modal\', \(\) => \{\s*const el = document\.getElementById\(\'joinModal\'\);\s*if \(el\) \{\s*const modal = new bootstrap\.Modal\(el\);\s*modal\.show\(\);\s*\}\s*\}\);\s*\}\);/s',
    '',
    $content
);

file_put_contents('resources/views/battles/room.blade.php', $content);
