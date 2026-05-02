<?php
$content = file_get_contents('app/Livewire/BattleRoom.php');
$content = preg_replace('/public \$showEditTeamA = false;\s+public \$showEditTeamB = false;\s+/', '', $content);
$content = preg_replace('/\$this->showEditTeamA = false;\s+/', '', $content);
$content = preg_replace('/\$this->showEditTeamB = false;\s+/', '', $content);
file_put_contents('app/Livewire/BattleRoom.php', $content);
