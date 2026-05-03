<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

// Fix missing form tag for marshallDeclareWinAForm
$content = preg_replace(
    '/<button type="button" class="btn btn-neon btn-sm" onclick="window\.neonConfirm\(\'As Marshall, are you sure you want to officially declare TEAM A as the winner\?\'\)\.then\(c => \{ if\(c\) document\.getElementById\(\'marshallDeclareWinAForm\'\)\.submit\(\); \}\)">TEAM A WON<\/button><\/form>/s',
    '<form action="{{ route(\'battles.action.declare_win\', $battle) }}" method="POST" class="d-inline" id="marshallDeclareWinAForm">@csrf <input type="hidden" name="team" value="A"><button type="button" class="btn btn-neon btn-sm" onclick="window.neonConfirm(\'As Marshall, are you sure you want to officially declare TEAM A as the winner?\').then(c => { if(c) handleActionSubmit(\'marshallDeclareWinAForm\'); })">TEAM A WON</button></form>',
    $content
);

// Update Marshall B Win
$content = preg_replace(
    '/onclick="window\.neonConfirm\(\'As Marshall, are you sure you want to officially declare TEAM B as the winner\?\'\)\.then\(c => \{ if\(c\) document\.getElementById\(\'marshallDeclareWinBForm\'\)\.submit\(\); \}\)"/s',
    'onclick="window.neonConfirm(\'As Marshall, are you sure you want to officially declare TEAM B as the winner?\').then(c => { if(c) handleActionSubmit(\'marshallDeclareWinBForm\'); })"',
    $content
);

// Update Team Leader Win
$content = preg_replace(
    '/onclick="window\.neonConfirm\(\'Are you sure you want to declare WIN\?\'\)\.then\(c => \{ if\(c\) document\.getElementById\(\'declareWin\{\{ \$winTeam \}\}Form\'\)\.submit\(\); \}\)"/s',
    'onclick="window.neonConfirm(\'Are you sure you want to declare WIN?\').then(c => { if(c) handleActionSubmit(\'declareWin{{ $winTeam }}Form\'); })"',
    $content
);

// Update Team Leader Lost
$content = preg_replace(
    '/onclick="window\.neonConfirm\(\'Are you sure you want to declare LOST\?\'\)\.then\(c => \{ if\(c\) document\.getElementById\(\'declareWin\{\{ \$lostTeam \}\}Form\'\)\.submit\(\); \}\)"/s',
    'onclick="window.neonConfirm(\'Are you sure you want to declare LOST?\').then(c => { if(c) handleActionSubmit(\'declareWin{{ $lostTeam }}Form\'); })"',
    $content
);

// Update Ready Button
$oldReady = <<<'HTML'
                                        <form action="{{ route('battles.action.ready', $battle) }}" method="POST" class="d-inline w-100">@csrf <button type="submit" class="btn btn-neon-lime w-100" style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);"><i class="bi bi-check2-all"></i> READY</button></form>
HTML;

$newReady = <<<'HTML'
                                        <form action="{{ route('battles.action.ready', $battle) }}" method="POST" class="d-inline w-100" id="readyForm" onsubmit="event.preventDefault(); handleActionSubmit('readyForm');">@csrf <button type="submit" class="btn btn-neon-lime w-100" style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);"><i class="bi bi-check2-all"></i> READY</button></form>
HTML;

$content = str_replace($oldReady, $newReady, $content);

file_put_contents('resources/views/battles/room.blade.php', $content);
