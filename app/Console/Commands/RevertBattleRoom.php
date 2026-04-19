<?php

namespace App\Console\Commands;

use App\Models\Battle;
use Illuminate\Console\Command;

class RevertBattleRoom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'battle:revert {roomId : The ID of the battle room to revert}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reverts a battle room back to pending status and removes the opponent';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roomId = $this->argument('roomId');

        $battle = Battle::where('room_id', $roomId)->first();

        if (!$battle) {
            $this->error("Battle room [{$roomId}] not found.");
            return Command::FAILURE;
        }

        if ($battle->status === 'pending' && is_null($battle->opponent_id)) {
            $this->info("Battle room [{$roomId}] is already in a pending state without an opponent.");
            return Command::SUCCESS;
        }

        $battle->update([
            'status' => 'pending',
            'opponent_id' => null,
            'opponent_card_id' => null,
            'challenger_cancel' => false,
            'opponent_cancel' => false,
            'challenger_cancel_timestamp' => null,
            'opponent_cancel_timestamp' => null,
            'challenger_declared_user_win' => null,
            'opponent_declared_user_win' => null,
            'adjudicator_declared_user_win' => null,
            'adjudicator_id' => null,
            'challenger_adjudicator_id' => null,
            'opponent_adjudicator_id' => null,
            'winner_id' => null,
        ]);

        $this->info("Battle room [{$roomId}] has been successfully reverted to pending status.");

        return Command::SUCCESS;
    }
}
