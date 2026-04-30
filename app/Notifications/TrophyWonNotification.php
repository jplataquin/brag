<?php

namespace App\Notifications;

use App\Models\DigitalCard;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class TrophyWonNotification extends Notification
{
    use Queueable;

    protected $trophyCard;
    protected $message;
    protected $actionUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct(DigitalCard $trophyCard)
    {
        $this->trophyCard = $trophyCard;
        
        $cardName = $trophyCard->template->name ?? 'a card';
        $this->message = "You won {$cardName} as a trophy in battle!";
        $this->actionUrl = route('cards.show', $trophyCard);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'card_id' => $this->trophyCard->id,
            'message' => $this->message,
            'type' => 'trophy_won',
            'action_url' => $this->actionUrl,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'card_id' => $this->trophyCard->id,
            'message' => $this->message,
            'type' => 'trophy_won',
            'action_url' => $this->actionUrl,
            'created_at' => now()->diffForHumans(),
        ]);
    }
}
