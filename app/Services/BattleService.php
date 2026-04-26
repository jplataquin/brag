<?php

namespace App\Services;

use App\Models\DigitalCard;
use App\Models\Battle;
use App\Models\User;
use App\Models\BattleActivity;
use App\Models\BattleInvite;
use App\Notifications\BattleNotification;

class BattleService
{
    /**
     * Create a new battle.
     */
    public function createBattle(User $challenger, DigitalCard $card, ?string $terms = null): Battle
    {
        // Verify the user owns the card
        if ($card->owner_id !== $challenger->id) {
            throw new \Exception('You do not own this card.');
        }

        if ($card->life_points <= 0) {
            throw new \Exception('This card has no life points and cannot be bet in a battle.');
        }

        $battle = Battle::create([
            'room_id' => \Illuminate\Support\Str::random(12),
            'terms' => $terms,
            'challenger_id' => $challenger->id,
            'challenger_card_id' => $card->id,
            'status' => 'pending',
        ]);

        $this->logActivity($battle->id, $challenger->id, 'create', "Battle room created by {$challenger->username}.");

        return $battle;
    }

    /**
     * Join a battle as opponent.
     */
    public function joinBattle(Battle $battle, User $opponent, DigitalCard $card): Battle
    {
        if ($battle->status !== 'pending') {
            throw new \Exception('This battle is no longer available.');
        }

        if ($battle->challenger_id === $opponent->id) {
            throw new \Exception('You cannot join your own battle.');
        }

        if ($card->owner_id !== $opponent->id) {
            throw new \Exception('You do not own this card.');
        }

        if ($card->life_points <= 0) {
            throw new \Exception('This card has no life points and cannot be bet in a battle.');
        }

        // Verify same level and same game title
        $challengerCard = $battle->challengerCard;
        if ($card->level !== $challengerCard->level) {
            throw new \Exception("Only cards of equal level can be put into battle. (Challenger: Level {$challengerCard->levelName}, Yours: Level {$card->levelName})");
        }

        if ($card->template->game_title_id !== $challengerCard->template->game_title_id) {
            throw new \Exception("Only cards of the same game title can be put into battle.");
        }

        $battle->update([
            'opponent_id' => $opponent->id,
            'opponent_card_id' => $card->id,
            'status' => 'ready',
        ]);

        $this->logActivity($battle->id, $opponent->id, 'join', "{$opponent->username} joined the battle.");

        // Notify Challenger
        if ($battle->challenger) {
            $battle->challenger->notify(new BattleNotification(
                $battle,
                "{$opponent->username} joined your battle!",
                'join'
            ));
        }

        event(new \App\Events\BattleUpdated($battle, "{$opponent->username} joined the battle.", 'join'));

        return $battle->fresh();
    }

