<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Battle;

class BattleRoomLogs extends Component
{
    public Battle $battle;

    public function mount(Battle $battle)
    {
        $this->battle = $battle;
    }

    public function refreshBattle()
    {
        $this->battle->refresh();
        $this->battle->load(['activities']);
        $this->dispatch('battle-logs-updated');
    }

    public function getListeners()
    {
        return [
            "echo:battle.{$this->battle->room_id},BattleUpdated" => 'refreshBattle',
        ];
    }

    public function render()
    {
        return view('livewire.battle-room-logs');
    }
}
