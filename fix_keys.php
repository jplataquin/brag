<?php
$content = file_get_contents('resources/views/livewire/battle-room.blade.php');
// Give every action button a wire:key so Livewire doesn't lose track of it
$replacements = [
    'wire:click.prevent="startBattle"' => 'wire:click.prevent="startBattle" wire:key="btn-start"',
    'wire:click.prevent="cancelBattle"' => 'wire:click.prevent="cancelBattle" wire:key="btn-cancel"',
    'wire:click.prevent="teamBReady"' => 'wire:click.prevent="teamBReady" wire:key="btn-ready"',
    'wire:click.prevent="standUp"' => 'wire:click.prevent="standUp" wire:key="btn-standup"',
    'wire:click.prevent="acceptMarshall"' => 'wire:click.prevent="acceptMarshall" wire:key="btn-accept-marshall"',
    'wire:click.prevent="rejectMarshall"' => 'wire:click.prevent="rejectMarshall" wire:key="btn-reject-marshall"',
];

foreach ($replacements as $old => $new) {
    $content = str_replace($old, $new, $content);
}

file_put_contents('resources/views/livewire/battle-room.blade.php', $content);
