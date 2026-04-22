<?php

namespace App\Console\Commands;

use App\Models\Template;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ConvertImagesToWebp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:convert-images-to-webp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert existing user avatars and template photos to WebP format and update the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!function_exists('imagewebp')) {
            $this->error('The GD library with WebP support is not available on this server.');
            return;
        }

        $this->info('Starting image conversion to WebP...');

        // 1. Process User Avatars
        $users = User::whereNotNull('avatar')->get();
        $this->info("Found {$users->count()} users with avatars.");
        $userCount = 0;

        foreach ($users as $user) {
            $newPath = $this->convertToWebp($user->avatar, 500, 500); // Avatars don't need to be huge
            if ($newPath && $newPath !== $user->avatar) {
                $user->avatar = $newPath;
                $user->save();
                $userCount++;
            }
        }
        $this->info("Converted {$userCount} user avatars.");

        // 2. Process Template Photos
        $templates = Template::whereNotNull('photo')->orWhereNotNull('ai_photo')->get();
        $this->info("Found {$templates->count()} templates with photos.");
        $templateCount = 0;

        foreach ($templates as $template) {
            $updated = false;

            // Main Photo
            if ($template->photo) {
                $newPath = $this->convertToWebp($template->photo, 900, 560);
                if ($newPath && $newPath !== $template->photo) {
                    $template->photo = $newPath;
                    $updated = true;
                }
            }

            // AI Photo
            if ($template->ai_photo) {
                // For AI photos, they are 500x700 vertically, so we adjust max dimensions accordingly
                $newPath = $this->convertToWebp($template->ai_photo, 700, 700); 
                if ($newPath && $newPath !== $template->ai_photo) {
                    $template->ai_photo = $newPath;
                    $updated = true;
                }
            }

            if ($updated) {
                $template->save();
                $templateCount++;
            }
        }
        $this->info("Converted photos for {$templateCount} templates.");

        $this->info('WebP conversion completed successfully!');
    }

    /**
     * Convert an image to WebP format, optionally scaling it down.
     *
     * @param string $path The relative path in the public disk
     * @param int $maxW Maximum width
     * @param int $maxH Maximum height
     * @return string|null The new path, or the original path if already WebP or conversion failed.
     */
    private function convertToWebp($path, $maxW = 900, $maxH = 900)
    {
        if (!Storage::disk('public')->exists($path)) {
            return $path;
        }

        $fullPath = Storage::disk('public')->path($path);
        $mimeType = mime_content_type($fullPath);

        // If it's already WebP, just return the path
        if ($mimeType === 'image/webp') {
            return $path;
        }

        // Only process supported image types
        if (strpos($mimeType, 'image/') !== 0 || !in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif'])) {
            return $path;
        }

        $image = null;
        switch ($mimeType) {
            case 'image/jpeg':
                $image = @imagecreatefromjpeg($fullPath);
                break;
            case 'image/png':
                $image = @imagecreatefrompng($fullPath);
                if ($image) {
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                }
                break;
            case 'image/gif':
                $image = @imagecreatefromgif($fullPath);
                if ($image) {
                    imagepalettetotruecolor($image);
                }
                break;
        }

        if ($image) {
            $width = imagesx($image);
            $height = imagesy($image);

            // Scale down if necessary
            if ($width > $maxW || $height > $maxH) {
                $ratio = min($maxW / $width, $maxH / $height);
                $newWidth = (int) ($width * $ratio);
                $newHeight = (int) ($height * $ratio);

                $scaledImage = imagecreatetruecolor($newWidth, $newHeight);
                
                if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
                    imagealphablending($scaledImage, false);
                    imagesavealpha($scaledImage, true);
                    $transparent = imagecolorallocatealpha($scaledImage, 255, 255, 255, 127);
                    imagefilledrectangle($scaledImage, 0, 0, $newWidth, $newHeight, $transparent);
                }

                imagecopyresampled($scaledImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $scaledImage;
            }

            $pathInfo = pathinfo($path);
            $newPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_' . uniqid() . '.webp';
            $newFullPath = Storage::disk('public')->path($newPath);

            if (@imagewebp($image, $newFullPath, 85)) {
                imagedestroy($image);
                
                // Set correct permissions
                Storage::disk('public')->setVisibility($newPath, 'public');
                
                // Delete the old file
                Storage::disk('public')->delete($path);
                
                return $newPath;
            } else {
                imagedestroy($image);
            }
        }

        return $path;
    }
}
