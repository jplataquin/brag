<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixStoragePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:fix-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes file and directory permissions in storage/app/public to resolve 403 Forbidden errors';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = storage_path('app/public');

        if (!File::exists($path)) {
            $this->error("The path does not exist: {$path}");
            return Command::FAILURE;
        }

        $this->info("Scanning {$path}...");

        // Gather all directories and files recursively
        $directories = File::allDirectories($path);
        $files = File::allFiles($path);

        $this->info('Fixing directory permissions (0775)...');
        $this->withProgressBar($directories, function ($dir) {
            // Suppress warnings in case the CLI user doesn't own the directory
            @chmod($dir, 0775);
        });
        $this->newLine(2);

        $this->info('Fixing file permissions (0664)...');
        $this->withProgressBar($files, function ($file) {
            // Suppress warnings in case the CLI user doesn't own the file
            @chmod($file->getPathname(), 0664);
        });
        $this->newLine(2);

        $this->info("Permissions successfully updated for " . count($directories) . " directories and " . count($files) . " files.");
        
        $this->warn("Note: If 403 errors persist, you may still need to fix the file ownership by running:");
        $this->line("sudo chown -R www-data:www-data storage/");

        return Command::SUCCESS;
    }
}
