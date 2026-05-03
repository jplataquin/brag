<?php
$content = file_get_contents('app/Http/Controllers/BattleActionController.php');

$old = <<<'PHP'
        $teamASnapshots = $this->generateTeamSnapshots($battle, 'A', $overrides);
        $teamBSnapshots = $this->generateTeamSnapshots($battle, 'B', $overrides);

        $battle->update([
            'status' => 'completed',
            'winner_team' => $winnerTeam,
            'team_a_card_data' => $teamASnapshots,
            'team_b_card_data' => $teamBSnapshots,
        ]);
PHP;

$new = <<<'PHP'
        // We must update the status to 'completed' FIRST so that the getIntegrityStatAttribute() 
        // accessor on the DigitalCard model includes this current battle in its calculation.
        $battle->update([
            'status' => 'completed',
            'winner_team' => $winnerTeam,
        ]);

        $teamASnapshots = $this->generateTeamSnapshots($battle, 'A', $overrides);
        $teamBSnapshots = $this->generateTeamSnapshots($battle, 'B', $overrides);

        $battle->update([
            'team_a_card_data' => $teamASnapshots,
            'team_b_card_data' => $teamBSnapshots,
        ]);
PHP;

$content = str_replace($old, $new, $content);
file_put_contents('app/Http/Controllers/BattleActionController.php', $content);
