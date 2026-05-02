<?php
$content = file_get_contents('resources/views/livewire/battle-room.blade.php');

$pattern = '/@if\(\$joiningTeam\).*?<!-- Rename Team Modal -->/s';
preg_match($pattern, $content, $matches);
if($matches) {
    $modalContent = $matches[0];
    
    // Instead of completely custom DOM injected by Livewire, wrap it in a proper Bootstrap modal structure
    // so that it behaves like Rename/Invite modals. But it relies on $joiningTeam state.
}

