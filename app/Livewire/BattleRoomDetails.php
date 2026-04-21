<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Battle;
use Illuminate\Support\Facades\Auth;

class BattleRoomDetails extends Component
{
    public Battle $battle;

    public function mount(Battle $battle)
    {
        $this->battle = $battle;
    }

    public function refreshBattle()
    {
        $this->battle->refresh();
        $this->battle->load([
            'challenger', 
            'opponent', 
            'challengerMarshall', 
            'opponentMarshall', 
            'marshall', 
            'challengerCard.template.gameTitle'
        ]);
    }

    public function getListeners()
    {
        return [
            "echo:battle.{$this->battle->room_id},BattleUpdated" => 'refreshBattle',
        ];
    }

    public function render()
    {
        return view('livewire.battle-room-details');
    }
}
