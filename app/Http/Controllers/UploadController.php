<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Handle chunked file uploads.
     */
    public function uploadChunk(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'file_id' => 'required|string',
            'chunk_index' => 'required|integer',
            'total_chunks' => 'required|integer',
            'extension' => 'required|string',
        ]);

        $file = $request->file('file');
        $fileId = $request->input('file_id');
        $chunkIndex = (int) $request->input('chunk_index');
        $totalChunks = (int) $request->input('total_chunks');
        $extension = $request->input('extension');

        $tempDirPath = 'tmp/chunks/' . $fileId;
        $finalDirPath = 'tmp/uploads';
        
        // Ensure directories exist
        if (!Storage::disk('public')->exists($tempDirPath)) {
            Storage::disk('public')->makeDirectory($tempDirPath);
        }
        if (!Storage::disk('public')->exists($finalDirPath)) {
            Storage::disk('public')->makeDirectory($finalDirPath);
        }

        // Store current chunk
        $chunkPath = $tempDirPath . '/' . $chunkIndex . '.part';
        file_put_contents(Storage::disk('public')->path($chunkPath), file_get_contents($file->path()));

        // Check if this is the last chunk
        if ($chunkIndex == $totalChunks - 1) {
            // Verify all chunks are present
            for ($i = 0; $i < $totalChunks; $i++) {
                if (!Storage::disk('public')->exists($tempDirPath . '/' . $i . '.part')) {
                    return response()->json(['error' => 'Missing chunks'], 400);
                }
            }

            // Merge chunks
            $finalFilename = $fileId . '.' . $extension;
            $finalFilePath = $finalDirPath . '/' . $finalFilename;
            $finalFullPath = Storage::disk('public')->path($finalFilePath);
            
            $outputFile = fopen($finalFullPath, 'wb');
            for ($i = 0; $i < $totalChunks; $i++) {
                $partPath = Storage::disk('public')->path($tempDirPath . '/' . $i . '.part');
                $inputFile = fopen($partPath, 'rb');
                stream_copy_to_stream($inputFile, $outputFile);
                fclose($inputFile);
            }
            fclose($outputFile);

            // Convert to WebP if it's a supported image and GD is available
            $mimeType = mime_content_type($finalFullPath);
            if (function_exists('imagewebp') && strpos($mimeType, 'image/') === 0 && $mimeType !== 'image/webp') {
                $image = null;
                switch ($mimeType) {
                    case 'image/jpeg':
                        $image = @imagecreatefromjpeg($finalFullPath);
                        break;
                    case 'image/png':
                        $image = @imagecreatefrompng($finalFullPath);
                        if ($image) {
                            imagepalettetotruecolor($image);
                            imagealphablending($image, true);
                            imagesavealpha($image, true);
                        }
                        break;
                    case 'image/gif':
                        $image = @imagecreatefromgif($finalFullPath);
                        if ($image) {
                            imagepalettetotruecolor($image);
                        }
                        break;
                }

                if ($image) {
                    $width = imagesx($image);
                    $height = imagesy($image);
                    $maxW = 900;
                    $maxH = 560;

                    // Calculate scaling factor to fit within 900x560 while preserving ratio
                    if ($width > $maxW || $height > $maxH) {
                        $ratio = min($maxW / $width, $maxH / $height);
                        $newWidth = (int) ($width * $ratio);
                        $newHeight = (int) ($height * $ratio);

                        $scaledImage = imagecreatetruecolor($newWidth, $newHeight);
                        
                        // Handle transparency for PNG/GIF
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

                    $webpFilename = $fileId . '.webp';
                    $webpFilePath = $finalDirPath . '/' . $webpFilename;
                    $webpFullPath = Storage::disk('public')->path($webpFilePath);

                    // 85% quality for a great balance of size and clarity
                    if (@imagewebp($image, $webpFullPath, 85)) {
                        imagedestroy($image);
                        @unlink($finalFullPath); // Delete the original file
                        $finalFilePath = $webpFilePath; // Update path to the new WebP
                    } else {
                        imagedestroy($image);
                    }
                }
            }

            // Ensure the file has public read permissions (fixes 403 Forbidden)
            Storage::disk('public')->setVisibility($finalFilePath, 'public');

            // Cleanup chunks directory
            Storage::disk('public')->deleteDirectory($tempDirPath);

            return response()->json([
                'success' => true,
                'path' => $finalFilePath,
                'url' => asset('storage/' . $finalFilePath)
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Chunk uploaded']);
    }
}
