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
    protected $description = 'Fixes file/directory permissions, creates required folders, and changes ownership to www-data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $publicPath = storage_path('app/public');

        $requiredDirs = [
            $publicPath . '/templates',
            $publicPath . '/tmp',
            $publicPath . '/tmp/chunks',
            $publicPath . '/tmp/uploads',
        ];

        $this->info('Checking required directories...');
        foreach ($requiredDirs as $dir) {
            if (!File::exists($dir)) {
                $this->comment("Creating directory: {$dir}");
                File::makeDirectory($dir, 0775, true);
            }
        }

        if (!File::exists($publicPath)) {
            $this->error("The path does not exist: {$publicPath}");
            return Command::FAILURE;
        }

        $this->info("Scanning {$publicPath}...");

        // Gather all directories and files recursively
        $directories = File::allDirectories($publicPath);
        // Include the root path itself
        array_unshift($directories, $publicPath);
        
        $files = File::allFiles($publicPath);

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

        $this->info("Permissions successfully updated.");
        
        $this->info("Attempting to change ownership to www-data:www-data...");
        $output = [];
        $returnVar = 0;
        
        // Execute chown on the entire public storage folder
        exec('chown -R www-data:www-data ' . escapeshellarg($publicPath) . ' 2>&1', $output, $returnVar);

        if ($returnVar !== 0) {
            $this->warn("Failed to change ownership automatically. This command must be run with sudo to change ownership.");
            $this->warn("Error Output: " . implode("\n", $output));
            $this->line("Please run this command manually:");
            $this->line("sudo chown -R www-data:www-data storage/app/public");
        } else {
            $this->info("Successfully changed ownership to www-data:www-data.");
        }

        return Command::SUCCESS;
    }
}
