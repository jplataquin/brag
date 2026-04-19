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
