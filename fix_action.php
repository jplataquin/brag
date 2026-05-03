<?php
$content = file_get_contents('app/Http/Controllers/BattleActionController.php');

$method = <<<PHP
    public function show(Battle \$battle)
    {
        \$user = Auth::user();
        \$myEligibleCards = collect();
        if (\$user) {
            \$myEligibleCards = \$user->digitalCards()
                ->where('life_points', '>', 0)
                ->get()
                ->filter(fn(\$c) => \$c->template->game_title_id == \$battle->game_title_id);
        }

        // Logic for Participant Check
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

$content = preg_replace('/class BattleActionController extends Controller\n\{/', "class BattleActionController extends Controller\n{\n$method\n", $content);
file_put_contents('app/Http/Controllers/BattleActionController.php', $content);
