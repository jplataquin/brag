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
    protected $description = 'Automatically cancel battles with unresponsive cancellation requests after 5 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fiveMinutesAgo = Carbon::now()->subMinutes(5);
        $oneHourAgo = Carbon::now()->subHour();

        // 1. Handle unanswered cancellation requests (5 mins)
        $cancellationBattles = Battle::whereIn('status', ['active', 'ready', 'failed'])
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('team_a_cancel_flag', true)->where('team_b_cancel_flag', false);
                })->orWhere(function ($q) {
                    $q->where('team_b_cancel_flag', true)->where('team_a_cancel_flag', false);
                });
            })
            ->where('updated_at', '<=', $fiveMinutesAgo)
            ->get();

        foreach ($cancellationBattles as $battle) {
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
                    'message' => 'Battle cancelled automatically due to no response to the cancellation request for more than 5 minutes.',
                ]);

                event(new BattleUpdated($battle, "Battle cancelled due to no response.", 'cancel'));
            });

            $this->info("Cancelled battle ID (No response): {$battle->id}");
        }

        // 2. Handle failed battles without Marshall resolution (1 hour)
        $failedBattles = Battle::where('status', 'failed')
            ->where('updated_at', '<=', $oneHourAgo)
            ->get();

        foreach ($failedBattles as $battle) {
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
                    'message' => 'Battle cancelled automatically. The Marshall did not resolve the conflict within 1 hour.',
                ]);

                event(new BattleUpdated($battle, "Battle cancelled automatically due to Marshall inactivity.", 'cancel'));
            });

            $this->info("Cancelled battle ID (Marshall timeout): {$battle->id}");
        }

        return Command::SUCCESS;
    }
}
