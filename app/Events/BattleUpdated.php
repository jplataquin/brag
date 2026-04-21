<?php

namespace App\Events;

use App\Models\Battle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BattleUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $battle;
    public $message;
    public $type;

    /**
     * Create a new event instance.
     */
    public function __construct(Battle $battle, string $message = '', string $type = 'update')
    {
        $this->battle = $battle;
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('battle.' . $this->battle->room_id),
        ];
    }

    /**
     * Data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'battle_id' => $this->battle->id,
            'status' => $this->battle->status,
            'message' => $this->message,
            'type' => $this->type,
            'challenger_declared_user_win' => $this->battle->challenger_declared_user_win,
            'opponent_declared_user_win' => $this->battle->opponent_declared_user_win,
            'marshall_declared_user_win' => $this->battle->marshall_declared_user_win,
            'challenger_declared_name' => $this->battle->challenger_declared_user_win ? \App\Models\User::find($this->battle->challenger_declared_user_win)->username : null,
            'opponent_declared_name' => $this->battle->opponent_declared_user_win ? \App\Models\User::find($this->battle->opponent_declared_user_win)->username : null,
        ];
    }
}
