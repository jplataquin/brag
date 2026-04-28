<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TeamBattle;
use App\Models\DigitalCard;
use App\Models\User;
use App\Models\BattleActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeamBattleRoom extends Component
{
    public TeamBattle $teamBattle;
    public $selectedCardId = null;
    public $joiningTeam = ''; // 'A' or 'B'
    public $pairingSlot = null;
    public $marshallNomineeId = '';
    
    // For editing team names
    public $showEditTeamA = false;
    public $showEditTeamB = false;
    public $newTeamNameA = '';
    public $newTeamNameB = '';

    public $marshallSearchQuery = '';
    public $marshallSearchResults = [];

    public function isParticipant()
    {
        $userId = Auth::id();
        
        if (!$userId) return false;

        for ($i = 1; $i <= $this->teamBattle->no_players_per_team; $i++) {

            if ($this->teamBattle->{"team_a_user_{$i}"} == $userId && $this->teamBattle->{"team_a_card_{$i}"}) {
                return true;
            }

            if ($this->teamBattle->{"team_b_user_{$i}"} == $userId && $this->teamBattle->{"team_b_card_{$i}"}) {
                return true;
            }
        }
        return $this->teamBattle->marshall_id == $userId;
    }

    public function updatedMarshallSearchQuery()
    {
        if (strlen($this->marshallSearchQuery) < 2) {
            $this->marshallSearchResults = [];
            return;
        }

        $this->marshallSearchResults = User::where('username', 'like', '%' . $this->marshallSearchQuery . '%')
            ->take(5)
            ->get();
    }

    public function selectMarshallNominee($userId, $username)
    {
        $this->marshallNomineeId = $userId;
        $this->marshallSearchQuery = '';
        $this->marshallSearchResults = [];
    }

    public function clearMarshallSelection()
    {
        $this->marshallNomineeId = '';
        $this->marshallSearchQuery = '';
    }

    public function mount(TeamBattle $teamBattle)
    {
        $this->teamBattle = $teamBattle;
        $this->newTeamNameA = $teamBattle->team_name_a;
        $this->newTeamNameB = $teamBattle->team_name_b;
    }

    public function getListeners()
    {
        return [
            "echo:team-battle.{$this->teamBattle->id},TeamBattleUpdated" => 'refreshRoom',
        ];
    }

    public function refreshRoom()
    {
        $this->teamBattle->refresh();
        $this->dispatch('battle-updated');
    }

    public function joinTeam($team, $slot = null)
    {
        $this->joiningTeam = $team;
        $this->pairingSlot = $slot;
        $this->selectedCardId = null;
    }

    public function selectCard($cardId)
    {
        $this->selectedCardId = $cardId;
    }

        public function confirmJoin()
    {
        Log::info("Join attempt: User ".Auth::id()." Team ".$this->joiningTeam." Slot ".$this->pairingSlot." Card ".$this->selectedCardId);

        $this->validate([
            'selectedCardId' => 'required|exists:digital_cards,id',
            'joiningTeam' => 'required|in:A,B',
        ]);

        $user = Auth::user();
        $card = DigitalCard::find($this->selectedCardId);

        // Basic validations
        if ($card->template->game_title_id != $this->teamBattle->game_title_id) {
            Log::warning("Join failed: Card game title mismatch");
            $this->addError('selectedCardId', 'Card must match the game title.');
            return;
        }

        if ($card->life_points <= 0) {
            Log::warning("Join failed: Card has no life points");
            $this->addError('selectedCardId', 'Card has no life points.');
            return;
        }

        if ($this->teamBattle->team_a_user_1 == $user->id) {
            Log::warning("Join failed: Creator attempted to change slot");
            $this->addError('selectedCardId', 'The room creator cannot change their slot.');
            return;
        }

        $this->teamBattle->refresh();

        try {
            DB::transaction(function () use ($user, $card) {
                // Lock the record for update to prevent race conditions
                $battle = TeamBattle::where('id', $this->teamBattle->id)->lockForUpdate()->first();

                $team = $this->joiningTeam;
                $slot = $this->pairingSlot;
                $teamLower = strtolower($team);

                if (!$slot) {
                    throw new \Exception("A specific slot must be selected.");
                }

                // Removed forced Team B Slot 1 assignment to allow free choice.

                $userField = "team_{$teamLower}_user_{$slot}";
                $cardField = "team_{$teamLower}_card_{$slot}";

                if ($battle->$userField && $battle->$userField != $user->id) {
                     throw new \Exception("Slot {$team}{$slot} has already been taken by another player.");
                }

                // Remove from existing slot if any (to support transferring slots)
                $wasAlreadyInBattle = false;
                for ($i = 1; $i <= $battle->no_players_per_team; $i++) {
                    if ($battle->{"team_a_user_{$i}"} == $user->id) {
                        $battle->{"team_a_user_{$i}"} = null;
                        $battle->{"team_a_card_{$i}"} = null;
                        $wasAlreadyInBattle = true;
                    }
                    if ($battle->{"team_b_user_{$i}"} == $user->id) {
                        $battle->{"team_b_user_{$i}"} = null;
                        $battle->{"team_b_card_{$i}"} = null;
                        $wasAlreadyInBattle = true;
                    }
                }

                Log::info("Assigning to specific slot {$team}{$slot}");
                $battle->$userField = $user->id;
                $battle->$cardField = $card->id;

                $assignedSlot = $slot;

                $battle->save();

                $actionWord = $wasAlreadyInBattle ? "transferred to" : "joined";
                BattleActivity::create([
                    'team_battle_id' => $battle->id,
                    'user_id' => $user->id,
                    'type' => 'join',
                    'message' => "{$user->username} {$actionWord} Team {$team} (Slot {$assignedSlot}).",
                ]);
            });

            $this->joiningTeam = '';
            $this->pairingSlot = null;
            $this->teamBattle->refresh();
            
            $this->broadcastUpdate("{$user->username} joined or transferred within the battle.");
            
            Log::info("User {$user->id} successfully joined/transferred in Team Battle {$this->teamBattle->id}");
            
            session()->flash('success', 'Joined/Transferred successfully!');
            return redirect()->route('team-battles.room', $this->teamBattle);

        } catch (\Exception $e) {
            Log::error("Failed to join team battle: " . $e->getMessage());
            session()->flash('error', 'Failed to join: ' . $e->getMessage());
            $this->joiningTeam = '';
        }
    }

    public function teamBReady()
    {
        $user = Auth::user();
        if ($user->id != $this->teamBattle->team_b_user_1) {
            session()->flash('error', 'Only Team B leader can declare ready status.');
            return;
        }

        if (!$this->teamBattle->is_team_b_full) {
            session()->flash('error', 'All Team B slots must be filled before getting ready.');
            return;
        }

        $this->teamBattle->update(['team_b_ready' => true]);
        $this->logActivity($user->id, 'ready', "Team B is now READY!");
        $this->broadcastUpdate("Team B is ready!");
    }

    public function startBattle()
    {
        $user = Auth::user();
        if ($user->id != $this->teamBattle->team_a_user_1) {
            session()->flash('error', 'Only Team A leader can start the battle.');
            return;
        }

        if (!$this->teamBattle->team_b_ready) {
            session()->flash('error', 'Team B must be ready before starting.');
            return;
        }

        // Check if all slots are filled
        for ($i = 1; $i <= $this->teamBattle->no_players_per_team; $i++) {
            if (!$this->teamBattle->{"team_a_user_{$i}"} || !$this->teamBattle->{"team_b_user_{$i}"}) {
                session()->flash('error', 'All player slots must be filled before starting.');
                return;
            }
        }

        $this->teamBattle->update(['status' => 'active']);
        $this->logActivity($user->id, 'start', "Battle has officially BEGUN!");
        $this->broadcastUpdate("Battle started!");
    }

    public function declareWin($team)
    {
        $user = Auth::user();
        $isLeaderA = $user->id == $this->teamBattle->team_a_user_1;
        $isLeaderB = $user->id == $this->teamBattle->team_b_user_1;
        $isMarshall = $user->id == $this->teamBattle->marshall_id;

        if (!$isLeaderA && !$isLeaderB && !$isMarshall) {
             session()->flash('error', 'Unauthorized.');
             return;
        }

        DB::transaction(function () use ($team, $user, $isLeaderA, $isLeaderB, $isMarshall) {
            $battle = TeamBattle::where('id', $this->teamBattle->id)->lockForUpdate()->first();
            
            if ($isLeaderA) $battle->team_a_declare_win = $team;
            if ($isLeaderB) $battle->team_b_declare_win = $team;
            if ($isMarshall) $battle->marshall_declare_win = $team;
            
            $battle->save();

            $this->logActivity($user->id, 'declare', "{$user->username} declared Team {$team} as winner.");

            // Check for finalization
            $finalWinnerTeam = null;
            if ($isMarshall) {
                $finalWinnerTeam = $team;
            } elseif ($battle->team_a_declare_win && $battle->team_b_declare_win) {
                if ($battle->team_a_declare_win == $battle->team_b_declare_win) {
                    $finalWinnerTeam = $battle->team_a_declare_win;
                } else {
                    $battle->update(['status' => 'failed']);
                    $this->broadcastUpdate("Conflict in declaration!");
                }
            }

            if ($finalWinnerTeam) {
                $this->finalizeTeamBattle($battle, $finalWinnerTeam);
            } else {
                $this->broadcastUpdate("{$user->username} declared a winner.");
            }
        });
        
        $this->refreshRoom();
    }

    protected function finalizeTeamBattle($battle, $winnerTeam)
    {
        $loserTeam = $winnerTeam == 'A' ? 'B' : 'A';
        
        // Process results for each pair
        for ($i = 1; $i <= $battle->no_players_per_team; $i++) {
            $winnerUserId = $battle->{"team_{$winnerTeam}_user_{$i}"};
            $winnerCardId = $battle->{"team_{$winnerTeam}_card_{$i}"};
            $loserCardId = $battle->{"team_{$loserTeam}_card_{$i}"};
            
            $winnerUser = User::find($winnerUserId);
            $winnerCard = DigitalCard::find($winnerCardId);
            $loserCard = DigitalCard::find($loserCardId);
            
            if ($winnerCard && $loserCard && $winnerUser) {
                $battle->processBattleResult($winnerCard, $loserCard, $winnerUser);
            }
        }

        $battle->update(['status' => 'completed']);
        $this->logActivity(null, 'completed', "Team Battle finalized. Team {$winnerTeam} won!");
        $this->broadcastUpdate("Team Battle finalized!");
    }

    public function cancelBattle()
    {
        $user = Auth::user();
        $isLeaderA = $user->id == $this->teamBattle->team_a_user_1;
        $isLeaderB = $user->id == $this->teamBattle->team_b_user_1;
        $isMarshall = $user->id == $this->teamBattle->marshall_id;

        if (!$isLeaderA && !$isLeaderB && !$isMarshall) return;

        DB::transaction(function () use ($user, $isLeaderA, $isLeaderB, $isMarshall) {
            $battle = TeamBattle::where('id', $this->teamBattle->id)->lockForUpdate()->first();
            
            if ($isLeaderA) $battle->team_a_cancel_flag = true;
            if ($isLeaderB) $battle->team_b_cancel_flag = true;
            if ($isMarshall) $battle->marshall_cancel_flag = true;
            
            $battle->save();

            if ($isMarshall || ($battle->team_a_cancel_flag && $battle->team_b_cancel_flag)) {
                $battle->update(['status' => 'cancelled']);
                $this->broadcastUpdate("Battle cancelled.");
            } else {
                $this->broadcastUpdate("{$user->username} requested cancellation.");
            }
        });
        
        $this->refreshRoom();
    }

    public function electMarshall($nomineeId = null)
    {
        $nomineeId = $nomineeId ?: $this->marshallNomineeId;
        if (!$nomineeId) return;

        $user = Auth::user();
        $isLeaderA = $user->id == $this->teamBattle->team_a_user_1;
        $isLeaderB = $user->id == $this->teamBattle->team_b_user_1;
        
        if (!$isLeaderA && !$isLeaderB) return;

        // Prevent electing someone who is already a player in the match
        for ($i = 1; $i <= $this->teamBattle->no_players_per_team; $i++) {
            if ($this->teamBattle->{"team_a_user_{$i}"} == $nomineeId || $this->teamBattle->{"team_b_user_{$i}"} == $nomineeId) {
                session()->flash('error', 'Cannot elect a player currently in the battle.');
                return;
            }
        }

        if ($isLeaderA) $this->teamBattle->update(['team_a_marshall_elect' => $nomineeId]);
        if ($isLeaderB) $this->teamBattle->update(['team_b_marshall_elect' => $nomineeId]);

        if ($this->teamBattle->team_a_marshall_elect && $this->teamBattle->team_a_marshall_elect == $this->teamBattle->team_b_marshall_elect) {
             // Consensus
             $this->teamBattle->update([
                 'marshall_id' => $this->teamBattle->team_a_marshall_elect,
                 'team_a_marshall_elect' => null,
                 'team_b_marshall_elect' => null
             ]);
             $this->broadcastUpdate("Marshall elected by consensus!");
             $this->logActivity($user->id, 'consensus', "Marshall consensus reached.");
        } else {
            $this->broadcastUpdate("Marshall nominated.");
        }
        
        $this->marshallNomineeId = '';
        $this->refreshRoom();
    }

    public function updateTeamName($team)
    {
        $user = Auth::user();
        if ($team == 'A' && $user->id == $this->teamBattle->team_a_user_1) {
            $this->teamBattle->update(['team_name_a' => $this->newTeamNameA]);
            $this->showEditTeamA = false;
        } elseif ($team == 'B' && $user->id == $this->teamBattle->team_b_user_1) {
            $this->teamBattle->update(['team_name_b' => $this->newTeamNameB]);
            $this->showEditTeamB = false;
        }
        $this->broadcastUpdate("Team name updated.");
        $this->refreshRoom();
    }

    protected function logActivity($userId, $type, $message)
    {
        BattleActivity::create([
            'team_battle_id' => $this->teamBattle->id,
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
        ]);
    }

    protected function broadcastUpdate($message)
    {
        event(new \App\Events\TeamBattleUpdated($this->teamBattle, $message, 'update'));
    }

    public function render()
    {
        $user = Auth::user();
        $cards = collect();
        if ($user) {
            $cards = $user->digitalCards()
                ->where('life_points', '>', 0)
                ->get()
                ->filter(fn($c) => $c->template->game_title_id == $this->teamBattle->game_title_id);
        }

        $activities = BattleActivity::where('team_battle_id', $this->teamBattle->id)->latest()->take(20)->get()->reverse();

        return view('livewire.team-battle-room', [
            'myEligibleCards' => $cards,
            'activities' => $activities,
        ]);
    }
}
