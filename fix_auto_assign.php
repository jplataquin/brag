<?php

$file = 'app/Livewire/TeamBattleRoom.php';
$content = file_get_contents($file);

$startPos = strpos($content, 'public function confirmJoin()');
$endPos = strpos($content, 'public function startBattle()');

if ($startPos !== false && $endPos !== false) {
    $oldFunc = substr($content, $startPos, $endPos - $startPos);

$newFunc = <<<NEW
    public function confirmJoin()
    {
        Log::info("Join attempt: User ".Auth::id()." Team ".\$this->joiningTeam." Slot ".\$this->pairingSlot." Card ".\$this->selectedCardId);

        \$this->validate([
            'selectedCardId' => 'required|exists:digital_cards,id',
            'joiningTeam' => 'required|in:A,B',
        ]);

        \$user = Auth::user();
        \$card = DigitalCard::find(\$this->selectedCardId);

        // Basic validations
        if (\$card->template->game_title_id != \$this->teamBattle->game_title_id) {
            Log::warning("Join failed: Card game title mismatch");
            \$this->addError('selectedCardId', 'Card must match the game title.');
            return;
        }

        if (\$card->life_points <= 0) {
            Log::warning("Join failed: Card has no life points");
            \$this->addError('selectedCardId', 'Card has no life points.');
            return;
        }

        if (\$this->teamBattle->team_a_user_1 == \$user->id) {
            Log::warning("Join failed: Creator attempted to change slot");
            \$this->addError('selectedCardId', 'The room creator cannot change their slot.');
            return;
        }

        \$this->teamBattle->refresh();

        try {
            DB::transaction(function () use (\$user, \$card) {
                // Lock the record for update to prevent race conditions
                \$battle = TeamBattle::where('id', \$this->teamBattle->id)->lockForUpdate()->first();

                \$team = \$this->joiningTeam;
                \$slot = \$this->pairingSlot;
                \$teamLower = strtolower(\$team);

                if (!\$slot) {
                    throw new \Exception("A specific slot must be selected.");
                }

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

                \$userField = "team_{\$teamLower}_user_{\$slot}";
                \$cardField = "team_{\$teamLower}_card_{\$slot}";

                if (\$battle->\$userField && \$battle->\$userField != \$user->id) {
                     throw new \Exception("Slot {\$team}{\$slot} has already been taken by another player.");
                }

                // Remove from existing slot if any (to support transferring slots)
                \$wasAlreadyInBattle = false;
                for (\$i = 1; \$i <= \$battle->no_players_per_team; \$i++) {
                    if (\$battle->{"team_a_user_{\$i}"} == \$user->id) {
                        \$battle->{"team_a_user_{\$i}"} = null;
                        \$battle->{"team_a_card_{\$i}"} = null;
                        \$wasAlreadyInBattle = true;
                    }
                    if (\$battle->{"team_b_user_{\$i}"} == \$user->id) {
                        \$battle->{"team_b_user_{\$i}"} = null;
                        \$battle->{"team_b_card_{\$i}"} = null;
                        \$wasAlreadyInBattle = true;
                    }
                }

                Log::info("Assigning to specific slot {\$team}{\$slot}");
                \$battle->\$userField = \$user->id;
                \$battle->\$cardField = \$card->id;

                \$assignedSlot = \$slot;

                \$battle->save();

                \$actionWord = \$wasAlreadyInBattle ? "transferred to" : "joined";
                BattleActivity::create([
                    'team_battle_id' => \$battle->id,
                    'user_id' => \$user->id,
                    'type' => 'join',
                    'message' => "{\$user->username} {\$actionWord} Team {\$team} (Slot {\$assignedSlot}).",
                ]);
            });

            \$this->joiningTeam = '';
            \$this->pairingSlot = null;
            \$this->teamBattle->refresh();
            
            \$this->broadcastUpdate("{\$user->username} joined or transferred within the battle.");
            
            Log::info("User {\$user->id} successfully joined/transferred in Team Battle {\$this->teamBattle->id}");
            
            session()->flash('success', 'Joined/Transferred successfully!');
            return redirect()->route('team-battles.room', \$this->teamBattle);

        } catch (\Exception \$e) {
            Log::error("Failed to join team battle: " . \$e->getMessage());
            session()->flash('error', 'Failed to join: ' . \$e->getMessage());
            \$this->joiningTeam = '';
        }
    }

    
NEW;
    
    $content = str_replace($oldFunc, $newFunc, $content);
    file_put_contents($file, $content);
    echo "Replaced confirmJoin successfully.\n";
} else {
    echo "Failed to find confirmJoin bounds.\n";
}
