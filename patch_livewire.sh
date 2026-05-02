sed -i '/$user = Auth::user();/i \        \Log::info("editTeamName triggered by user " . \Auth::id());' app/Livewire/BattleRoom.php
