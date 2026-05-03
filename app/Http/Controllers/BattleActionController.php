<?php

namespace App\Http\Controllers;

use App\Models\Battle;
use App\Models\DigitalCard;
use App\Models\User;
use App\Models\BattleActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BattleActionController extends Controller
{
    public function show(Battle $battle)
    {
        $user = Auth::user();
        $myEligibleCards = collect();
        if ($user) {
            $myEligibleCards = $user->digitalCards()
                ->where('life_points', '>', 0)
                ->get()
                ->filter(fn($c) => $c->template->game_title_id == $battle->game_title_id);
        }

        // Logic for Participant Check
        $isParticipant = false;
        if ($user) {
            for ($i = 1; $i <= $battle->no_players_per_team; $i++) {
                if ($battle->{"team_a_user_{$i}"} == $user->id && $battle->{"team_a_card_{$i}"}) $isParticipant = true;
                if ($battle->{"team_b_user_{$i}"} == $user->id && $battle->{"team_b_card_{$i}"}) $isParticipant = true;
            }
            if ($battle->marshall_id == $user->id) $isParticipant = true;
        }

        return view('battles.room', compact('battle'));
    }

    protected function logActivity($battleId, $userId, $type, $message)
    {
        BattleActivity::create([
            'battle_id' => $battleId,
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
        ]);
    }

    protected function broadcastUpdate($battle, $message)
    {
        event(new \App\Events\BattleUpdated($battle, $message, 'update'));
    }

    public function updateTeamName(Request $request, Battle $battle)
    {
        $request->validate([
            'team' => 'required|in:A,B',
            'name' => 'required|string|max:50',
        ]);

        $user = Auth::user();
        if ($request->team == 'A' && $user->id == $battle->team_a_user_1) {
            $battle->update(['team_name_a' => $request->name]);
        } elseif ($request->team == 'B' && $user->id == $battle->team_b_user_1) {
            $battle->update(['team_name_b' => $request->name]);
        }
        $this->broadcastUpdate($battle, "Team name updated.");
        return back();
    }

    public function join(Request $request, Battle $battle)
    {
        $request->validate([
            'selectedCardId' => 'required|exists:digital_cards,id',
            'joiningTeam' => 'required|in:A,B',
            'pairingSlot' => 'required|integer|min:1|max:6',
        ]);

        $user = Auth::user();
        $currentRoom = $user->currentBattleRoom();
        if ($currentRoom && !($currentRoom['type'] === 'team' && $currentRoom['battle']->id === $battle->id)) {
            return back()->with('error', 'You are already in another active battle room. You must finish or cancel it before joining this one.');
        }

        $card = DigitalCard::find($request->selectedCardId);

        if ($card->template->game_title_id != $battle->game_title_id) {
            return back()->with('error', 'Card must match the game title.');
        }

        if ($card->life_points <= 0) {
            return back()->with('error', 'Card has no life points.');
        }

        if ($battle->team_a_user_1 == $user->id) {
            return back()->with('error', 'The room creator cannot change their slot.');
        }

        try {
            DB::transaction(function () use ($user, $card, $battle, $request) {
                $battle = clone $battle;
                $battle = Battle::where('id', $battle->id)->lockForUpdate()->first();

                $team = $request->joiningTeam;
                $slot = $request->pairingSlot;
                $teamLower = strtolower($team);

                $userField = "team_{$teamLower}_user_{$slot}";
                $cardField = "team_{$teamLower}_card_{$slot}";

                if ($battle->$userField && $battle->$userField != $user->id) {
                     throw new \Exception("Slot {$team}{$slot} has already been taken by another player.");
                }

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

                $battle->$userField = $user->id;
                $battle->$cardField = $card->id;
                $battle->save();

                $actionWord = $wasAlreadyInBattle ? "transferred to" : "joined";
                $this->logActivity($battle->id, $user->id, 'join', "{$user->username} {$actionWord} Team {$team} (Slot {$slot}).");
            });

            $this->broadcastUpdate($battle, "{$user->username} joined or transferred within the battle.");
            return redirect()->route('battles.room', $battle)->with('success', 'Joined successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to join: ' . $e->getMessage());
        }
    }

    public function standUp(Battle $battle)
    {
        $user = Auth::user();
        
        if ($battle->status !== 'pending') {
            return back()->with('error', 'You can only stand up while the battle is pending.');
        }

        if ($battle->team_a_user_1 == $user->id) {
            return back()->with('error', 'The creator of the battle room cannot stand up.');
        }

        DB::transaction(function () use ($user, $battle) {
            $battle = Battle::where('id', $battle->id)->lockForUpdate()->first();
            
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
                $this->logActivity($battle->id, $user->id, 'leave', "{$user->username} stood up from {$slotInfo}.");
            }
        });

        $this->broadcastUpdate($battle, "{$user->username} left their slot.");
        return back();
    }

    public function teamBReady(Battle $battle)
    {
        $user = Auth::user();
        if ($user->id != $battle->team_b_user_1) {
            return back()->with('error', 'Only Team B leader can declare ready status.');
        }

        if (!$battle->is_team_b_full) {
            return back()->with('error', 'All Team B slots must be filled before getting ready.');
        }

        $battle->update(['team_b_ready' => true]);
        $this->logActivity($battle->id, $user->id, 'ready', "Team B is now READY!");
        $this->broadcastUpdate($battle, "Team B is ready!");
        return back();
    }

    public function start(Battle $battle)
    {
        $user = Auth::user();
        if ($user->id != $battle->team_a_user_1) {
            return back()->with('error', 'Only Team A leader can start the battle.');
        }

        if (!$battle->team_b_ready) {
            return back()->with('error', 'Team B must be ready before starting.');
        }

        for ($i = 1; $i <= $battle->no_players_per_team; $i++) {
            if (!$battle->{"team_a_user_{$i}"} || !$battle->{"team_b_user_{$i}"}) {
                return back()->with('error', 'All player slots must be filled before starting.');
            }
        }

        $battle->update(['status' => 'active']);
        $this->logActivity($battle->id, $user->id, 'start', "Battle has officially BEGUN!");
        $this->broadcastUpdate($battle, "Battle started!");
        return back();
    }

    public function cancel(Battle $battle)
    {
        $user = Auth::user();
        $isLeaderA = $user->id == $battle->team_a_user_1;
        $isLeaderB = $user->id == $battle->team_b_user_1;
        $isMarshall = $user->id == $battle->marshall_id;

        if (!$isLeaderA && !$isLeaderB && !$isMarshall) return back();

        DB::transaction(function () use ($user, $isLeaderA, $isLeaderB, $isMarshall, $battle) {
            $battle = Battle::where('id', $battle->id)->lockForUpdate()->first();
            
            if ($battle->status === 'pending') {
                if ($isLeaderA) {
                    $battle->update(['status' => 'cancelled']);
                    $this->logActivity($battle->id, $user->id, 'cancel', "Team A Leader cancelled the pending battle.");
                    $this->broadcastUpdate($battle, "Battle cancelled by creator.");
                }
                return;
            }
            
            if ($isLeaderA) $battle->team_a_cancel_flag = true;
            if ($isLeaderB) $battle->team_b_cancel_flag = true;
            if ($isMarshall) $battle->marshall_cancel_flag = true;
            
            $battle->save();

            if ($isMarshall || ($battle->team_a_cancel_flag && $battle->team_b_cancel_flag)) {
                $battle->update(['status' => 'cancelled']);
                $this->logActivity($battle->id, $user->id, 'cancel', "Battle has been cancelled.");
                $this->broadcastUpdate($battle, "Battle cancelled.");
            } else {
                $this->logActivity($battle->id, $user->id, 'cancel_request', "{$user->username} requested to cancel the battle.");
                $this->broadcastUpdate($battle, "{$user->username} requested cancellation.");
            }
        });
        
        return back();
    }

    public function respondCancel(Request $request, Battle $battle)
    {
        $agreed = $request->boolean('agreed');
        $user = Auth::user();
        $isLeaderA = $user->id == $battle->team_a_user_1;
        $isLeaderB = $user->id == $battle->team_b_user_1;

        if (!$isLeaderA && !$isLeaderB) return back();

        DB::transaction(function () use ($agreed, $user, $isLeaderA, $isLeaderB, $battle) {
            $battle = clone $battle;
            $battle = Battle::where('id', $battle->id)->lockForUpdate()->first();

            if ($agreed) {
                if ($isLeaderA) $battle->team_a_cancel_flag = true;
                if ($isLeaderB) $battle->team_b_cancel_flag = true;
                $battle->save();

                if ($battle->team_a_cancel_flag && $battle->team_b_cancel_flag) {
                    $battle->update(['status' => 'cancelled']);
                    $this->logActivity($battle->id, $user->id, 'cancel_agree', "{$user->username} agreed to cancel. Battle cancelled.");
                    $this->broadcastUpdate($battle, "Battle cancelled by mutual agreement.");
                }
            } else {
                $battle->team_a_cancel_flag = false;
                $battle->team_b_cancel_flag = false;
                $battle->save();

                $this->logActivity($battle->id, $user->id, 'cancel_reject', "{$user->username} rejected the cancellation request.");
                $this->broadcastUpdate($battle, "Cancellation request rejected by {$user->username}.");
            }
        });

        return back();
    }

    public function declareWin(Request $request, Battle $battle)
    {
        $team = $request->input('team');
        $user = Auth::user();
        $isLeaderA = $user->id == $battle->team_a_user_1;
        $isLeaderB = $user->id == $battle->team_b_user_1;
        $isMarshall = $user->id == $battle->marshall_id;

        if (!$isLeaderA && !$isLeaderB && !$isMarshall) {
             return back()->with('error', 'Unauthorized.');
        }

        DB::transaction(function () use ($team, $user, $isLeaderA, $isLeaderB, $isMarshall, $battle) {
            $battle = clone $battle;
            $battle = Battle::where('id', $battle->id)->lockForUpdate()->first();
            
            if ($isLeaderA) $battle->team_a_declare_win = $team;
            if ($isLeaderB) $battle->team_b_declare_win = $team;
            if ($isMarshall) $battle->marshall_declare_win = $team;
            
            $battle->save();

            $this->logActivity($battle->id, $user->id, 'declare', "{$user->username} declared Team {$team} as winner.");

            $finalWinnerTeam = null;
            if ($isMarshall) {
                $finalWinnerTeam = $team;
                $this->logActivity($battle->id, $user->id, 'marshall_decision', "Marshall {$user->username} has made the final decision: Team {$team} wins.");
            } elseif ($battle->team_a_declare_win && $battle->team_b_declare_win) {
                if ($battle->team_a_declare_win == $battle->team_b_declare_win) {
                    $finalWinnerTeam = $battle->team_a_declare_win;
                } else {
                    $battle->update(['status' => 'failed']);
                    $this->broadcastUpdate($battle, "Conflict in declaration!");
                }
            }

            if ($finalWinnerTeam) {
                $this->finalizeBattle($battle, $finalWinnerTeam);
            } else {
                $this->broadcastUpdate($battle, "{$user->username} declared a winner.");
            }
        });
        
        return back();
    }

    protected function finalizeBattle($battle, $winnerTeam)
    {
        $loserTeam = $winnerTeam == 'A' ? 'B' : 'A';
        $winnerTeamLower = strtolower($winnerTeam);
        $loserTeamLower = strtolower($loserTeam);

        $overrides = [];

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

        $teamASnapshots = $this->generateTeamSnapshots($battle, 'A', $overrides);
        $teamBSnapshots = $this->generateTeamSnapshots($battle, 'B', $overrides);

        $battle->update([
            'status' => 'completed',
            'winner_team' => $winnerTeam,
            'team_a_card_data' => $teamASnapshots,
            'team_b_card_data' => $teamBSnapshots,
        ]);

        $this->logActivity($battle->id, null, 'completed', "Battle finalized. Team {$winnerTeam} won!");
        $this->broadcastUpdate($battle, "Battle finalized!");
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

    public function electMarshall(Request $request, Battle $battle)
    {
        $nomineeId = $request->input('marshall_id');
        if (!$nomineeId) return back();

        $user = Auth::user();
        $isLeaderA = $user->id == $battle->team_a_user_1;
        $isLeaderB = $user->id == $battle->team_b_user_1;

        if (!$isLeaderA && !$isLeaderB) return back();

        for ($i = 1; $i <= $battle->no_players_per_team; $i++) {
            if ($battle->{"team_a_user_{$i}"} == $nomineeId || $battle->{"team_b_user_{$i}"} == $nomineeId) {
                return back()->with('error', 'Cannot elect a player currently in the battle.');
            }
        }

        if ($isLeaderA) $battle->update(['team_a_marshall_elect' => $nomineeId]);
        if ($isLeaderB) $battle->update(['team_b_marshall_elect' => $nomineeId]);

        $nominee = \App\Models\User::find($nomineeId);
        $nomineeName = $nominee ? $nominee->username : 'Unknown';

        if ($battle->team_a_marshall_elect && $battle->team_a_marshall_elect == $battle->team_b_marshall_elect) {
             $nominee->notify(new \App\Notifications\BattleNotification(
                 $battle,
                 "Both team leaders have elected you as the MARSHALL. Will you accept?",
                 'marshall_election'
             ));

             $this->broadcastUpdate($battle, "Marshall consensus reached! Waiting for {$nomineeName} to accept.");
             $this->logActivity($battle->id, $user->id, 'consensus', "Marshall consensus reached: Waiting for {$nomineeName} to accept.");
        } else {
            $this->broadcastUpdate($battle, "Marshall nominated.");
            $this->logActivity($battle->id, $user->id, 'system', "{$user->username} nominated {$nomineeName} as Marshall.");
        }

        return back()->with('success', 'Marshall nominated successfully');
    }

    public function acceptMarshall(Battle $battle)
    {
        $user = Auth::user();
        if ($battle->team_a_marshall_elect == $user->id && $battle->team_b_marshall_elect == $user->id) {
            $battle->update([
                 'marshall_id' => $user->id,
                 'team_a_marshall_elect' => null,
                 'team_b_marshall_elect' => null
            ]);
            $this->logActivity($battle->id, $user->id, 'system', "{$user->username} accepted the Marshall role.");
            $this->broadcastUpdate($battle, "{$user->username} accepted the Marshall role.");
        }
        return back();
    }

    public function rejectMarshall(Battle $battle)
    {
        $user = Auth::user();
        if ($battle->team_a_marshall_elect == $user->id && $battle->team_b_marshall_elect == $user->id) {
            $battle->update([
                 'team_a_marshall_elect' => null,
                 'team_b_marshall_elect' => null
            ]);
            $this->logActivity($battle->id, $user->id, 'system', "{$user->username} rejected the Marshall role.");
            $this->broadcastUpdate($battle, "{$user->username} rejected the Marshall role.");
        }
        return back();
    }

    public function invite(Request $request, Battle $battle)
    {
        $inviteNomineeId = $request->input('user_id');
        if (!$inviteNomineeId) return back();

        $user = Auth::user();
        $isLeaderA = $user->id == $battle->team_a_user_1;
        $isLeaderB = $user->id == $battle->team_b_user_1;

        if (!$isLeaderA && !$isLeaderB) return back();

        $invitedUser = User::find($inviteNomineeId);
        if ($invitedUser) {
            $teamName = $isLeaderA ? $battle->team_name_a : $battle->team_name_b;
            $invitedUser->notify(new \App\Notifications\BattleNotification(
                $battle,
                "{$user->username} has invited you to join {$teamName} in Battle #{$battle->id}!",
                'invite',
                route('battles.join', $battle)
            ));
            
            $this->logActivity($battle->id, $user->id, 'invite', "{$user->username} invited @{$invitedUser->username} to join the battle.");
            return back()->with('success', "Invite sent to @{$invitedUser->username}");
        }

        return back();
    }
}
