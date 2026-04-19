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

        // Check max 3 own cards
        $ownCardCount = $user->digitalCards()
            ->where('original_owner_id', $user->id)
            ->where('is_trophy', false)
            ->count();

        if ($ownCardCount >= 3) {
            return [
                'can_forge' => false,
                'reason' => 'You already have 3 of your own Digital Cards in your inventory. Surrender or trade some first.',
                'cooldown_ends' => null,
            ];
        }

        // Check cooldown (3 days from last forge of this template)
        $lastForge = DigitalCard::where('template_id', $template->id)
            ->where('original_owner_id', $user->id)
            ->orderBy('forged_at', 'desc')
            ->first();

        if ($lastForge) {
            $cooldownEnds = $lastForge->forged_at->addDays(3);
            if (now()->lt($cooldownEnds)) {
                return [
                    'can_forge' => false,
                    'reason' => 'Forge cooldown active. You can forge again after ' . $cooldownEnds->diffForHumans(),
                    'cooldown_ends' => $cooldownEnds,
                ];
            }
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
    }
}
