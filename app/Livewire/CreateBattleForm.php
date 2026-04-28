<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GameTitle;
use App\Models\DigitalCard;
use App\Models\TeamBattle;
use App\Services\BattleService;
use Illuminate\Support\Facades\Auth;

class CreateBattleForm extends Component
{
    public $step = 1;
    public $battleType = ''; // '1on1' or 'team'
    
    // Form fields
    public $gameTitleId = '';
    public $teamNameA = '';
    public $teamNameB = '';
    public $noPlayersPerTeam = 2;
    public $battleTerms = '';
    public $selectedCardId = '';

    public function setBattleType($type)
    {
        $this->battleType = $type;
        $this->step = 2;
        
        if ($type === 'team') {
            $this->teamNameA = Auth::user()->username . "'s Team";
            $this->teamNameB = "Opponents";
        }
    }

    public function back()
    {
        $this->step = 1;
    }

    public function createBattle()
    {
        if (Auth::user()->currentBattleRoom()) {
            session()->flash('error', 'You are already in an active battle room. You must finish or cancel it before joining or creating another.');
            return;
        }

        if ($this->battleType === '1on1') {
            $this->validate([
                'selectedCardId' => 'required|exists:digital_cards,id',
                'battleTerms' => 'nullable|string|max:1000',
            ]);

            $user = Auth::user();
            $card = DigitalCard::find($this->selectedCardId);
            $service = app(BattleService::class);

            try {
                $battle = $service->createBattle($user, $card, $this->battleTerms);
                return redirect()->route('battles.room', $battle)
                    ->with('success', '⚔️ Battle Room created! Waiting for an opponent.');
            } catch (\Exception $e) {
                session()->flash('error', $e->getMessage());
            }
        } else {
            $this->validate([
                'gameTitleId' => 'required|exists:game_titles,id',
                'teamNameA' => 'required|string|max:255',
                'teamNameB' => 'required|string|max:255',
                'noPlayersPerTeam' => 'required|integer|min:2|max:6',
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
                $teamBattle = TeamBattle::create([
                    'game_title_id' => $this->gameTitleId,
                    'team_name_a' => $this->teamNameA,
                    'team_name_b' => $this->teamNameB,
                    'battle_terms' => $this->battleTerms,
                    'no_players_per_team' => $this->noPlayersPerTeam,
                    'status' => 'pending',
                    'team_a_user_1' => $user->id,
                    'team_a_card_1' => $card->id,
                ]);

                return redirect()->route('team-battles.room', $teamBattle)
                    ->with('success', '⚔️ Team Battle Room created! Waiting for teammates and opponents.');
            } catch (\Exception $e) {
                session()->flash('error', $e->getMessage());
            }
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
