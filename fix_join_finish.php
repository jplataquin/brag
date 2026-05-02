<?php
$content = file_get_contents('resources/views/livewire/battle-room.blade.php');
// The issue is likely that Livewire is ripping out the modal markup or the buttons aren't wired up to Livewire properly.
// The best way to use Modals with Livewire is alpine JS.
$content = str_replace(
    'data-bs-toggle="modal" data-bs-target="#joinModal" wire:click.prevent="joinTeam(\'A\', {{ $i }})"',
    'wire:click.prevent="joinTeam(\'A\', {{ $i }})" onclick="new bootstrap.Modal(document.getElementById(\'joinModal\')).show()"',
    $content
);
$content = str_replace(
    'data-bs-toggle="modal" data-bs-target="#joinModal" wire:click.prevent="joinTeam(\'B\', {{ $i }})"',
    'wire:click.prevent="joinTeam(\'B\', {{ $i }})" onclick="new bootstrap.Modal(document.getElementById(\'joinModal\')).show()"',
    $content
);
file_put_contents('resources/views/livewire/battle-room.blade.php', $content);
