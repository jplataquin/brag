<?php
$content = file_get_contents('app/Livewire/BattleRoom.php');
$content = str_replace(
"        if (\$team == 'A' && \$user->id == \$this->battle->team_a_user_1) {
            \Log::info(\"Updating Team A to \" . \$this->newTeamNameA);
            \$this->battle->update(['team_name_a' => \$this->newTeamNameA]);
            } elseif (\$team == 'B' && \$user->id == \$this->battle->team_b_user_1) {
            \$this->battle->update(['team_name_b' => \$this->newTeamNameB]);
            }",
"        if (\$team == 'A' && \$user->id == \$this->battle->team_a_user_1) {
            \Log::info(\"Updating Team A to \" . \$this->newTeamNameA);
            \$this->battle->update(['team_name_a' => \$this->newTeamNameA]);
        } elseif (\$team == 'B' && \$user->id == \$this->battle->team_b_user_1) {
            \$this->battle->update(['team_name_b' => \$this->newTeamNameB]);
        }",
$content);
file_put_contents('app/Livewire/BattleRoom.php', $content);
