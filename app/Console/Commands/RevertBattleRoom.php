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

        if ($battle->status === 'pending' && is_null($battle->team_b_user_1)) {
            $this->info("Battle room [{$roomId}] is already in a pending state without a Team B.");
            return Command::SUCCESS;
        }

        $updateData = [
            'status' => 'pending',
            'team_b_ready' => false,
            'marshall_id' => null,
            'winner_team' => null,
        ];
        
        for ($i = 1; $i <= 6; $i++) {
            $updateData["team_b_user_{$i}"] = null;
            $updateData["team_a_card_{$i}"] = null;
            $updateData["team_b_card_{$i}"] = null;
        }

        $battle->update($updateData);

        $this->info("Battle room [{$roomId}] has been successfully reverted to pending status.");

        return Command::SUCCESS;
    }
}
