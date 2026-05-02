<?php
$content = file_get_contents('resources/views/livewire/battle-room.blade.php');
$content = preg_replace('/onclick="var m = bootstrap\.Modal\.getInstance\(document\.getElementById\(\'joinModal\'\)\); if\(m\) m\.hide\(\);"/g', 'data-bs-dismiss="modal"', $content);
$content = str_replace(
    'onclick="var m = bootstrap.Modal.getInstance(document.getElementById(\'joinModal\')); if(m) m.hide();"',
    'data-bs-dismiss="modal"',
    $content
);
$content = str_replace(
    'onclick="var m = bootstrap.Modal.getInstance(document.getElementById(\'renameTeamModal\')); if(m) m.hide();"',
    'data-bs-dismiss="modal"',
    $content
);

file_put_contents('resources/views/livewire/battle-room.blade.php', $content);
