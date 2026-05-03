<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

// Fix $this->isParticipant() to $isParticipant
$content = str_replace('$this->isParticipant()', '$isParticipant', $content);

// Remove livewire:battle-status and livewire:battle-activity-log
// Assuming they should be moved or just converted. For now, let's keep them if they are livewire components on their own, 
// wait, the prompt says "transfer the livewire logic in the BattleController class" - maybe they meant the whole BattleRoom component.
// Let's check if the child components are still Livewire.
