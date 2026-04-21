<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Battle;
use Illuminate\Support\Facades\Auth;

class BattleRoomCards extends Component
{
    public Battle $battle;

    public function mount(Battle $battle)
    {
        $this->battle = $battle;
    }

    public function refreshBattle()
    {
        // Cache the previous structural state
        $oldStatus = $this->battle->status;
        $oldOpponentId = $this->battle->opponent_id;

        $this->battle->refresh();

        // If the status is identical and the opponent hasn't changed, the cards UI 
        // does not need to brutally re-render or trigger canvas reconstructions!
        if ($oldStatus === $this->battle->status && $oldOpponentId === $this->battle->opponent_id) {
            return;
        }

        $this->battle->load([
            'challenger', 
            'opponent', 
            'challengerCard.template.gameTitle', 
            'opponentCard.template.gameTitle'
        ]);

        $this->dispatch('battle-cards-updated');
    }

    public function getListeners()
    {
        return [
            "echo:battle.{$this->battle->room_id},BattleUpdated" => 'refreshBattle',
        ];
    }

    public function render()
    {
        return view('livewire.battle-room-cards');
    }
}
