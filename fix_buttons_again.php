<?php
$content = file_get_contents('resources/views/livewire/battle-room.blade.php');
// Give every action button a wire:key and type="button" and wire:click.prevent
$replacements = [
    '<button class="btn btn-neon-lime" wire:click="startBattle"' => '<button type="button" class="btn btn-neon-lime" wire:click.prevent="startBattle" wire:key="btn-start"',
    '<button class="btn btn-neon-danger" wire:click="cancelBattle"' => '<button type="button" class="btn btn-neon-danger" wire:click.prevent="cancelBattle" wire:key="btn-cancel"',
    '<button class="btn btn-neon-lime" wire:click="teamBReady"' => '<button type="button" class="btn btn-neon-lime" wire:click.prevent="teamBReady" wire:key="btn-ready"',
    '<button class="btn btn-outline-warning" wire:click="standUp"' => '<button type="button" class="btn btn-outline-warning" wire:click.prevent="standUp" wire:key="btn-standup"',
    '<button wire:click="acceptMarshall"' => '<button type="button" wire:click.prevent="acceptMarshall" wire:key="btn-accept-marshall"',
    '<button wire:click="rejectMarshall"' => '<button type="button" wire:click.prevent="rejectMarshall" wire:key="btn-reject-marshall"',
    '<button class="btn btn-neon-magenta w-100" wire:click="respondToCancellation(true)"' => '<button type="button" class="btn btn-neon-magenta w-100" wire:click.prevent="respondToCancellation(true)" wire:key="btn-cancel-agree"',
    '<button class="btn btn-outline-secondary w-100" style="border-color: #555;" wire:click="respondToCancellation(false)"' => '<button type="button" class="btn btn-outline-secondary w-100" style="border-color: #555;" wire:click.prevent="respondToCancellation(false)" wire:key="btn-cancel-reject"',
    '<button class="btn btn-neon btn-sm" x-data x-on:click="window.neonConfirm(\'As Marshall, are you sure you want to officially declare TEAM A as the winner?\').then(c => { if(c) $wire.declareWin(\'A\') })">TEAM A WON</button>' => '<button type="button" class="btn btn-neon btn-sm" x-data x-on:click="window.neonConfirm(\'As Marshall, are you sure you want to officially declare TEAM A as the winner?\').then(c => { if(c) $wire.declareWin(\'A\') })" wire:key="btn-marshall-win-a">TEAM A WON</button>',
    '<button class="btn btn-neon-magenta btn-sm" x-data x-on:click="window.neonConfirm(\'As Marshall, are you sure you want to officially declare TEAM B as the winner?\').then(c => { if(c) $wire.declareWin(\'B\') })">TEAM B WON</button>' => '<button type="button" class="btn btn-neon-magenta btn-sm" x-data x-on:click="window.neonConfirm(\'As Marshall, are you sure you want to officially declare TEAM B as the winner?\').then(c => { if(c) $wire.declareWin(\'B\') })" wire:key="btn-marshall-win-b">TEAM B WON</button>',
    '<button class="btn btn-neon-danger btn-sm" x-data x-on:click="window.neonConfirm(\'Are you sure you want to CANCEL this match? No cards will be transferred.\').then(c => { if(c) $wire.cancelBattle() })">CANCEL MATCH</button>' => '<button type="button" class="btn btn-neon-danger btn-sm" x-data x-on:click="window.neonConfirm(\'Are you sure you want to CANCEL this match? No cards will be transferred.\').then(c => { if(c) $wire.cancelBattle() })" wire:key="btn-marshall-cancel">CANCEL MATCH</button>',
];

foreach ($replacements as $old => $new) {
    $content = str_replace($old, $new, $content);
}

// Remove destructive wire:poll entirely
$content = str_replace('<div class="team-battle-room" wire:poll.10s style="overflow: visible;">', '<div class="team-battle-room" style="overflow: visible;">', $content);
$content = str_replace('<div class="team-battle-room" @if(!$joiningTeam) wire:poll.10s @endif style="overflow: visible;">', '<div class="team-battle-room" style="overflow: visible;">', $content);

// Ensure Modals toggle perfectly via html
$content = str_replace('<button type="button" class="btn btn-outline-info btn-sm" wire:click.prevent="editTeamName" wire:key="btn-rename">', '<button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#renameTeamModal">', $content);

file_put_contents('resources/views/livewire/battle-room.blade.php', $content);
