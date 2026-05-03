<?php
$html = file_get_contents('resources/views/livewire/battle-status.blade.php');

$html = str_replace(
    '<button wire:click="$parent.acceptMarshall" class="btn btn-sm btn-neon w-50" style="border-color: #ffdd00; color: #ffdd00;"><i class="bi bi-check-lg"></i> ACCEPT</button>',
    '<form action="{{ route(\'battles.action.accept_marshall\', $battle) }}" method="POST" class="d-inline w-50">@csrf <button type="submit" class="btn btn-sm btn-neon w-100" style="border-color: #ffdd00; color: #ffdd00;"><i class="bi bi-check-lg"></i> ACCEPT</button></form>',
    $html
);

$html = str_replace(
    '<button wire:click="$parent.rejectMarshall" class="btn btn-sm btn-outline-light w-50"><i class="bi bi-x-lg"></i> REJECT</button>',
    '<form action="{{ route(\'battles.action.reject_marshall\', $battle) }}" method="POST" class="d-inline w-50">@csrf <button type="submit" class="btn btn-sm btn-outline-light w-100"><i class="bi bi-x-lg"></i> REJECT</button></form>',
    $html
);

file_put_contents('resources/views/livewire/battle-status.blade.php', $html);
