<?php
$content = file_get_contents('app/Http/Controllers/BattleController.php');

$roomMethod = <<<PHP
    public function room(Battle \$battle)
    {
        \$user = Auth::user();
        
        if (\$battle->status === 'pending') {
            \$isParticipant = false;
            for (\$i = 1; \$i <= \$battle->no_players_per_team; \$i++) {
                if (\$battle->{"team_a_user_{\$i}"} == \$user->id || \$battle->{"team_b_user_{\$i}"} == \$user->id) {
                    \$isParticipant = true;
                    break;
                }
            }
            if (!\$isParticipant) {
                return redirect()->route('battles.join', \$battle);
            }
        }

        \$myEligibleCards = collect();
        if (\$user) {
            \$myEligibleCards = \$user->digitalCards()
                ->where('life_points', '>', 0)
                ->get()
                ->filter(fn(\$c) => \$c->template->game_title_id == \$battle->game_title_id);
        }

        // Logic for Participant Check (re-evaluating strictly for UI permissions)
        \$isParticipant = false;
        if (\$user) {
            for (\$i = 1; \$i <= \$battle->no_players_per_team; \$i++) {
                if (\$battle->{"team_a_user_{\$i}"} == \$user->id && \$battle->{"team_a_card_{\$i}"}) \$isParticipant = true;
                if (\$battle->{"team_b_user_{\$i}"} == \$user->id && \$battle->{"team_b_card_{\$i}"}) \$isParticipant = true;
            }
            if (\$battle->marshall_id == \$user->id) \$isParticipant = true;
        }

        return view('battles.room', compact('battle', 'myEligibleCards', 'isParticipant'));
    }
PHP;

$content = preg_replace('/public function room\(Battle \$battle\)\s*\{.*?return view\(\'battles\.room\', compact\(\'battle\'\)\);\s*\}/s', $roomMethod, $content);
file_put_contents('app/Http/Controllers/BattleController.php', $content);
