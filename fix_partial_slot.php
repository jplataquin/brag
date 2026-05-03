<?php
$content = file_get_contents('app/Http/Controllers/BattleController.php');

$old = <<<'PHP'
        $isMe = $u && $u->id == \Illuminate\Support\Facades\Auth::id();
        $isFinal = $battle->status == 'completed';
        $snapshot = null; // Partial rendering for in-progress battles primarily

        return view('battles.partials.single-slot', compact('battle', 'team', 'slot', 'u', 'c', 'isMe', 'isFinal', 'snapshot'));
PHP;

$new = <<<'PHP'
        $isMe = $u && $u->id == \Illuminate\Support\Facades\Auth::id();
        $isFinal = $battle->status == 'completed';
        
        $snapshot = null;
        if ($isFinal) {
            $teamData = $team == 'A' ? $battle->team_a_card_data : $battle->team_b_card_data;
            if (is_array($teamData) && isset($teamData[$slot])) {
                $snapshot = $teamData[$slot];
            }
        }

        return view('battles.partials.single-slot', compact('battle', 'team', 'slot', 'u', 'c', 'isMe', 'isFinal', 'snapshot'));
PHP;

$content = str_replace($old, $new, $content);
file_put_contents('app/Http/Controllers/BattleController.php', $content);
