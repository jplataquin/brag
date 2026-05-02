sed -i 's/@if(($isLeaderA && !$showEditTeamA) || ($isLeaderB && !$showEditTeamB))/@if($isLeaderA || $isLeaderB)/g' resources/views/livewire/battle-room.blade.php
