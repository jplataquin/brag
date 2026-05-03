<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$old = <<<'HTML'
                 x-init="setTimeout(() => checkOverflow(), 200)"
                 @resize.window="checkOverflow()">
HTML;

$new = <<<'HTML'
                 x-init="setTimeout(() => checkOverflow(), 200)"
                 x-on:resize.window="checkOverflow()">
HTML;

$content = str_replace($old, $new, $content);
file_put_contents('resources/views/battles/room.blade.php', $content);
