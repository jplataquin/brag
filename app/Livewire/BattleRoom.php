<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Battle;
use App\Models\DigitalCard;
use App\Models\User;
use App\Models\BattleActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BattleRoom extends Component
{
    public Battle $battle;
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

    public $inviteSearchQuery = '';
    public $inviteSearchResults = [];
    public $inviteNomineeId = '';

    public function updatedInviteSearchQuery()
    {
        if (strlen($this->inviteSearchQuery) < 2) {
            $this->inviteSearchResults = [];
            return;
        }

        $except = [];
        for ($i = 1; $i <= $this->battle->no_players_per_team; $i++) {
            if ($this->battle->{"team_a_user_{$i}"}) $except[] = $this->battle->{"team_a_user_{$i}"};
            if ($this->battle->{"team_b_user_{$i}"}) $except[] = $this->battle->{"team_b_user_{$i}"};
        }
        if ($this->battle->marshall_id) $except[] = $this->battle->marshall_id;

        $this->inviteSearchResults = User::where('username', 'like', '%' . $this->inviteSearchQuery . '%')
            ->whereNotIn('id', $except)
            ->take(5)
            ->get()
            ->toArray();
    }

    public function selectInviteNominee($userId, $username)
    {
        $this->inviteNomineeId = $userId;
        $this->inviteSearchQuery = '';
        $this->inviteSearchResults = [];
    }

    public function clearInviteSelection()
    {
        $this->inviteNomineeId = '';
        $this->inviteSearchQuery = '';
    }

    public function sendInvite()
    {
        if (!$this->inviteNomineeId) return;

        $user = Auth::user();
        $isLeaderA = $user->id == $this->battle->team_a_user_1;
        $isLeaderB = $user->id == $this->battle->team_b_user_1;

        if (!$isLeaderA && !$isLeaderB) return;

        $invitedUser = User::find($this->inviteNomineeId);
        if ($invitedUser) {
            $teamName = $isLeaderA ? $this->battle->team_name_a : $this->battle->team_name_b;
            $invitedUser->notify(new \App\Notifications\BattleNotification(
                $this->battle,
                "{$user->username} has invited you to join {$teamName} in Battle #{$this->battle->id}!",
                'invite',
                route('battles.join', $this->battle)
            ));
            
            $this->logActivity($user->id, 'invite', "{$user->username} invited @{$invitedUser->username} to join the battle.");
            session()->flash('success', "Invite sent to @{$invitedUser->username}");
        }

        $this->clearInviteSelection();
    }

    public function isParticipant()
    {
        $userId = Auth::id();
        
        if (!$userId) return false;

        for ($i = 1; $i <= $this->battle->no_players_per_team; $i++) {

            if ($this->battle->{"team_a_user_{$i}"} == $userId && $this->battle->{"team_a_card_{$i}"}) {
                return true;
            }

            if ($this->battle->{"team_b_user_{$i}"} == $userId && $this->battle->{"team_b_card_{$i}"}) {
                return true;
            }
        }
        return $this->battle->marshall_id == $userId;
    }

    public function updatedMarshallSearchQuery()
    {
        if (strlen($this->marshallSearchQuery) < 2) {
            $this->marshallSearchResults = [];
            return;
        }

        $except = [];
        for ($i = 1; $i <= $this->battle->no_players_per_team; $i++) {
            if ($this->battle->{"team_a_user_{$i}"}) $except[] = $this->battle->{"team_a_user_{$i}"};
            if ($this->battle->{"team_b_user_{$i}"}) $except[] = $this->battle->{"team_b_user_{$i}"};
        }
        if ($this->battle->marshall_id) $except[] = $this->battle->marshall_id;

        $this->marshallSearchResults = User::where('username', 'like', '%' . $this->marshallSearchQuery . '%')
            ->whereNotIn('id', $except)
            ->take(5)
            ->get()
            ->toArray();
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

    public function mount(Battle $battle)
    {
        $this->battle = $battle;
        $this->newTeamNameA = $battle->team_name_a;
        $this->newTeamNameB = $battle->team_name_b;
    }

    public function getListeners()
    {
        return [
            "echo:battle.{$this->battle->id},BattleUpdated" => 'refreshRoom',
        ];
    }

    public function refreshRoom()
    {
        $this->battle->refresh();
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

        $user = Auth::user();
        $currentRoom = $user->currentBattleRoom();
        if ($currentRoom && !($currentRoom['type'] === 'team' && $currentRoom['battle']->id === $this->battle->id)) {
            session()->flash('error', 'You are already in another active battle room. You must finish or cancel it before joining this one.');
            $this->joiningTeam = '';
            return;
        }

        $this->validate([
            'selectedCardId' => 'required|exists:digital_cards,id',
            'joiningTeam' => 'required|in:A,B',
        ]);

        $card = DigitalCard::find($this->selectedCardId);

        // Basic validations
        if ($card->template->game_title_id != $this->battle->game_title_id) {
            Log::warning("Join failed: Card game title mismatch");
            $this->addError('selectedCardId', 'Card must match the game title.');
            return;
        }

        if ($card->life_points <= 0) {
            Log::warning("Join failed: Card has no life points");
            $this->addError('selectedCardId', 'Card has no life points.');
            return;
        }

        if ($this->battle->team_a_user_1 == $user->id) {
            Log::warning("Join failed: Creator attempted to change slot");
            $this->addError('selectedCardId', 'The room creator cannot change their slot.');
            return;
        }

        $this->battle->refresh();

        try {
            DB::transaction(function () use ($user, $card) {
                // Lock the record for update to prevent race conditions
                $battle = Battle::where('id', $this->battle->id)->lockForUpdate()->first();

                $team = $this->joiningTeam;
                $slot = $this->pairingSlot;
                $teamLower = strtolower($team);

                if (!$slot) {
                    throw new \Exception("A specific slot must be selected.");
                }

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
                    'battle_id' => $battle->id,
                    'user_id' => $user->id,
                    'type' => 'join',
                    'message' => "{$user->username} {$actionWord} Team {$team} (Slot {$assignedSlot}).",
                ]);
            });

            $this->joiningTeam = '';
            $this->pairingSlot = null;
            $this->battle->refresh();
            
            $this->broadcastUpdate("{$user->username} joined or transferred within the battle.");
            
            Log::info("User {$user->id} successfully joined/transferred in Battle {$this->battle->id}");
            
            session()->flash('success', 'Joined/Transferred successfully!');
            return redirect()->route('battles.room', $this->battle);

        } catch (\Exception $e) {
            Log::error("Failed to join battle: " . $e->getMessage());
            session()->flash('error', 'Failed to join: ' . $e->getMessage());
            $this->joiningTeam = '';
        }
    }

    public function standUp()
    {
        $user = Auth::user();
        
        if ($this->battle->status !== 'pending') {
            session()->flash('error', 'You can only stand up while the battle is pending.');
            return;
        }

        if ($this->battle->team_a_user_1 == $user->id) {
            session()->flash('error', 'The creator of the battle room cannot stand up.');
            return;
        }

        DB::transaction(function () use ($user) {
            $battle = Battle::where('id', $this->battle->id)->lockForUpdate()->first();
            
            $stoodUp = false;
            $slotInfo = '';

            for ($i = 1; $i <= $battle->no_players_per_team; $i++) {
                if ($battle->{"team_a_user_{$i}"} == $user->id) {
                    $battle->{"team_a_user_{$i}"} = null;
                    $battle->{"team_a_card_{$i}"} = null;
                    $stoodUp = true;
                    $slotInfo = "Team A Slot {$i}";
                    break;
                }
                if ($battle->{"team_b_user_{$i}"} == $user->id) {
                    $battle->{"team_b_user_{$i}"} = null;
                    $battle->{"team_b_card_{$i}"} = null;
                    $stoodUp = true;
                    $slotInfo = "Team B Slot {$i}";
                    break;
                }
            }

            if ($stoodUp) {
                if (str_contains($slotInfo, 'Team B')) {
                    $battle->team_b_ready = false;
                }
                $battle->save();
                
                $this->logActivity($user->id, 'leave', "{$user->username} stood up from {$slotInfo}.");
            }
        });

        $this->broadcastUpdate("{$user->username} left their slot.");
        $this->refreshRoom();
    }

    public function teamBReady()
    {
        $user = Auth::user();
        if ($user->id != $this->battle->team_b_user_1) {
            session()->flash('error', 'Only Team B leader can declare ready status.');
            return;
        }

        if (!$this->battle->is_team_b_full) {
            session()->flash('error', 'All Team B slots must be filled before getting ready.');
            return;
        }

        $this->battle->update(['team_b_ready' => true]);
        $this->logActivity($user->id, 'ready', "Team B is now READY!");
        $this->broadcastUpdate("Team B is ready!");
    }

    public function startBattle()
    {
        $user = Auth::user();
        if ($user->id != $this->battle->team_a_user_1) {
            session()->flash('error', 'Only Team A leader can start the battle.');
            return;
        }

        if (!$this->battle->team_b_ready) {
            session()->flash('error', 'Team B must be ready before starting.');
            return;
        }

        // Check if all slots are filled
        for ($i = 1; $i <= $this->battle->no_players_per_team; $i++) {
            if (!$this->battle->{"team_a_user_{$i}"} || !$this->battle->{"team_b_user_{$i}"}) {
                session()->flash('error', 'All player slots must be filled before starting.');
                return;
            }
        }

        $this->battle->update(['status' => 'active']);
        $this->logActivity($user->id, 'start', "Battle has officially BEGUN!");
        $this->broadcastUpdate("Battle started!");
    }

    public function declareWin($team)
    {
        $user = Auth::user();
        $isLeaderA = $user->id == $this->battle->team_a_user_1;
        $isLeaderB = $user->id == $this->battle->team_b_user_1;
        $isMarshall = $user->id == $this->battle->marshall_id;

        if (!$isLeaderA && !$isLeaderB && !$isMarshall) {
             session()->flash('error', 'Unauthorized.');
             return;
        }

        DB::transaction(function () use ($team, $user, $isLeaderA, $isLeaderB, $isMarshall) {
            $battle = Battle::where('id', $this->battle->id)->lockForUpdate()->first();
            
            if ($isLeaderA) $battle->team_a_declare_win = $team;
            if ($isLeaderB) $battle->team_b_declare_win = $team;
            if ($isMarshall) $battle->marshall_declare_win = $team;
            
            $battle->save();

            $this->logActivity($user->id, 'declare', "{$user->username} declared Team {$team} as winner.");

            // Check for finalization
            $finalWinnerTeam = null;
            if ($isMarshall) {
                $finalWinnerTeam = $team;
                $this->logActivity($user->id, 'marshall_decision', "Marshall {$user->username} has made the final decision: Team {$team} wins.");
            } elseif ($battle->team_a_declare_win && $battle->team_b_declare_win) {
                if ($battle->team_a_declare_win == $battle->team_b_declare_win) {
                    $finalWinnerTeam = $battle->team_a_declare_win;
                } else {
                    $battle->update(['status' => 'failed']);
                    $this->broadcastUpdate("Conflict in declaration!");
                }
            }

            if ($finalWinnerTeam) {
                $this->finalizeBattle($battle, $finalWinnerTeam);
            } else {
                $this->broadcastUpdate("{$user->username} declared a winner.");
            }
        });
        
        $this->battle->refresh();
    }

    protected function finalizeBattle($battle, $winnerTeam)
    {
        $loserTeam = $winnerTeam == 'A' ? 'B' : 'A';
        $winnerTeamLower = strtolower($winnerTeam);
        $loserTeamLower = strtolower($loserTeam);

        $overrides = [];

        // Process results for each pair first
        for ($i = 1; $i <= $battle->no_players_per_team; $i++) {
            $winnerUserId = $battle->{"team_{$winnerTeamLower}_user_{$i}"};
            $winnerCardId = $battle->{"team_{$winnerTeamLower}_card_{$i}"};
            $loserCardId = $battle->{"team_{$loserTeamLower}_card_{$i}"};
            
            $winnerUser = User::find($winnerUserId);
            $winnerCard = DigitalCard::find($winnerCardId);
            $loserCard = DigitalCard::find($loserCardId);
            
            if ($winnerCard && $loserCard && $winnerUser) {
                $result = $battle->processBattleResult($winnerCard, $loserCard, $winnerUser);
                if ($result['cardTransferred']) {
                    $overrides[$loserCardId] = ['life_points' => 0];
                }
            }
        }

        // Generate snapshots AFTER processing so they contain updated wins, losses, and life points
        $teamASnapshots = $this->generateTeamSnapshots($battle, 'A', $overrides);
        $teamBSnapshots = $this->generateTeamSnapshots($battle, 'B', $overrides);

        $battle->update([
            'status' => 'completed',
            'winner_team' => $winnerTeam,
            'team_a_card_data' => $teamASnapshots,
            'team_b_card_data' => $teamBSnapshots,
        ]);

        $this->logActivity(null, 'completed', "Battle finalized. Team {$winnerTeam} won!");
        $this->broadcastUpdate("Battle finalized!");
    }

    protected function generateTeamSnapshots($battle, $team, $overrides = [])
    {
        $snapshots = [];
        $teamLower = strtolower($team);

        for ($i = 1; $i <= $battle->no_players_per_team; $i++) {
            $cardId = $battle->{"team_{$teamLower}_card_{$i}"};
            if ($cardId) {
                $card = DigitalCard::find($cardId);
                if ($card) {
                    $snapshots[$i] = [
                        'wins' => $card->wins,
                        'losses' => $card->losses,
                        'win_rate' => ($card->wins + $card->losses > 0) ? round(($card->wins / ($card->wins + $card->losses)) * 100) : 0,
                        'integrity_stat' => $card->integrity_stat,
                        'life_points' => $overrides[$cardId]['life_points'] ?? $card->life_points,
                        'rarity' => $card->rarity_slug,
                        'status' => $card->status,
                        'level' => $card->level,
                    ];
                }
            }
        }

        return $snapshots;
    }

    public function cancelBattle()
    {
        $user = Auth::user();
        $isLeaderA = $user->id == $this->battle->team_a_user_1;
        $isLeaderB = $user->id == $this->battle->team_b_user_1;
        $isMarshall = $user->id == $this->battle->marshall_id;

        if (!$isLeaderA && !$isLeaderB && !$isMarshall) return;

        DB::transaction(function () use ($user, $isLeaderA, $isLeaderB, $isMarshall) {
            $battle = Battle::where('id', $this->battle->id)->lockForUpdate()->first();
            
            if ($battle->status === 'pending') {
                if ($isLeaderA) {
                    $battle->update(['status' => 'cancelled']);
                    $this->logActivity($user->id, 'cancel', "Team A Leader cancelled the pending battle.");
                    $this->broadcastUpdate("Battle cancelled by creator.");
                }
                return;
            }
            
            if ($isLeaderA) $battle->team_a_cancel_flag = true;
            if ($isLeaderB) $battle->team_b_cancel_flag = true;
            if ($isMarshall) $battle->marshall_cancel_flag = true;
            
            $battle->save();

            if ($isMarshall || ($battle->team_a_cancel_flag && $battle->team_b_cancel_flag)) {
                $battle->update(['status' => 'cancelled']);
                $this->logActivity($user->id, 'cancel', "Battle has been cancelled.");
                $this->broadcastUpdate("Battle cancelled.");
            } else {
                $this->logActivity($user->id, 'cancel_request', "{$user->username} requested to cancel the battle.");
                $this->broadcastUpdate("{$user->username} requested cancellation.");
            }
        });
        
        $this->refreshRoom();
    }

    public function respondToCancellation($agreed)
    {
        $user = Auth::user();
        $isLeaderA = $user->id == $this->battle->team_a_user_1;
        $isLeaderB = $user->id == $this->battle->team_b_user_1;

        if (!$isLeaderA && !$isLeaderB) return;

        DB::transaction(function () use ($agreed, $user, $isLeaderA, $isLeaderB) {
            $battle = Battle::where('id', $this->battle->id)->lockForUpdate()->first();

            if ($agreed) {
                if ($isLeaderA) $battle->team_a_cancel_flag = true;
                if ($isLeaderB) $battle->team_b_cancel_flag = true;
                $battle->save();

                if ($battle->team_a_cancel_flag && $battle->team_b_cancel_flag) {
                    $battle->update(['status' => 'cancelled']);
                    $this->logActivity($user->id, 'cancel_agree', "{$user->username} agreed to cancel. Battle cancelled.");
                    $this->broadcastUpdate("Battle cancelled by mutual agreement.");
                }
            } else {
                $battle->team_a_cancel_flag = false;
                $battle->team_b_cancel_flag = false;
                $battle->save();

                $this->logActivity($user->id, 'cancel_reject', "{$user->username} rejected the cancellation request.");
                $this->broadcastUpdate("Cancellation request rejected by {$user->username}.");
            }
        });

        $this->refreshRoom();
    }

    public function electMarshall($nomineeId = null)
    {
        if (in_array($this->battle->status, ['completed', 'cancelled'])) {
            session()->flash('error', 'Cannot elect a marshall for a battle that has already ended.');
            return;
        }

        $nomineeId = $nomineeId ?: $this->marshallNomineeId;
        if (!$nomineeId) return;

        $user = Auth::user();
        $isLeaderA = $user->id == $this->battle->team_a_user_1;
        $isLeaderB = $user->id == $this->battle->team_b_user_1;

        if (!$isLeaderA && !$isLeaderB) return;

        // Prevent electing someone who is already a player in the match
        for ($i = 1; $i <= $this->battle->no_players_per_team; $i++) {
            if ($this->battle->{"team_a_user_{$i}"} == $nomineeId || $this->battle->{"team_b_user_{$i}"} == $nomineeId) {
                session()->flash('error', 'Cannot elect a player currently in the battle.');
                return;
            }
        }

        if ($isLeaderA) $this->battle->update(['team_a_marshall_elect' => $nomineeId]);
        if ($isLeaderB) $this->battle->update(['team_b_marshall_elect' => $nomineeId]);

        $nominee = \App\Models\User::find($nomineeId);
        $nomineeName = $nominee ? $nominee->username : 'Unknown';

        if ($this->battle->team_a_marshall_elect && $this->battle->team_a_marshall_elect == $this->battle->team_b_marshall_elect) {
             // Consensus reached, wait for acceptance
             $nominee->notify(new \App\Notifications\BattleNotification(
                 $this->battle,
                 "Both team leaders have elected you as the MARSHALL. Will you accept?",
                 'marshall_election'
             ));

             $this->broadcastUpdate("Marshall consensus reached! Waiting for {$nomineeName} to accept.");
             $this->logActivity($user->id, 'consensus', "Marshall consensus reached: Waiting for {$nomineeName} to accept.");
        } else {
            $this->broadcastUpdate("Marshall nominated.");
            $this->logActivity($user->id, 'system', "{$user->username} nominated {$nomineeName} as Marshall.");
        }

        $this->marshallNomineeId = '';
        $this->refreshRoom();
    }

    public function acceptMarshall()
    {
        if (in_array($this->battle->status, ['completed', 'cancelled'])) {
            session()->flash('error', 'Cannot accept a marshall role for a battle that has already ended.');
            return;
        }

        $user = Auth::user();
        if ($this->battle->team_a_marshall_elect == $user->id && $this->battle->team_b_marshall_elect == $user->id) {
            $this->battle->update([
                 'marshall_id' => $user->id,
                 'team_a_marshall_elect' => null,
                 'team_b_marshall_elect' => null
            ]);
            $this->logActivity($user->id, 'system', "{$user->username} accepted the Marshall role.");
            $this->broadcastUpdate("{$user->username} accepted the Marshall role.");
            $this->refreshRoom();
        }
    }

    public function rejectMarshall()
    {
        if (in_array($this->battle->status, ['completed', 'cancelled'])) {
            return; // No need for error here, just ignore
        }

        $user = Auth::user();
        if ($this->battle->team_a_marshall_elect == $user->id && $this->battle->team_b_marshall_elect == $user->id) {
            $this->battle->update([
                 'team_a_marshall_elect' => null,
                 'team_b_marshall_elect' => null
            ]);
            $this->logActivity($user->id, 'system', "{$user->username} rejected the Marshall role.");
            $this->broadcastUpdate("{$user->username} rejected the Marshall role.");
            $this->refreshRoom();
        }
    }
    public function updateTeamName($team)
    {
        $user = Auth::user();
        if ($team == 'A' && $user->id == $this->battle->team_a_user_1) {
            $this->battle->update(['team_name_a' => $this->newTeamNameA]);
            $this->showEditTeamA = false;
        } elseif ($team == 'B' && $user->id == $this->battle->team_b_user_1) {
            $this->battle->update(['team_name_b' => $this->newTeamNameB]);
            $this->showEditTeamB = false;
        }
        $this->broadcastUpdate("Team name updated.");
        $this->refreshRoom();
    }

    protected function logActivity($userId, $type, $message)
    {
        BattleActivity::create([
            'battle_id' => $this->battle->id,
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
        ]);
    }

    protected function broadcastUpdate($message)
    {
        event(new \App\Events\BattleUpdated($this->battle, $message, 'update'));
    }

    public function render()
    {
        $user = Auth::user();
        $cards = collect();
        if ($user) {
            $cards = $user->digitalCards()
                ->where('life_points', '>', 0)
                ->get()
                ->filter(fn($c) => $c->template->game_title_id == $this->battle->game_title_id);
        }

        $activities = BattleActivity::where('battle_id', $this->battle->id)->latest()->take(50)->get();

        return view('livewire.battle-room', [
            'myEligibleCards' => $cards,
            'activities' => $activities,
        ]);
    }
}
