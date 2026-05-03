<?php
$content = file_get_contents('app/Http/Controllers/BattleController.php');

$method = <<<'PHP'
    public function partialSlot(Battle $battle, $team, $slot)
    {
        $team = strtoupper($team);
        if (!in_array($team, ['A', 'B']) || !is_numeric($slot)) {
            abort(400, 'Invalid parameters');
        }

        $teamLower = strtolower($team);
        $userId = $battle->{"team_{$teamLower}_user_{$slot}"};
        $cardId = $battle->{"team_{$teamLower}_card_{$slot}"};
        
        $u = \App\Models\User::find($userId);
        $c = \App\Models\DigitalCard::find($cardId);

        $isMe = $u && $u->id == \Illuminate\Support\Facades\Auth::id();
        $isFinal = $battle->status == 'completed';
        $snapshot = null; // Partial rendering for in-progress battles primarily

        return view('battles.partials.single-slot', compact('battle', 'team', 'slot', 'u', 'c', 'isMe', 'isFinal', 'snapshot'));
    }
}
PHP;

$content = preg_replace('/\}\s*$/', "\n$method\n", $content);
file_put_contents('app/Http/Controllers/BattleController.php', $content);
