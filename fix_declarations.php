<?php
$content = file_get_contents('resources/views/livewire/battle-room.blade.php');
// Ensure declare win buttons use wire:click and standard buttons
$content = str_replace(
    '<button class="btn btn-neon btn-sm" x-data x-on:click="window.neonConfirm(\'As Marshall, are you sure you want to officially declare TEAM A as the winner?\').then(c => { if(c) $wire.declareWin(\'A\') })">TEAM A WON</button>',
    '<button type="button" class="btn btn-neon btn-sm" x-data x-on:click="window.neonConfirm(\'As Marshall, are you sure you want to officially declare TEAM A as the winner?\').then(c => { if(c) $wire.declareWin(\'A\') })" wire:key="btn-marshall-win-a">TEAM A WON</button>',
    $content
);
$content = str_replace(
    '<button class="btn btn-neon-magenta btn-sm" x-data x-on:click="window.neonConfirm(\'As Marshall, are you sure you want to officially declare TEAM B as the winner?\').then(c => { if(c) $wire.declareWin(\'B\') })">TEAM B WON</button>',
    '<button type="button" class="btn btn-neon-magenta btn-sm" x-data x-on:click="window.neonConfirm(\'As Marshall, are you sure you want to officially declare TEAM B as the winner?\').then(c => { if(c) $wire.declareWin(\'B\') })" wire:key="btn-marshall-win-b">TEAM B WON</button>',
    $content
);
$content = str_replace(
    '<button class="btn btn-neon-danger btn-sm" x-data x-on:click="window.neonConfirm(\'Are you sure you want to CANCEL this match? No cards will be transferred.\').then(c => { if(c) $wire.cancelBattle() })">CANCEL MATCH</button>',
    '<button type="button" class="btn btn-neon-danger btn-sm" x-data x-on:click="window.neonConfirm(\'Are you sure you want to CANCEL this match? No cards will be transferred.\').then(c => { if(c) $wire.cancelBattle() })" wire:key="btn-marshall-cancel">CANCEL MATCH</button>',
    $content
);
file_put_contents('resources/views/livewire/battle-room.blade.php', $content);
