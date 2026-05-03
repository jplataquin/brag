<?php
$content = file_get_contents('app/Http/Controllers/BattleActionController.php');

// Fix teamBReady
$oldReady = <<<'PHP'
        $battle->update(['team_b_ready' => true]);
        $this->logActivity($battle->id, $user->id, 'ready', "Team B is now READY!");
        $this->broadcastUpdate($battle, "Team B is ready!");
        return back();
    }
PHP;

$newReady = <<<'PHP'
        $battle->update(['team_b_ready' => true]);
        $this->logActivity($battle->id, $user->id, 'ready', "Team B is now READY!");
        $this->broadcastUpdate($battle, "Team B is ready!");
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Team B is now ready!'
            ]);
        }
        
        return back();
    }
PHP;

$content = str_replace($oldReady, $newReady, $content);
file_put_contents('app/Http/Controllers/BattleActionController.php', $content);
