<?php
$content = file_get_contents('app/Http/Controllers/BattleController.php');

$oldJoin = <<<'OLD'
    public function join(Battle $battle)
    {
        $user = Auth::user();
        
        $isParticipant = false;
        for ($i = 1; $i <= $battle->no_players_per_team; $i++) {
            if ($battle->{"team_a_user_{$i}"} == $user->id || $battle->{"team_b_user_{$i}"} == $user->id) {
                $isParticipant = true;
                break;
            }
        }
        if ($isParticipant) {
            return redirect()->route('battles.room', $battle);
        }

        
        return view('battles.room', compact('battle', 'myEligibleCards', 'isParticipant'));
    }
OLD;

$newJoin = <<<'NEW'
    public function join(Battle $battle)
    {
        $user = Auth::user();
        
        $isParticipant = false;
        for ($i = 1; $i <= $battle->no_players_per_team; $i++) {
            if ($battle->{"team_a_user_{$i}"} == $user->id || $battle->{"team_b_user_{$i}"} == $user->id) {
                $isParticipant = true;
                break;
            }
        }
        if ($isParticipant) {
            return redirect()->route('battles.room', $battle);
        }

        $myEligibleCards = collect();
        if ($user) {
            $myEligibleCards = $user->digitalCards()
                ->where('life_points', '>', 0)
                ->get()
                ->filter(fn($c) => $c->template->game_title_id == $battle->game_title_id);
        }

        $isParticipant = false; // By definition, if they are here, they are not a participant in the UI context either

        return view('battles.room', compact('battle', 'myEligibleCards', 'isParticipant'));
    }
NEW;

$content = str_replace($oldJoin, $newJoin, $content);
file_put_contents('app/Http/Controllers/BattleController.php', $content);
