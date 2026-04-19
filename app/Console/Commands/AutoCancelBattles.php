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

        $battles = Battle::whereIn('status', ['active', 'ready'])
            ->where(function ($query) use ($fiveMinutesAgo) {
                $query->where(function ($q) use ($fiveMinutesAgo) {
                    $q->where('challenger_cancel', true)
                      ->where('opponent_cancel', false)
                      ->where('challenger_cancel_timestamp', '<=', $fiveMinutesAgo);
                })->orWhere(function ($q) use ($fiveMinutesAgo) {
                    $q->where('opponent_cancel', true)
                      ->where('challenger_cancel', false)
                      ->where('opponent_cancel_timestamp', '<=', $fiveMinutesAgo);
                });
            })
            ->get();

        foreach ($battles as $battle) {
            DB::transaction(function () use ($battle) {
                $battle->update([
                    'status' => 'cancelled',
                    'challenger_cancel' => false,
                    'opponent_cancel' => false,
                ]);

                BattleActivity::create([
                    'battle_id' => $battle->id,
                    'user_id' => null,
                    'type' => 'cancel',
                    'message' => 'Battle cancelled automatically due to no response to the cancellation request for more than 5 minutes.',
                ]);

                event(new BattleUpdated($battle, "Battle cancelled due to no response.", 'cancel'));
            });

            $this->info("Cancelled battle ID: {$battle->id}");
        }

        return Command::SUCCESS;
    }
}
