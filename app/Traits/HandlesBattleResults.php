<?php

namespace App\Traits;

use App\Models\DigitalCard;
use App\Models\User;

trait HandlesBattleResults
{
    /**
     * Process the battle result for a pair of cards.
     * 
     * @param DigitalCard $winnerCard
     * @param DigitalCard $loserCard
     * @param User $winnerUser
     * @return array Result metadata [promoted, cardTransferred]
     */
    public function processBattleResult(DigitalCard $winnerCard, DigitalCard $loserCard, User $winnerUser): array
    {
        \Log::info("Processing Battle Result: Winner Card {$winnerCard->id}, Loser Card {$loserCard->id}, Winner User {$winnerUser->id}");

        // Update winner card stats
        $winnerCard->wins += 1;
        $winnerCard->save();
        $winnerCard->refresh();
        
        $promoted = $winnerCard->checkPromotion();
        if ($promoted) {
             \Log::info("Card {$winnerCard->id} PROMOTED to Level {$winnerCard->level}");
        }

        // Update loser card stats
        $loserCard->losses += 1;
        
        $cardTransferred = false;

        if ($loserCard->life_points > 0) {
            $loserCard->life_points -= 1;
            \Log::info("Card {$loserCard->id} lost 1 LP. Remaining: {$loserCard->life_points}");
        }

        if ($loserCard->life_points <= 0) {
            $loserCard->owner_id = $winnerUser->id;
            $loserCard->is_trophy = true;
            $loserCard->life_points = 3; // Reset life points for the new owner as a trophy
            $cardTransferred = true;
            \Log::info("Card {$loserCard->id} TRANSFERRED to User {$winnerUser->id} as trophy");
            
            // Notify the winner that they received a trophy
            $winnerUser->notify(new \App\Notifications\TrophyWonNotification($loserCard));
        }
        
        $loserCard->save();

        // Update leaderboard stats for both cards
        $winnerCard->updateLeaderboardStats();
        $loserCard->updateLeaderboardStats();

        return [
            'promoted' => $promoted,
            'cardTransferred' => $cardTransferred,
        ];
    }
}
