<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GrantDiamonds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:grant-diamonds {amount : The amount of diamonds to grant} {user=* : The User ID or "*" for everyone} {--remarks= : Custom remarks for the ledger entry}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant diamonds to a specific user or all users as a system entry.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $amount = $this->argument('amount');
        $userTarget = $this->argument('user');
        $customRemarks = $this->option('remarks');

        if (!is_numeric($amount) || $amount <= 0) {
            $this->error('The amount must be a positive number.');
            return Command::FAILURE;
        }

        $remarks = $customRemarks ?: "System reward: " . number_format($amount, 2) . " diamonds granted.";

        if ($userTarget === '*') {
            $users = User::all();
            $count = $users->count();
            
            if ($count === 0) {
                $this->warn('No users found to grant diamonds to.');
                return Command::SUCCESS;
            }

            $this->info("Granting {$amount} diamonds to ALL users ({$count} total)...");
            $this->withProgressBar($users, function ($user) use ($amount, $remarks) {
                $user->addDiamonds($amount, 'system', $remarks);
            });
            $this->newLine();
            $this->info("Successfully granted diamonds to all users.");
        } else {
            $user = User::find($userTarget);
            
            if (!$user) {
                $this->error("User with ID [{$userTarget}] not found.");
                return Command::FAILURE;
            }

            $user->addDiamonds($amount, 'system', $remarks);
            $this->info("Successfully granted {$amount} diamonds to user: {$user->username} (ID: {$user->id}).");
        }

        return Command::SUCCESS;
    }
}
