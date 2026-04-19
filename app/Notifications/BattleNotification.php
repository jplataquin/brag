<?php

namespace App\Notifications;

use App\Models\Battle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class BattleNotification extends Notification
{
    protected $battle;
    protected $message;
    protected $type;
    protected $actionUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct(Battle $battle, string $message, string $type, ?string $actionUrl = null)
    {
        $this->battle = $battle;
        $this->message = $message;
        $this->type = $type;
        $this->actionUrl = $actionUrl ?? route('battles.room', $battle);
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
            'battle_id' => $this->battle->id,
            'message' => $this->message,
            'type' => $this->type,
            'action_url' => $this->actionUrl,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'battle_id' => $this->battle->id,
            'message' => $this->message,
            'type' => $this->type,
            'action_url' => $this->actionUrl,
            'created_at' => now()->diffForHumans(),
        ]);
    }
}
