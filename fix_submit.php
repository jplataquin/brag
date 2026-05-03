<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

// Disable the confirm button by default
$content = preg_replace(
    '/<button type="submit" class="btn btn-neon w-50 py-2 orbitron">CONFIRM JOIN<\/button>/s',
    '<button type="submit" class="btn btn-neon w-50 py-2 orbitron" id="confirmJoinBtn" disabled>CONFIRM JOIN</button>',
    $content
);

// Enable the button when a card is selected
$content = preg_replace(
    '/document\.getElementById\(\'selectedCardId\'\)\.value=\'\{\{\$card->id\}\}\';/s',
    'document.getElementById(\'selectedCardId\').value=\'{{$card->id}}\'; document.getElementById(\'confirmJoinBtn\').disabled=false;',
    $content
);

file_put_contents('resources/views/battles/room.blade.php', $content);
