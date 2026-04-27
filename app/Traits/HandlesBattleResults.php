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
        // Update winner card stats
        $winnerCard->increment('wins');
        $winnerCard->refresh();
        $promoted = $winnerCard->checkPromotion();

        // Update loser card stats
        $loserCard->increment('losses');
        
        $cardTransferred = false;

        if ($loserCard->life_points > 0) {
            $loserCard->decrement('life_points');
            $loserCard->refresh();
        }

        if ($loserCard->life_points <= 0) {
            $loserCard->update([
                'owner_id' => $winnerUser->id,
                'is_trophy' => true,
                'life_points' => 3, // Reset life points for the new owner as a trophy
            ]);
            $cardTransferred = true;
        }

        return [
            'promoted' => $promoted,
            'cardTransferred' => $cardTransferred,
        ];
    }
}
