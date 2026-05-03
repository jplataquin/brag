<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$old = "const els = document.querySelectorAll('[x-ref=\"' + teamId + '\"]');";
$new = "const els = document.querySelectorAll('[x-ref=\"' + teamId + '\"]');"; // Wait, string concat is valid JS.
