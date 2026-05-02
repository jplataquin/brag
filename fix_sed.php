<?php
$content = file_get_contents('app/Livewire/BattleRoom.php');
$content = preg_replace('/og::info\([^;]+\);\s+\$this->refreshroom\(\);/', '$this->refreshRoom();', $content);
file_put_contents('app/Livewire/BattleRoom.php', $content);
