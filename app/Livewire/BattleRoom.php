<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Battle;
use Illuminate\Support\Facades\Auth;

class BattleRoom extends Component
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
            'activities', 
            'challenger', 
            'opponent', 
            'challengerMarshall', 
            'opponentMarshall', 
            'marshall', 
            'challengerCard.template.gameTitle', 
            'opponentCard.template.gameTitle'
        ]);
        
        // Dispatch browser event to trigger log scroll and js updates
        $this->dispatch('battle-updated');
    }

    public function getListeners()
    {
        return [
            "echo:battle.{$this->battle->room_id},BattleUpdated" => 'refreshBattle',
        ];
    }

    public function render()
    {
        return view('livewire.battle-room');
    }
}
