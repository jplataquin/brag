sed -i '/$this->battle->update(\['\''team_name_a'\'' => $this->newTeamNameA\]);/i \            \\Log::info("Updating Team A to " . $this->newTeamNameA);' app/Livewire/BattleRoom.php
