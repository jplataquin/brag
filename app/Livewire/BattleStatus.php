<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Battle;

class BattleStatus extends Component
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
        return view('livewire.battle-status');
    }
}
