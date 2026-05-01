<?php

namespace App\Services;

use App\Models\DigitalCard;
use App\Models\Template;
use App\Models\User;
use Carbon\Carbon;

class CardForgeService
{
    /**
     * Check if the user can forge a card from the given template.
     *
     * @return array{can_forge: bool, reason: string|null, cooldown_ends: \Carbon\Carbon|null}
     */
    public function canForge(User $user, Template $template): array
    {
        // Check if user owns the template
        if ($template->user_id !== $user->id) {
            return [
                'can_forge' => false,
                'reason' => 'You can only forge cards from your own templates.',
                'cooldown_ends' => null,
            ];
        }

        // Check if user has enough diamonds
        $cost = config('diamonds.costs.forging');
        if ($user->diamonds_balance < $cost) {
            return [
                'can_forge' => false,
                'reason' => "You need at least {$cost} Diamonds to forge a new card. You currently have " . $user->diamonds_balance . '.',
                'cooldown_ends' => null,
            ];
        }

        // Check if the user has 3 or more cards in their inventory from the same template
        $templateCardCount = $user->digitalCards()
            ->where('template_id', $template->id)
            ->count();

        if ($templateCardCount >= 3) {
            return [
                'can_forge' => false,
                'reason' => 'You already have 3 Digital Cards from this template in your inventory. You must lose or transfer some first.',
                'cooldown_ends' => null,
            ];
        }

        return [
            'can_forge' => true,
            'reason' => null,
            'cooldown_ends' => null,
        ];
    }

    /**
     * Forge a new digital card from a template.
     */
    public function forge(User $user, Template $template): DigitalCard
    {
        $forgeCheck = $this->canForge($user, $template);
        if (!$forgeCheck['can_forge']) {
            throw new \Exception($forgeCheck['reason']);
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($user, $template) {
            // Deduct diamonds
            $cost = config('diamonds.costs.forging');
            $user->deductDiamonds($cost, 'system', "Forged new card from template: {$template->card_title}");

            return DigitalCard::create([
                'template_id' => $template->id,
                'owner_id' => $user->id,
                'original_owner_id' => $user->id,
                'serial_number' => $template->next_serial_number,
                'wins' => 0,
                'losses' => 0,
                'is_trophy' => false,
                'forged_at' => now(),
            ]);
        });
    }
}
