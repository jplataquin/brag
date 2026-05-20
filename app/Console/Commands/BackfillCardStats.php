<?php

namespace App\Console\Commands;

use App\Models\DigitalCard;
use Illuminate\Console\Command;

class BackfillCardStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cards:backfill-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and store win_rate and integrity_stat for all existing digital cards.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cards = DigitalCard::all();
        $count = $cards->count();

        $this->info("Starting backfill for {$count} cards...");
        $bar = $this->output->createProgressBar($count);

        foreach ($cards as $card) {
            $card->updateLeaderboardStats();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Backfill completed successfully.");
    }
}
