<?php
$content = file_get_contents('app/Livewire/BattleRoom.php');
$pattern = '/public function editTeamName\(\)\s*\{[^\}]+\}\s*elseif[^\}]+\}\s*\}/s';
$content = preg_replace($pattern, '', $content);
file_put_contents('app/Livewire/BattleRoom.php', $content);
