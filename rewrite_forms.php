<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

// Replace wire:click="cancelBattle"
$content = preg_replace(
    '/<button class="btn btn-outline-danger btn-sm" wire:click="cancelBattle">/s',
    '<form action="{{ route(\'battles.action.cancel\', $battle) }}" method="POST" class="d-inline">@csrf <button type="submit" class="btn btn-outline-danger btn-sm">',
    $content
);
// Fix the closing button tag for the form
$content = preg_replace(
    '/(<form action="\{\{ route\(\'battles\.action\.cancel\', \$battle\) \}\}" method="POST" class="d-inline">@csrf <button type="submit" class="btn btn-outline-danger btn-sm">.*?<\/button>)/s',
    '$1</form>',
    $content
);


// Replace $wire.declareWin('A') and $wire.declareWin('B') with a dynamic form
$content = preg_replace_callback(
    '/<button class="btn (.*?) btn-sm" x-data x-on:click="window\.neonConfirm\(\'(.*?)\'\)\.then\(c => \{ if\(c\) \$wire\.declareWin\(\'(.*?)\'\) \}\)" wire:loading\.attr="disabled" style="(.*?)">/s',
    function($matches) {
        $btnClass = $matches[1];
        $msg = $matches[2];
        $team = $matches[3];
        $style = $matches[4];
        
        return '<form action="{{ route(\'battles.action.declare_win\', $battle) }}" method="POST" class="d-inline" id="declareWin'.$team.'Form">@csrf <input type="hidden" name="team" value="'.$team.'"><button type="button" class="btn '.$btnClass.' btn-sm" onclick="window.neonConfirm(\''.$msg.'\').then(c => { if(c) document.getElementById(\'declareWin'.$team.'Form\').submit(); })" style="'.$style.'">';
    },
    $content
);

// Add closing form tag for those buttons
$content = preg_replace(
    '/(<form action="\{\{ route\(\'battles\.action\.declare_win\', \$battle\) \}\}" method="POST" class="d-inline" id="declareWin.*?Form">@csrf <input type="hidden" name="team" value=".*?"><button type="button" class="btn .*? btn-sm" onclick=".*?" style=".*?">.*?<\/button>)/s',
    '$1</form>',
    $content
);

// Replace marshall's cancel button
$content = preg_replace(
    '/<button type="button" class="btn btn-neon-danger btn-sm" x-data x-on:click="window\.neonConfirm\(\'(.*?)\'\)\.then\(c => \{ if\(c\) \$wire\.cancelBattle\(\) \}\)">CANCEL MATCH<\/button>/s',
    '<form action="{{ route(\'battles.action.cancel\', $battle) }}" method="POST" class="d-inline" id="marshallCancelForm">@csrf <button type="button" class="btn btn-neon-danger btn-sm" onclick="window.neonConfirm(\'$1\').then(c => { if(c) document.getElementById(\'marshallCancelForm\').submit(); })">CANCEL MATCH</button></form>',
    $content
);


// Replace Marshall's declare win buttons
$content = preg_replace_callback(
    '/<button type="button" class="btn (.*?) btn-sm" x-data x-on:click="window\.neonConfirm\(\'(.*?)\'\)\.then\(c => \{ if\(c\) \$wire\.declareWin\(\'(.*?)\'\) \}\)">(.*?)<\/button>/s',
    function($matches) {
        $btnClass = $matches[1];
        $msg = $matches[2];
        $team = $matches[3];
        $label = $matches[4];
        return '<form action="{{ route(\'battles.action.declare_win\', $battle) }}" method="POST" class="d-inline" id="marshallDeclareWin'.$team.'Form">@csrf <input type="hidden" name="team" value="'.$team.'"><button type="button" class="btn '.$btnClass.' btn-sm" onclick="window.neonConfirm(\''.$msg.'\').then(c => { if(c) document.getElementById(\'marshallDeclareWin'.$team.'Form\').submit(); })">'.$label.'</button></form>';
    },
    $content
);

file_put_contents('resources/views/battles/room.blade.php', $content);
