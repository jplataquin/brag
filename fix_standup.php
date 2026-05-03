<?php
$content = file_get_contents('app/Http/Controllers/BattleActionController.php');

$oldStandUp = <<<'PHP'
            if ($stoodUp) {
                if (str_contains($slotInfo, 'Team B')) {
                    $battle->team_b_ready = false;
                }
                $battle->save();
                $this->logActivity($battle->id, $user->id, 'leave', "{$user->username} stood up from {$slotInfo}.");
            }
        });

        $this->broadcastUpdate($battle, "{$user->username} left their slot.");
        return back();
    }
PHP;

$newStandUp = <<<'PHP'
            if ($stoodUp) {
                if (str_contains($slotInfo, 'Team B')) {
                    $battle->team_b_ready = false;
                }
                $battle->save();
                $this->logActivity($battle->id, $user->id, 'leave', "{$user->username} stood up from {$slotInfo}.");
            }
        });

        $this->broadcastUpdate($battle, "{$user->username} left their slot.");
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'You stood up successfully. Reloading room...'
            ]);
        }
        
        return back();
    }
PHP;

$content = str_replace($oldStandUp, $newStandUp, $content);
file_put_contents('app/Http/Controllers/BattleActionController.php', $content);
