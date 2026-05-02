<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Battle;
use App\Models\BattleActivity;

class BattleActivityLog extends Component
{
    public Battle $battle;

    public function getListeners()
    {
        return [
            "echo:battle.{$this->battle->id},BattleUpdated" => '$refresh',
        ];
    }

    public function render()
    {
        $activities = BattleActivity::where('battle_id', $this->battle->id)->latest()->take(50)->get();
        return view('livewire.battle-activity-log', ['activities' => $activities]);
    }
}
