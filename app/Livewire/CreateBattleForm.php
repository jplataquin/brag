<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GameTitle;
use App\Models\DigitalCard;
use App\Models\Battle;
use Illuminate\Support\Facades\Auth;

class CreateBattleForm extends Component
{
    public $step = 1;
    
    // Form fields
    public $gameTitleId = '';
    public $teamNameA = '';
    public $teamNameB = '';
    public $noPlayersPerTeam = 1;
    public $battleTerms = '';
    public $selectedCardId = '';

    public function mount($gameTitleId = null, $selectedCardId = null)
    {
        $this->teamNameA = Auth::user()->username . "'s Team";
        $this->teamNameB = "Opponents";
        
        if ($gameTitleId) {
            $this->gameTitleId = $gameTitleId;
        }
        
        if ($selectedCardId) {
            $this->selectedCardId = $selectedCardId;
        }
    }

    public function updatedGameTitleId()
    {
        $this->selectedCardId = '';
    }

    public function createBattle()
    {
        if (Auth::user()->currentBattleRoom()) {
            session()->flash('error', 'You are already in an active battle room. You must finish or cancel it before joining or creating another.');
            return;
        }

        $this->validate([
            'gameTitleId' => 'required|exists:game_titles,id',
            'teamNameA' => 'required|string|max:255',
            'teamNameB' => 'required|string|max:255',
            'noPlayersPerTeam' => 'required|integer|min:1|max:6',
            'battleTerms' => 'required|string|max:1000',
            'selectedCardId' => 'required|exists:digital_cards,id',
        ]);

        $user = Auth::user();
        $card = DigitalCard::find($this->selectedCardId);

        // Verify card matches game title
        if ($card->template->game_title_id != $this->gameTitleId) {
             session()->flash('error', 'Selected card must match the selected game title.');
             return;
        }

        try {
            $battle = Battle::create([
                'game_title_id' => $this->gameTitleId,
                'team_name_a' => $this->teamNameA,
                'team_name_b' => $this->teamNameB,
                'battle_terms' => $this->battleTerms,
                'no_players_per_team' => $this->noPlayersPerTeam,
                'status' => 'pending',
                'team_a_user_1' => $user->id,
                'team_a_card_1' => $card->id,
            ]);

            return redirect()->route('battles.room', $battle)
                ->with('success', '⚔️ Battle Room created! Waiting for teammates and opponents.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();
        $cards = collect();
        if ($user) {
            $cards = $user->digitalCards()
                ->where('life_points', '>', 0)
                ->with('template.gameTitle')
                ->get();
        }

        $games = GameTitle::where('status', 'active')->get();

        return view('livewire.create-battle-form', [
            'cards' => $cards,
            'games' => $games,
        ]);
    }
}
