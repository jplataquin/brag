<?php

namespace App\Events;

use App\Models\TeamBattle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamBattleUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $teamBattle;
    public $message;
    public $type;

    /**
     * Create a new event instance.
     */
    public function __construct(TeamBattle $teamBattle, string $message = '', string $type = 'update')
    {
        $this->teamBattle = $teamBattle;
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
            new Channel('team-battle.' . $this->teamBattle->id),
        ];
    }

    /**
     * Data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'team_battle_id' => $this->teamBattle->id,
            'status' => $this->teamBattle->status,
            'message' => $this->message,
            'type' => $this->type,
        ];
    }
}
