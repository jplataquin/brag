<?php

namespace App\Console\Commands;

use App\Models\Battle;
use App\Models\BattleActivity;
use App\Events\BattleUpdated;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AutoCancelBattles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-cancel-battles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically cancel inactive battles (Active: 3-hours, Ready: 15 mins, Pending: 15 mins)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fifteenMinutesAgo = Carbon::now()->subMinutes(15);
        $threeHoursAgo = Carbon::now()->subHours(3);

        // 1. Handle pending/ready battles inactive for 15 minutes
        $staleRooms = Battle::whereIn('status', ['pending', 'ready'])
            ->where('updated_at', '<=', $fifteenMinutesAgo)
            ->get();

        foreach ($staleRooms as $battle) {
            DB::transaction(function () use ($battle) {
                $battle->update([
                    'status' => 'cancelled',
                    'team_a_cancel_flag' => false,
                    'team_b_cancel_flag' => false,
                    'marshall_cancel_flag' => false,
                ]);

                BattleActivity::create([
                    'battle_id' => $battle->id,
                    'user_id' => null,
                    'type' => 'cancel',
                    'message' => 'Battle cancelled automatically due to inactivity in pending/ready state for 15 minutes.',
                ]);

                event(new BattleUpdated($battle, "Battle cancelled due to inactivity.", 'cancel'));
            });

            $this->info("Cancelled battle ID (Pending/Ready timeout): {$battle->id}");
        }

        // 2. Handle active battles inactive for 3 hours
        $activeStaleBattles = Battle::where('status', 'active')
            ->where('updated_at', '<=', $threeHoursAgo)
            ->get();

        foreach ($activeStaleBattles as $battle) {
            DB::transaction(function () use ($battle) {
                $battle->update([
                    'status' => 'cancelled',
                    'team_a_cancel_flag' => false,
                    'team_b_cancel_flag' => false,
                    'marshall_cancel_flag' => false,
                ]);

                BattleActivity::create([
                    'battle_id' => $battle->id,
                    'user_id' => null,
                    'type' => 'cancel',
                    'message' => 'Battle cancelled automatically due to no activity for 3 hours.',
                ]);

                event(new BattleUpdated($battle, "Battle cancelled due to 3-hour inactivity timeout.", 'cancel'));
            });

            $this->info("Cancelled battle ID (Active timeout): {$battle->id}");
        }

        return Command::SUCCESS;
    }
}
