<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-temp-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up temporary upload files and chunks older than 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of temporary files...');
        
        $now = Carbon::now();
        $hoursThreshold = 24;
        $filesDeleted = 0;
        $foldersDeleted = 0;

        $directories = [
            'tmp/uploads',
            'tmp/chunks'
        ];

        foreach ($directories as $dir) {
            if (!Storage::disk('public')->exists($dir)) {
                $this->line("Directory does not exist, skipping: $dir");
                continue;
            }

            // Cleanup files in the directory (specifically for tmp/uploads)
            $files = Storage::disk('public')->files($dir);
            foreach ($files as $file) {
                $lastModified = Carbon::createFromTimestamp(Storage::disk('public')->lastModified($file));
                if ($now->diffInHours($lastModified) >= $hoursThreshold) {
                    Storage::disk('public')->delete($file);
                    $filesDeleted++;
                }
            }

            // Cleanup subdirectories (specifically for tmp/chunks which uses unique file IDs)
            $subdirs = Storage::disk('public')->directories($dir);
            foreach ($subdirs as $subdir) {
                // Since directories don't always track 'last modified' reliably in all filesystems, 
                // we check if the directory is empty or if its contents are old.
                // For simplicity and safety, we check the directory's contents.
                $subdirFiles = Storage::disk('public')->allFiles($subdir);
                
                $shouldDelete = true;
                if (!empty($subdirFiles)) {
                    foreach ($subdirFiles as $sFile) {
                        $sLastModified = Carbon::createFromTimestamp(Storage::disk('public')->lastModified($sFile));
                        if ($now->diffInHours($sLastModified) < $hoursThreshold) {
                            $shouldDelete = false;
                            break;
                        }
                    }
                } else {
                    // Empty directory, check its own age if possible or just delete if it's old
                    // On some systems directory timestamps aren't updated on file deletion.
                    // We'll delete empty directories in tmp/chunks regardless if they are "old".
                }

                if ($shouldDelete) {
                    Storage::disk('public')->deleteDirectory($subdir);
                    $foldersDeleted++;
                }
            }
        }

        $this->info("Cleanup complete. Deleted $filesDeleted files and $foldersDeleted folders.");
    }
}
