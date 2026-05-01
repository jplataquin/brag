<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GrantWelcomeDiamonds
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        $user = $event->user;

        // Check if the user has already received the welcome gift to prevent abuse
        $hasReceivedGift = $user->diamondTransactions()
            ->where('remarks', 'Welcome Gift')
            ->exists();

        if (!$hasReceivedGift) {
            $user->addDiamonds(10, 'system', 'Welcome Gift');
        }
    }
}