    /**
     * Declare a winner for the battle (Consensus logic).
     */
    public function declareWinner(Battle $battle, User $winner, User $declarer): Battle
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($battle, $winner, $declarer) {
            if (!in_array($battle->status, ['active', 'failed'])) {
                throw new \Exception('Winner can only be declared for active or failed battles.');
            }

            // Winner must be a participant
            if (!in_array($winner->id, [$battle->challenger_id, $battle->opponent_id])) {
                throw new \Exception('Winner must be a battle participant.');
            }

            $isChallenger = $declarer->id === $battle->challenger_id;
            $isOpponent = $declarer->id === $battle->opponent_id;
            $isMarshall = $declarer->id === $battle->marshall_id;

            if (!$isChallenger && !$isOpponent && !$isMarshall) {
                throw new \Exception('You are not authorized to declare a winner for this battle.');
            }

            // Update the declaration
            if ($isChallenger) {
                $battle->update(['challenger_declared_user_win' => $winner->id]);
            } elseif ($isOpponent) {
                $battle->update(['opponent_declared_user_win' => $winner->id]);
            } elseif ($isMarshall) {
                $battle->update(['marshall_declared_user_win' => $winner->id]);
            }

            $this->logActivity($battle->id, $declarer->id, 'declare', "{$declarer->username} declared {$winner->username} as the winner.");

            // Determine if we can finalize the battle
            $finalWinnerId = null;

            if ($isMarshall) {
                // Marshall decision is final
                $finalWinnerId = $winner->id;
                $this->logActivity($battle->id, $declarer->id, 'marshall_decision', "Marshall {$declarer->username} has made the final decision.");
            } else {
                // Check for consensus between players
                if ($battle->challenger_declared_user_win && $battle->opponent_declared_user_win) {
                    if ($battle->challenger_declared_user_win === $battle->opponent_declared_user_win) {
                        // Consensus reached
                        $finalWinnerId = $battle->challenger_declared_user_win;
                        $this->logActivity($battle->id, null, 'consensus', "Consensus reached! Both players agree on the winner.");
                    } else {
                        // Conflict
                        $battle->update(array_merge(['status' => 'failed'], $this->generateCardSnapshots($battle)));
                        $this->logActivity($battle->id, null, 'conflict', "Conflict! Players declared different winners. Battle status set to FAILED.");
                        event(new \App\Events\BattleUpdated($battle, "Conflict in winner declaration! Waiting for consensus or marshall.", 'conflict'));
                    }
                }
            }

            if ($finalWinnerId) {
                $this->finalizeBattle($battle, User::find($finalWinnerId), $declarer);
            } else {
                // Notify other participants
                $participants = collect([$battle->challenger, $battle->opponent, $battle->marshall])
                    ->filter()
                    ->reject(fn($p) => $p->id === $declarer->id);
                    
                foreach ($participants as $participant) {
                    $participant->notify(new BattleNotification(
                        $battle,
                        "{$declarer->username} declared {$winner->username} as the winner.",
                        'declare'
                    ));
                }

                event(new \App\Events\BattleUpdated($battle, "{$declarer->username} declared a winner.", 'declare'));
            }

            return $battle->fresh();
        });
    }

    /**
     * Finalize the battle result and transfer cards.
     */
    protected function finalizeBattle(Battle $battle, User $winner, User $declarer): void
    {
        // Determine loser
        $loserId = $winner->id === $battle->challenger_id
            ? $battle->opponent_id
            : $battle->challenger_id;

        $loserCardId = $winner->id === $battle->challenger_id
            ? $battle->opponent_card_id
            : $battle->challenger_card_id;

        $winnerCardId = $winner->id === $battle->challenger_id
            ? $battle->challenger_card_id
            : $battle->opponent_card_id;

        // Update winner card stats
        $winnerCard = DigitalCard::find($winnerCardId);
        $winnerCard->increment('wins');
        $winnerCard->refresh();
        $promoted = $winnerCard->checkPromotion();

        if ($promoted) {
            $winnerCard->refresh();
            $winner->notify(new BattleNotification(
                $battle, 
                "Congratulations! Your combat card leveled up to Level {$winnerCard->level} and restored all its Life Points! 🥳", 
                'promotion'
            ));
            $this->logActivity($battle->id, null, 'promotion', "{$winner->username}'s card reached Level {$winnerCard->level}!");
        }

        // Update loser card stats
        $loserCard = DigitalCard::find($loserCardId);
        $loserCard->increment('losses');
        
        $cardTransferred = false;

        if ($loserCard->life_points > 0) {
            $loserCard->decrement('life_points');
            $loserCard->refresh();
        }

        if ($loserCard->life_points <= 0) {
            $loserCard->update([
                'owner_id' => $winner->id,
                'is_trophy' => true,
                'life_points' => 3,
            ]);
            $cardTransferred = true;
        }

        $overrides = [];
        if ($cardTransferred) {
            $overrides[$loserCardId]['life_points'] = 0;
        }

        // Mark battle as completed and snapshot stats
        $battle->update(array_merge([
            'winner_id' => $winner->id,
            'status' => 'completed',
        ], $this->generateCardSnapshots($battle, $overrides)));

        $this->logActivity($battle->id, $declarer->id, 'winner', "Battle finalized. {$winner->username} is the official winner.");

        if ($cardTransferred) {
            $this->logActivity($battle->id, null, 'transfer', "{$loserCard->template->card_title} ran out of life points and was transferred to {$winner->username} as a trophy.");
        } else {
            $this->logActivity($battle->id, null, 'damage', "{$loserCard->template->card_title} lost 1 life point. Remaining: {$loserCard->life_points}");
        }
        
        // Notify Winner
        $winnerMsg = $cardTransferred 
            ? "Victory! You won the battle against {$loserCard->originalOwner->username} and claimed their card as a trophy!"
            : "Victory! You won the battle against {$loserCard->originalOwner->username}!";
            
        $winner->notify(new BattleNotification($battle, $winnerMsg, 'victory'));

        // Notify Loser
        $loser = User::find($loserId);
        if ($loser) {
            $loserMsg = $cardTransferred
                ? "Defeat! {$winner->username} won the battle and claimed your card because it ran out of life points."
                : "Defeat! {$winner->username} won the battle. Your card lost 1 life point.";
                
            $loser->notify(new BattleNotification($battle, $loserMsg, 'defeat'));
        }

        // Notify Marshall if present
        if ($battle->marshall_id) {
            if ($declarer->id !== $battle->marshall_id) {
                $battle->marshall->notify(new BattleNotification(
                    $battle,
                    "Battle finalized. {$winner->username} won.",
                    'finalized'
                ));
            }
            
            // Marshall automatically leaves the room but retains their historical assignment
            $this->logActivity($battle->id, $battle->marshall_id, 'marshall_leave', "{$battle->marshall->username} has automatically left the battle room.");
        }

        event(new \App\Events\BattleUpdated($battle, "Battle finalized! {$winner->username} won.", 'winner'));
    }

    /**
     * Cancel or request cancellation of a battle.
     */
    public function cancelBattle(Battle $battle, User $user): Battle
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($battle, $user) {
            // Case 1: Challenger cancels when Pending and no opponent
            if ($battle->status === 'pending' && is_null($battle->opponent_id)) {
                if ($battle->challenger_id !== $user->id) {
                    throw new \Exception('Only the challenger can cancel this battle.');
                }

                $battle->update(array_merge(['status' => 'cancelled'], $this->generateCardSnapshots($battle)));
                $this->logActivity($battle->id, $user->id, 'cancel', "Battle room cancelled by {$user->username}.");
                
                event(new \App\Events\BattleUpdated($battle, "Battle room cancelled by {$user->username}.", 'cancel'));
                
                return $battle;
            }

            // Case 2: Marshall cancels an active battle
            if ($battle->status === 'active' && $battle->marshall_id === $user->id) {
                $battle->update(array_merge([
                    'status' => 'cancelled',
                    'challenger_cancel' => false,
                    'opponent_cancel' => false,
                ], $this->generateCardSnapshots($battle)));
                
                $this->logActivity($battle->id, $user->id, 'cancel', "Battle was cancelled by the Marshall ({$user->username}).");
                
                event(new \App\Events\BattleUpdated($battle, "The marshall has cancelled the battle.", 'cancel'));
                
                return $battle;
            }

            // Case 3: Player requests cancellation in an active/ready battle
            if (in_array($battle->status, ['active', 'ready'])) {
                $isChallenger = $battle->challenger_id === $user->id;
                $isOpponent = $battle->opponent_id === $user->id;

                if (!$isChallenger && !$isOpponent) {
                    throw new \Exception('Only players or marshalls can cancel this battle.');
                }

                if ($isChallenger) {
                    $battle->update([
                        'challenger_cancel' => true,
                        'challenger_cancel_timestamp' => now(),
                    ]);
                } else {
                    $battle->update([
                        'opponent_cancel' => true,
                        'opponent_cancel_timestamp' => now(),
                    ]);
                }

                $this->logActivity($battle->id, $user->id, 'cancel_request', "{$user->username} has requested to cancel the battle.");

                // Notify other player
                $otherPlayer = $isChallenger ? $battle->opponent : $battle->challenger;
                if ($otherPlayer) {
                    $otherPlayer->notify(new BattleNotification(
                        $battle,
                        "{$user->username} requested to cancel the battle.",
                        'cancel_request'
                    ));
                }

                // If both agreed, cancel the room
                if ($battle->challenger_cancel && $battle->opponent_cancel) {
                    $battle->update(array_merge(['status' => 'cancelled'], $this->generateCardSnapshots($battle)));
                    $this->logActivity($battle->id, null, 'cancel', "Battle cancelled by mutual agreement.");
                    event(new \App\Events\BattleUpdated($battle, "Battle cancelled by mutual agreement.", 'cancel'));
                } else {
                    event(new \App\Events\BattleUpdated($battle, "{$user->username} requested to cancel the battle.", 'cancel_request_' . $user->id));
                }

                return $battle;
            }

            throw new \Exception('This battle cannot be cancelled in its current state.');
        });
    }

    /**
     * Respond to a cancellation request.
     */
    public function respondToCancellation(Battle $battle, User $user, bool $agreed): Battle
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($battle, $user, $agreed) {
            $isChallenger = $battle->challenger_id === $user->id;
            $isOpponent = $battle->opponent_id === $user->id;

            if (!$isChallenger && !$isOpponent) {
                throw new \Exception('Only players can respond to cancellation requests.');
            }

            if ($agreed) {
                if ($isChallenger) {
                    $battle->update([
                        'challenger_cancel' => true,
                        'challenger_cancel_timestamp' => now(),
                    ]);
                } else {
                    $battle->update([
                        'opponent_cancel' => true,
                        'opponent_cancel_timestamp' => now(),
                    ]);
                }

                $this->logActivity($battle->id, $user->id, 'cancel_agree', "{$user->username} agreed to cancel the battle.");

                if ($battle->challenger_cancel && $battle->opponent_cancel) {
                    $battle->update(array_merge(['status' => 'cancelled'], $this->generateCardSnapshots($battle)));
                    $this->logActivity($battle->id, null, 'cancel', "Battle cancelled by mutual agreement.");
                    event(new \App\Events\BattleUpdated($battle, "Battle cancelled by mutual agreement.", 'cancel'));
                } else {
                    event(new \App\Events\BattleUpdated($battle, "{$user->username} agreed to cancel the battle.", 'cancel_agree'));
                }
            } else {
                // Rejected
                $battle->update([
                    'challenger_cancel' => false,
                    'opponent_cancel' => false,
                    'challenger_cancel_timestamp' => null,
                    'opponent_cancel_timestamp' => null,
                ]);

                $this->logActivity($battle->id, $user->id, 'cancel_reject', "{$user->username} rejected the cancellation request.");
                event(new \App\Events\BattleUpdated($battle, "Cancellation request was rejected by {$user->username}.", 'cancel_reject'));
            }

            return $battle;
        });
    }

    /**
     * Start the battle (transition from ready to active).
     */
    public function startBattle(Battle $battle, User $user): Battle
    {
        if ($battle->challenger_id !== $user->id) {
            throw new \Exception('Only the challenger can start the battle.');
        }

        if ($battle->status !== 'ready') {
            throw new \Exception('The battle is not ready to start.');
        }

        $battle->update(['status' => 'active']);

        $this->logActivity($battle->id, $user->id, 'start', "Battle has officially BEGUN!");

        // Notify Opponent
        if ($battle->opponent) {
            $battle->opponent->notify(new BattleNotification(
                $battle,
                "The battle against {$user->username} has BEGUN!",
                'start'
            ));
        }

        event(new \App\Events\BattleUpdated($battle, "The battle has officially begun!", 'start'));

        return $battle->fresh();
    }

    /**
     * Reject the opponent and revert to pending.
     */
    public function rejectOpponent(Battle $battle, User $user): Battle
    {
        if ($battle->challenger_id !== $user->id) {
            throw new \Exception('Only the challenger can reject an opponent.');
        }

        if ($battle->status !== 'ready') {
            throw new \Exception('Can only reject opponents when the battle is ready.');
        }

        $opponentName = $battle->opponent ? $battle->opponent->username : 'Opponent';
        $opponentId = $battle->opponent_id;

        $battle->update([
            'status' => 'pending',
            'opponent_id' => null,
            'opponent_card_id' => null,
            'challenger_cancel' => false,
            'opponent_cancel' => false,
            'challenger_cancel_timestamp' => null,
            'opponent_cancel_timestamp' => null,
            'challenger_declared_user_win' => null,
            'opponent_declared_user_win' => null,
            'marshall_declared_user_win' => null,
            'marshall_id' => null,
            'challenger_marshall_id' => null,
            'opponent_marshall_id' => null,
        ]);

        $this->logActivity($battle->id, $user->id, 'reject', "{$opponentName}'s bet was rejected by the challenger.");

        // Notify Rejected Opponent
        if ($opponentId) {
            $rejectedUser = User::find($opponentId);
            if ($rejectedUser) {
                $rejectedUser->notify(new BattleNotification(
                    $battle,
                    "Your bet in {$user->username}'s battle was rejected.",
                    'reject'
                ));
            }
        }

        // Include the rejected opponent's ID in the message payload so the frontend knows who was rejected
        event(new \App\Events\BattleUpdated($battle, "{$opponentName}'s bet was rejected.", 'reject_' . $opponentId));

        return $battle->fresh();
    }

    /**
     * Send an invite.
     */
    public function sendInvite(Battle $battle, User $inviter, User $invited, string $role): BattleInvite
    {
        // Only challenger or opponent can invite
        if (!in_array($inviter->id, [$battle->challenger_id, $battle->opponent_id])) {
            throw new \Exception('Only battle participants can send invites.');
        }

        if ($invited->id === $inviter->id) {
            throw new \Exception('You cannot invite yourself.');
        }

        // Check if already invited
        $exists = BattleInvite::where('battle_id', $battle->id)
            ->where('invited_user_id', $invited->id)
            ->where('role', $role)
            ->where('status', 'pending')
            ->exists();
        
        if ($exists) {
            throw new \Exception('This user already has a pending invite for this role.');
        }

        $invite = BattleInvite::create([
            'battle_id' => $battle->id,
            'invited_user_id' => $invited->id,
            'role' => $role,
            'status' => 'pending',
        ]);

        $roleText = strtoupper($role);
        $this->logActivity($battle->id, $inviter->id, 'invite', "{$inviter->username} invited {$invited->username} as {$roleText}.");

        // Send Notification
        $invited->notify(new BattleNotification(
            $battle,
            "{$inviter->username} invited you to be an {$roleText} in a battle.",
            'invite'
        ));

        return $invite;
    }

    /**
     * Elect an marshall.
     */
    public function electMarshall(Battle $battle, User $elector, User $nominee): Battle
    {
        if (in_array($battle->status, ['completed', 'cancelled'])) {
            throw new \Exception('Cannot elect an marshall for a completed or cancelled battle.');
        }

        // Players in the battle room cannot be an marshall
        if (in_array($nominee->id, [$battle->challenger_id, $battle->opponent_id])) {
            throw new \Exception('Battle participants cannot be elected as marshalls.');
        }

        $isChallenger = $elector->id === $battle->challenger_id;
        $isOpponent = $elector->id === $battle->opponent_id;

        if (!$isChallenger && !$isOpponent) {
            throw new \Exception('Only battle participants can elect an marshall.');
        }

        if ($isChallenger) {
            $battle->update(['challenger_marshall_id' => $nominee->id]);
        } else {
            $battle->update(['opponent_marshall_id' => $nominee->id]);
        }

        $battle->refresh();

        $this->logActivity($battle->id, $elector->id, 'elect_marshall', "{$elector->username} elected @{$nominee->username} as marshall.");

        // Check for consensus
        if ($battle->challenger_marshall_id && $battle->opponent_marshall_id) {
            if ($battle->challenger_marshall_id === $battle->opponent_marshall_id) {
                // Same person elected by both!
                $nominee->notify(new BattleNotification(
                    $battle,
                    "Both players have elected you as an MARSHALL. Will you accept?",
                    'marshall_election'
                ));
                
                $this->logActivity($battle->id, null, 'marshall_election', "Consensus reached! Both players elected @{$nominee->username}. Waiting for response.");
            }
        }

        $battle->refresh();

        event(new \App\Events\BattleUpdated($battle, "{$elector->username} elected @{$nominee->username} as marshall.", 'elect_marshall'));

        return $battle->fresh();
    }

    /**
     * Respond to marshall election.
     */
    public function respondToMarshallElection(Battle $battle, User $marshall, bool $accept): Battle
    {
        if (in_array($battle->status, ['completed', 'cancelled'])) {
            throw new \Exception('Cannot respond to an marshall election for a completed or cancelled battle.');
        }

        // Race condition check: Ensure both still match this marshall
        if ($battle->challenger_marshall_id !== $marshall->id || $battle->opponent_marshall_id !== $marshall->id) {
            throw new \Exception('The marshall election is no longer valid or has changed.');
        }

        if ($accept) {
            $battle->update([
                'marshall_id' => $marshall->id,
                'challenger_marshall_id' => null,
                'opponent_marshall_id' => null,
            ]);

            $this->logActivity($battle->id, $marshall->id, 'marshall_accepted', "{$marshall->username} accepted the MARSHALL role.");
            
            // Notify players
            $battle->challenger->notify(new BattleNotification($battle, "{$marshall->username} is now the marshall.", 'marshall_accepted'));
            if ($battle->opponent) {
                $battle->opponent->notify(new BattleNotification($battle, "{$marshall->username} is now the marshall.", 'marshall_accepted'));
            }

            event(new \App\Events\BattleUpdated($battle, "{$marshall->username} joined as Marshall.", 'marshall_accepted'));
        } else {
            // Rejected, reset election for this nominee
            $battle->update([
                'challenger_marshall_id' => null,
                'opponent_marshall_id' => null,
            ]);

            $this->logActivity($battle->id, $marshall->id, 'marshall_rejected', "{$marshall->username} rejected the MARSHALL role.");
            
            // Notify players
            $battle->challenger->notify(new BattleNotification($battle, "{$marshall->username} rejected the marshall role.", 'marshall_rejected'));
            if ($battle->opponent) {
                $battle->opponent->notify(new BattleNotification($battle, "{$marshall->username} rejected the marshall role.", 'marshall_rejected'));
            }

            event(new \App\Events\BattleUpdated($battle, "Marshall nominee rejected the role.", 'marshall_rejected'));
        }

        return $battle->fresh();
    }

    /**
     * Marshall leaves the battle.
     */
    public function marshallLeave(Battle $battle, User $marshall): Battle
    {
        if ($battle->marshall_id !== $marshall->id) {
            throw new \Exception('You are not the marshall of this battle.');
        }

        if (in_array($battle->status, ['completed', 'cancelled'])) {
            throw new \Exception('You cannot leave a battle that has already ended.');
        }

        $battle->update([
            'marshall_id' => null,
            'challenger_marshall_id' => null,
            'opponent_marshall_id' => null,
            'marshall_declared_user_win' => null,
        ]);

        $this->logActivity($battle->id, $marshall->id, 'marshall_leave', "{$marshall->username} has left the battle room.");

        // Notify players
        $battle->challenger->notify(new BattleNotification($battle, "The marshall ({$marshall->username}) has left the battle.", 'marshall_leave'));
        if ($battle->opponent) {
            $battle->opponent->notify(new BattleNotification($battle, "The marshall ({$marshall->username}) has left the battle.", 'marshall_leave'));
        }

        event(new \App\Events\BattleUpdated($battle, "The marshall has left.", 'marshall_leave'));

        return $battle->fresh();
    }

    /**
     * Poke the other player in the battle room.
     */
    public function pokePlayer(Battle $battle, User $poker): void
    {
        if (!in_array($poker->id, [$battle->challenger_id, $battle->opponent_id])) {
            throw new \Exception('Only participants can poke.');
        }

        $target = $poker->id === $battle->challenger_id ? $battle->opponent : $battle->challenger;

        if (!$target) {
            throw new \Exception('There is no opponent to poke yet.');
        }

        // Send standard database/broadcast notification
        $target->notify(new BattleNotification(
            $battle,
            "👉 {$poker->username} poked you in battle room #{$battle->id}!",
            'poke'
        ));

        // Send a room-wide websocket event targeted to the specific user via the type
        event(new \App\Events\BattleUpdated($battle, "👉 {$poker->username} poked you!", 'poke_' . $target->id));
    }

    /**
     * Log activity.
     */
    private function logActivity(int $battleId, ?int $userId, string $type, string $message): void
    {
        BattleActivity::create([
            'battle_id' => $battleId,
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
        ]);
    }

    /**
     * Generate snapshot of card metadata.
     */
    private function generateCardSnapshots(Battle $battle, array $overrides = []): array
    {
        $snapshots = [];

        if ($battle->challenger_card_id) {
            $cCard = DigitalCard::find($battle->challenger_card_id);
            if ($cCard) {
                $snapshots['challenger_card_data'] = [
                    'wins' => $cCard->wins,
                    'losses' => $cCard->losses,
                    'win_rate' => ($cCard->wins + $cCard->losses > 0) ? round(($cCard->wins / ($cCard->wins + $cCard->losses)) * 100) : 0,
                    'integrity_stat' => $cCard->integrity_stat,
                    'life_points' => isset($overrides[$cCard->id]['life_points']) ? $overrides[$cCard->id]['life_points'] : $cCard->life_points,
                    'rarity' => $cCard->rarity_slug,
                    'status' => $cCard->status,
                    'level' => $cCard->level,
                ];
            }
        }

        if ($battle->opponent_card_id) {
            $oCard = DigitalCard::find($battle->opponent_card_id);
            if ($oCard) {
                $snapshots['opponent_card_data'] = [
                    'wins' => $oCard->wins,
                    'losses' => $oCard->losses,
                    'win_rate' => ($oCard->wins + $oCard->losses > 0) ? round(($oCard->wins / ($oCard->wins + $oCard->losses)) * 100) : 0,
                    'integrity_stat' => $oCard->integrity_stat,
                    'life_points' => isset($overrides[$oCard->id]['life_points']) ? $overrides[$oCard->id]['life_points'] : $oCard->life_points,
                    'rarity' => $oCard->rarity_slug,
                    'status' => $oCard->status,
                    'level' => $oCard->level,
                ];
            }
        }

        return $snapshots;
    }}
