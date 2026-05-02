<?php
$content = file_get_contents('app/Livewire/BattleRoom.php');
$content = preg_replace('/public function joinTeam\(\$team, \$slot = null\)\s*\{\s*\$this->joiningTeam = \$team;\s*\$this->pairingSlot = \$slot;\s*\$this->selectedCardId = null;\s*\}/', 'public function joinTeam($team, $slot = null) { \Log::info("Join clicked: team $team slot $slot"); $this->joiningTeam = $team; $this->pairingSlot = $slot; $this->selectedCardId = null; }', $content);
file_put_contents('app/Livewire/BattleRoom.php', $content);
