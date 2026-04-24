<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConvertBadgesToWebp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'badge:convert-webp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert badge icon PNGs to WebP format.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!function_exists('imagewebp')) {
            $this->error('The GD library with WebP support is not available on this server.');
            return Command::FAILURE;
        }

        $badgePath = public_path('img/badge');

        if (!File::isDirectory($badgePath)) {
            $this->error("Directory does not exist: {$badgePath}");
            return Command::FAILURE;
        }

        $files = File::files($badgePath);
        $convertedCount = 0;

        foreach ($files as $file) {
            if (strtolower($file->getExtension()) === 'png') {
                $filePath = $file->getPathname();
                $fileName = $file->getFilenameWithoutExtension();
                $newFilePath = $badgePath . DIRECTORY_SEPARATOR . $fileName . '.webp';

                // Skip if already exists
                if (File::exists($newFilePath)) {
                    $this->info("Skipping {$fileName}.webp (Already exists)");
                    continue;
                }

                $image = @imagecreatefrompng($filePath);
                if ($image) {
                    imagepalettetotruecolor($image);
                    imagealphablending($image, false);
                    imagesavealpha($image, true);

                    if (@imagewebp($image, $newFilePath, 90)) { // 90% quality
                        $this->info("Converted {$file->getFilename()} to {$fileName}.webp");
                        $convertedCount++;
                        // Optionally delete original PNG
                        // File::delete($filePath);
                    } else {
                        $this->error("Failed to convert {$file->getFilename()}");
                    }
                    imagedestroy($image);
                } else {
                    $this->error("Failed to read {$file->getFilename()}");
                }
            }
        }

        $this->info("Successfully converted {$convertedCount} badges to WebP.");
        return Command::SUCCESS;
    }
}
