<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Battle;
use App\Models\BattleActivity;
use App\Events\BattleUpdated;

class UpdateBattleStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'battle:status {id} {status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change the status of a specific battle room.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        $status = strtolower($this->argument('status'));

        $validStatuses = ['pending', 'ready', 'active', 'failed', 'completed', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            $this->error("Invalid status '{$status}'. Valid statuses are: " . implode(', ', $validStatuses));
            return self::FAILURE;
        }

        $battle = Battle::find($id);

        if (!$battle) {
            $this->error("Battle with ID {$id} not found.");
            return self::FAILURE;
        }

        $oldStatus = $battle->status;

        if ($oldStatus === $status) {
            $this->info("Battle {$id} is already in '{$status}' status.");
            return self::SUCCESS;
        }

        $battle->status = $status;
        
        $clearedData = false;
        if (in_array($status, ['pending', 'active'])) {
            $battle->team_a_declare_win = null;
            $battle->team_b_declare_win = null;
            $battle->marshall_declare_win = null;
            $battle->winner_team = null;
            $battle->team_a_card_data = null;
            $battle->team_b_card_data = null;
            $clearedData = true;
        }

        if ($status === 'pending') {
            $battle->marshall_id = null;
            $battle->team_a_marshall_elect = null;
            $battle->team_b_marshall_elect = null;
            $clearedData = true;
        }

        $battle->save();

        $logMessage = "System forcefully updated status from {$oldStatus} to {$status}.";
        if ($clearedData) {
            $logMessage .= " Result data and votes have been cleared.";
        }

        // Log the activity
        BattleActivity::create([
            'battle_id' => $battle->id,
            'user_id' => null, // System action
            'type' => 'system',
            'message' => $logMessage,
        ]);

        // Broadcast the update so real-time clients refresh
        event(new BattleUpdated($battle, "Battle status forcefully updated to {$status} by system.", 'update'));

        $this->info("Successfully updated Battle {$id} status from '{$oldStatus}' to '{$status}'.");
        return self::SUCCESS;
    }
}
