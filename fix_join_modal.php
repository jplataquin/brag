<?php
$content = file_get_contents('resources/views/livewire/battle-room.blade.php');

$content = str_replace(
    '<button type="button" class="btn btn-outline-cyan btn-sm w-100" style="max-width: 150px;" wire:click.prevent="joinTeam(\'A\', {{ $i }})">JOIN</button>',
    '<button type="button" class="btn btn-outline-cyan btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal" wire:click.prevent="$set(\'joiningTeam\', \'A\'); $set(\'pairingSlot\', {{ $i }})">JOIN</button>',
    $content
);
$content = str_replace(
    '<button type="button" class="btn btn-outline-magenta btn-sm w-100" style="max-width: 150px;" wire:click.prevent="joinTeam(\'B\', {{ $i }})">JOIN</button>',
    '<button type="button" class="btn btn-outline-magenta btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal" wire:click.prevent="$set(\'joiningTeam\', \'B\'); $set(\'pairingSlot\', {{ $i }})">JOIN</button>',
    $content
);

file_put_contents('resources/views/livewire/battle-room.blade.php', $content);
