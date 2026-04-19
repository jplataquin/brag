<?php

namespace App\Console\Commands;

use App\Models\Template;
use App\Models\DigitalCard;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResetForgeCooldown extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forge:reset {template : The ID of the template}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the 3-day forge cooldown for a specific template';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $templateId = $this->argument('template');
        $template = Template::find($templateId);

        if (!$template) {
            $this->error("Template with ID {$templateId} not found.");
            return Command::FAILURE;
        }

        // Find the most recently forged card from this template by the creator
        $lastForge = DigitalCard::where('template_id', $template->id)
            ->where('original_owner_id', $template->user_id)
            ->orderBy('forged_at', 'desc')
            ->first();

        if (!$lastForge) {
            $this->info("No cards have been forged from Template #{$template->id} yet. There is no cooldown to reset.");
            return Command::SUCCESS;
        }

        // Check if it's actually in cooldown
        $cooldownEnds = Carbon::parse($lastForge->forged_at)->addDays(3);
        if (now()->greaterThanOrEqualTo($cooldownEnds)) {
            $this->info("Template #{$template->id} is not currently in cooldown (cooldown ended " . $cooldownEnds->diffForHumans() . ").");
            return Command::SUCCESS;
        }

        // Reset the forged_at timestamp to 4 days ago to bypass the 3-day cooldown
        $lastForge->update([
            'forged_at' => Carbon::now()->subDays(4)
        ]);

        $this->info("🔥 Forge cooldown reset successfully for Template #{$template->id} ({$template->card_title}).");
        $this->info("You can now forge a new card from this template immediately.");

        return Command::SUCCESS;
    }
}
