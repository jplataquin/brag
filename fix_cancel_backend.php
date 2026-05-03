<?php
$content = file_get_contents('app/Http/Controllers/BattleActionController.php');

$oldCancel = <<<'PHP'
            } else {
                $this->logActivity($battle->id, $user->id, 'cancel_request', "{$user->username} requested to cancel the battle.");
                $this->broadcastUpdate($battle, "{$user->username} requested cancellation.");
            }
        });
        
        return back();
    }
PHP;

$newCancel = <<<'PHP'
            } else {
                $this->logActivity($battle->id, $user->id, 'cancel_request', "{$user->username} requested to cancel the battle.");
                $this->broadcastUpdate($battle, "{$user->username} requested cancellation.");
            }
        });
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Cancellation requested. Waiting for opponent...',
                'reload' => true
            ]);
        }

        return back();
    }
PHP;

$content = str_replace($oldCancel, $newCancel, $content);

$oldRespond = <<<'PHP'
                $this->logActivity($battle->id, $user->id, 'cancel_reject', "{$user->username} rejected the cancellation request.");
                $this->broadcastUpdate($battle, "Cancellation request rejected by {$user->username}.");
            }
        });

        return back();
    }
PHP;

$newRespond = <<<'PHP'
                $this->logActivity($battle->id, $user->id, 'cancel_reject', "{$user->username} rejected the cancellation request.");
                $this->broadcastUpdate($battle, "Cancellation request rejected by {$user->username}.");
            }
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $agreed ? 'You agreed to cancel the match.' : 'You rejected the cancellation request.',
                'reload' => true
            ]);
        }

        return back();
    }
PHP;

$content = str_replace($oldRespond, $newRespond, $content);

file_put_contents('app/Http/Controllers/BattleActionController.php', $content);
