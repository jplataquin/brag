<?php
$content = file_get_contents('resources/views/livewire/battle-room.blade.php');
$content = str_replace(
    'wire:click.prevent="joinTeam(',
    'data-bs-toggle="modal" data-bs-target="#joinModal" wire:click.prevent="joinTeam(',
    $content
);
file_put_contents('resources/views/livewire/battle-room.blade.php', $content);
