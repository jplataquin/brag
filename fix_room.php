<?php
$content = file_get_contents('app/Http/Controllers/BattleController.php');

$old = <<<'OLD'
        return view('battles.room', compact('battle'));
    }
OLD;

$new = <<<'NEW'
        $activities = \App\Models\BattleActivity::where('battle_id', $battle->id)->latest()->take(50)->get();
        return view('battles.room', compact('battle', 'myEligibleCards', 'isParticipant', 'activities'));
    }
NEW;

$content = str_replace($old, $new, $content);
file_put_contents('app/Http/Controllers/BattleController.php', $content);
