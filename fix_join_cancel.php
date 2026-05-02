<?php
$content = file_get_contents('resources/views/livewire/battle-room.blade.php');
$content = str_replace(
    '<button class="btn btn-outline-secondary w-50 py-2" wire:click="$set(\'joiningTeam\', \'\')" wire:loading.attr="disabled">CANCEL</button>',
    '<button type="button" class="btn btn-outline-secondary w-50 py-2" wire:click.prevent="$set(\'joiningTeam\', \'\')" wire:loading.attr="disabled">CANCEL</button>',
    $content
);
$content = str_replace(
    '<button class="btn btn-neon w-50 py-2 orbitron" wire:click="confirmJoin" wire:loading.attr="disabled">',
    '<button type="button" class="btn btn-neon w-50 py-2 orbitron" wire:click.prevent="confirmJoin" wire:loading.attr="disabled">',
    $content
);
file_put_contents('resources/views/livewire/battle-room.blade.php', $content);
