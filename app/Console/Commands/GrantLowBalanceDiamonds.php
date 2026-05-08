<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GrantLowBalanceDiamonds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:grant-low-balance-diamonds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically add 10 diamonds to users with a balance under 5 (runs 15th and end of month)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting low balance diamond grant process...');
        $count = 0;

        User::chunk(100, function ($users) use (&$count) {
            foreach ($users as $user) {
                // Check computed balance
                if ($user->diamonds_balance < 5) {
                    $user->addDiamonds(10, 'system_grant', 'Low balance system grant (15th/End of Month)');
                    $count++;
                    $this->comment("Granted 10 diamonds to user: {$user->username} (Current was: {$user->diamonds_balance})");
                }
            }
        });

        $this->info("Process completed. Granted 10 diamonds to {$count} users.");
    }
}
