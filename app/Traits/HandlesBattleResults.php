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

        // Calculate Elo Rating Change
        $winnerElo = $winnerCard->elo_score ?? 1000;
        $loserElo = $loserCard->elo_score ?? 1000;

        $kFactor = 32;

        // Expected score for winner card
        $expectedWinner = 1 / (1 + pow(10, ($loserElo - $winnerElo) / 400));
        // Expected score for loser card
        $expectedLoser = 1 / (1 + pow(10, ($winnerElo - $loserElo) / 400));

        // Elo adjustments
        $newWinnerElo = (int) round($winnerElo + $kFactor * (1 - $expectedWinner));
        $newLoserElo = (int) round($loserElo + $kFactor * (0 - $expectedLoser));

        // Update Elo values (minimum limit of 100 Elo)
        $winnerCard->elo_score = max(100, $newWinnerElo);
        $loserCard->elo_score = max(100, $newLoserElo);

        \Log::info("Elo Rating Updated: Winner Card {$winnerCard->id} ($winnerElo -> {$winnerCard->elo_score}), Loser Card {$loserCard->id} ($loserElo -> {$loserCard->elo_score})");

        $isNoQuarter = (isset($this->mode) && $this->mode === 'no_quarter');
        $loserOriginalLP = $loserCard->life_points;

        // Update winner card stats
        $winIncrement = 1;
        if ($isNoQuarter && $loserOriginalLP > 0) {
            $winIncrement = $loserOriginalLP;
        }

        $winnerCard->wins += $winIncrement;
        $winnerCard->save();
        $winnerCard->refresh();
        
        $promoted = $winnerCard->checkPromotion();
        if ($promoted) {
             \Log::info("Card {$winnerCard->id} PROMOTED to Level {$winnerCard->level}");
        }

        // Update loser card stats
        $loserCard->losses += 1;
        
        $cardTransferred = false;

        if ($isNoQuarter) {
            if ($loserCard->life_points > 0) {
                $loserCard->life_points = 0;
                \Log::info("Card {$loserCard->id} lost ALL LP in No Quarter mode.");
            }
        } else {
            if ($loserCard->life_points > 0) {
                $loserCard->life_points -= 1;
                \Log::info("Card {$loserCard->id} lost 1 LP. Remaining: {$loserCard->life_points}");
            }
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

        return [
            'promoted' => $promoted,
            'cardTransferred' => $cardTransferred,
        ];
    }
}
