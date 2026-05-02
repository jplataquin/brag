<?php
$content = file_get_contents('resources/views/livewire/battle-room.blade.php');
$content = str_replace('<button class="btn btn-neon-lime" wire:click="startBattle"', '<button type="button" class="btn btn-neon-lime" wire:click.prevent="startBattle"', $content);
$content = str_replace('<button class="btn btn-neon-danger" wire:click="cancelBattle"', '<button type="button" class="btn btn-neon-danger" wire:click.prevent="cancelBattle"', $content);
$content = str_replace('<button class="btn btn-neon-lime" wire:click="teamBReady"', '<button type="button" class="btn btn-neon-lime" wire:click.prevent="teamBReady"', $content);
$content = str_replace('<button class="btn btn-outline-warning" wire:click="standUp"', '<button type="button" class="btn btn-outline-warning" wire:click.prevent="standUp"', $content);
$content = str_replace('<button wire:click="acceptMarshall"', '<button type="button" wire:click.prevent="acceptMarshall"', $content);
$content = str_replace('<button wire:click="rejectMarshall"', '<button type="button" wire:click.prevent="rejectMarshall"', $content);
$content = str_replace('<button class="btn btn-neon-magenta w-100" wire:click="respondToCancellation(true)"', '<button type="button" class="btn btn-neon-magenta w-100" wire:click.prevent="respondToCancellation(true)"', $content);
$content = str_replace('<button class="btn btn-outline-secondary w-100" style="border-color: #555;" wire:click="respondToCancellation(false)"', '<button type="button" class="btn btn-outline-secondary w-100" style="border-color: #555;" wire:click.prevent="respondToCancellation(false)"', $content);

file_put_contents('resources/views/livewire/battle-room.blade.php', $content);
