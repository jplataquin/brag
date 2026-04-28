<?php

$file = 'app/Livewire/TeamBattleRoom.php';
$content = file_get_contents($file);

$oldLogic = <<<OLD
                // Check if this is the first person joining Team B
                if (\$team === 'B') {
                    \$isFirst = true;
                    for (\$i = 1; \$i <= \$battle->no_players_per_team; \$i++) {
                        if (\$battle->{"team_b_user_{\$i}"} && \$battle->{"team_b_user_{\$i}"} != \$user->id) {
                            \$isFirst = false;
                            break;
                        }
                    }
                    if (\$isFirst) {
                        Log::info("First opponent detected, forcing slot 1");
                        \$slot = 1; // Force into slot 1 to become leader
                    }
                }
OLD;

$newLogic = <<<NEW
                // Removed forced Team B Slot 1 assignment to allow free choice.
NEW;

$content = str_replace($oldLogic, $newLogic, $content);
file_put_contents($file, $content);
echo "Replaced Team B auto-assign logic successfully.\n";
