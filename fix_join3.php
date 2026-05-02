<?php
$content = file_get_contents('resources/views/livewire/battle-room.blade.php');
$content = str_replace(
    '<div class="custom-modal-backdrop">',
    '<div class="custom-modal-backdrop" style="z-index: 2000;" wire:key="join-modal">',
    $content
);
file_put_contents('resources/views/livewire/battle-room.blade.php', $content);
