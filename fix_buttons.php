<?php
$html = file_get_contents('resources/views/battles/room.blade.php');

$html = str_replace(
    '<button class="btn btn-neon-lime" wire:click="startBattle" style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);">
                                            <i class="bi bi-play-fill"></i> START MATCH
                                        </button>',
    '<form action="{{ route(\'battles.action.start\', $battle) }}" method="POST" class="d-inline">@csrf <button type="submit" class="btn btn-neon-lime" style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);"><i class="bi bi-play-fill"></i> START MATCH</button></form>',
    $html
);

$html = str_replace(
    '<button class="btn btn-neon-danger" wire:click="cancelBattle">
                                        <i class="bi bi-x-circle"></i> CANCEL BATTLE
                                    </button>',
    '<form action="{{ route(\'battles.action.cancel\', $battle) }}" method="POST" class="d-inline">@csrf <button type="submit" class="btn btn-neon-danger"><i class="bi bi-x-circle"></i> CANCEL BATTLE</button></form>',
    $html
);

$html = str_replace(
    '<button class="btn btn-outline-danger btn-sm" wire:click="cancelBattle">
                                            <i class="bi bi-x-circle"></i> REQUEST CANCEL
                                        </button>',
    '<form action="{{ route(\'battles.action.cancel\', $battle) }}" method="POST" class="d-inline">@csrf <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle"></i> REQUEST CANCEL</button></form>',
    $html
);

$html = str_replace(
    '<button class="btn btn-neon-lime" wire:click="teamBReady" style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);">
                                            <i class="bi bi-check2-all"></i> READY
                                        </button>',
    '<form action="{{ route(\'battles.action.ready\', $battle) }}" method="POST" class="d-inline">@csrf <button type="submit" class="btn btn-neon-lime" style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);"><i class="bi bi-check2-all"></i> READY</button></form>',
    $html
);

$html = str_replace(
    '<button class="btn btn-outline-warning" wire:click="standUp">
                                    <i class="bi bi-box-arrow-right"></i> STAND UP
                                </button>',
    '<form action="{{ route(\'battles.action.standup\', $battle) }}" method="POST" class="d-inline">@csrf <button type="submit" class="btn btn-outline-warning"><i class="bi bi-box-arrow-right"></i> STAND UP</button></form>',
    $html
);

// Accept Reject Marshall
$html = preg_replace('/<button wire:click="acceptMarshall" class="btn btn-sm btn-neon w-50"([^>]+)><i class="bi bi-check-lg"><\/i> ACCEPT<\/button>/', '<form action="{{ route(\'battles.action.accept_marshall\', $battle) }}" method="POST" class="d-inline w-50">@csrf<button type="submit" class="btn btn-sm btn-neon w-100"$1><i class="bi bi-check-lg"></i> ACCEPT</button></form>', $html);
$html = preg_replace('/<button wire:click="rejectMarshall" class="btn btn-sm btn-outline-light w-50"><i class="bi bi-x-lg"><\/i> REJECT<\/button>/', '<form action="{{ route(\'battles.action.reject_marshall\', $battle) }}" method="POST" class="d-inline w-50">@csrf<button type="submit" class="btn btn-sm btn-outline-light w-100"><i class="bi bi-x-lg"></i> REJECT</button></form>', $html);


file_put_contents('resources/views/battles/room.blade.php', $html);
