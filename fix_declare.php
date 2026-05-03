<?php
$content = file_get_contents('app/Http/Controllers/BattleActionController.php');

$old = <<<'PHP'
        DB::transaction(function () use ($team, $user, $isLeaderA, $isLeaderB, $isMarshall, $battle) {
            $battle = clone $battle;
            $battle = Battle::where('id', $battle->id)->lockForUpdate()->first();
            
            if ($isLeaderA) $battle->team_a_declare_win = $team;
            if ($isLeaderB) $battle->team_b_declare_win = $team;
            if ($isMarshall) $battle->marshall_declare_win = $team;
            
            $battle->save();

            $this->logActivity($battle->id, $user->id, 'declare', "{$user->username} declared Team {$team} as winner.");

            $finalWinnerTeam = null;
            if ($isMarshall) {
                $finalWinnerTeam = $team;
                $this->logActivity($battle->id, $user->id, 'marshall_decision', "Marshall {$user->username} has made the final decision: Team {$team} wins.");
            } elseif ($battle->team_a_declare_win && $battle->team_b_declare_win) {
                if ($battle->team_a_declare_win == $battle->team_b_declare_win) {
                    $finalWinnerTeam = $battle->team_a_declare_win;
                } else {
                    $battle->update(['status' => 'failed']);
                    $this->broadcastUpdate($battle, "Conflict in declaration!");
                }
            }

            if ($finalWinnerTeam) {
                $this->finalizeBattle($battle, $finalWinnerTeam);
            } else {
                $this->broadcastUpdate($battle, "{$user->username} declared a winner.");
            }
        });
        
        return back();
PHP;

$new = <<<'PHP'
        $consensusReached = false;
        $conflict = false;
        
        DB::transaction(function () use ($team, $user, $isLeaderA, $isLeaderB, $isMarshall, $battle, &$consensusReached, &$conflict) {
            $battle = clone $battle;
            $battle = Battle::where('id', $battle->id)->lockForUpdate()->first();
            
            if ($isLeaderA) $battle->team_a_declare_win = $team;
            if ($isLeaderB) $battle->team_b_declare_win = $team;
            if ($isMarshall) $battle->marshall_declare_win = $team;
            
            $battle->save();

            $this->logActivity($battle->id, $user->id, 'declare', "{$user->username} declared Team {$team} as winner.");

            $finalWinnerTeam = null;
            if ($isMarshall) {
                $finalWinnerTeam = $team;
                $this->logActivity($battle->id, $user->id, 'marshall_decision', "Marshall {$user->username} has made the final decision: Team {$team} wins.");
            } elseif ($battle->team_a_declare_win && $battle->team_b_declare_win) {
                if ($battle->team_a_declare_win == $battle->team_b_declare_win) {
                    $finalWinnerTeam = $battle->team_a_declare_win;
                } else {
                    $conflict = true;
                    $battle->update(['status' => 'failed']);
                    $this->broadcastUpdate($battle, "Conflict in declaration!");
                }
            }

            if ($finalWinnerTeam) {
                $consensusReached = true;
                $this->finalizeBattle($battle, $finalWinnerTeam);
            } else {
                if (!$conflict) {
                    $this->broadcastUpdate($battle, "{$user->username} declared a winner.");
                }
            }
        });
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'consensus' => $consensusReached,
                'conflict' => $conflict,
                'message' => 'Vote recorded successfully.'
            ]);
        }

        return back();
PHP;

$content = str_replace($old, $new, $content);
file_put_contents('app/Http/Controllers/BattleActionController.php', $content);
