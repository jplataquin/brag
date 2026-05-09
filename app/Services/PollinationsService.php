<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PollinationsService
{
    /**
     * Generate an artistic game art image using Pollinations AI.
     *
     * @param string $prompt
     * @param string|null $originalPhotoPath
     * @return string The path to the generated AI photo
     */
    public function generateImage(string $prompt, ?string $originalPhotoPath = null): string
    {
        Log::info("Pollinations AI generation requested", ['prompt' => $prompt]);

        $apiKey = config('services.pollinations.key');
        
        // Constructing a detailed prompt for better game art results
        $fullPrompt = "highly detailed video game character portrait, " . $prompt . ", digital art, vibrant neon colors, masterpiece 8k";

        try {
            $seed = rand(1, 999999);
            $baseUrl = "https://image.pollinations.ai/prompt/" . urlencode($fullPrompt);
            
            // Build query parameters
            $params = [
                'width' => 500,
                'height' => 700,
                'nologo' => 'true',
                'seed' => $seed,
                'model' => 'flux', // Defaulting to Flux for better quality
            ];

            // If an API key is provided, we use it to bypass rate limits.
            // Pollinations usually handles keys via Authorization header or a specific param.
            // For the purpose of this implementation, we will pass it as a Bearer token if present.
            $request = Http::withoutVerifying()->timeout(60);

            if (!empty($apiKey)) {
                $request = $request->withToken($apiKey);
            }

            $response = $request->get($baseUrl, $params);

            if ($response->successful()) {
                $filename = 'templates/ai_' . Str::random(16) . '.jpg';
                Storage::disk('public')->put($filename, $response->body());
                Storage::disk('public')->setVisibility($filename, 'public');
                
                Log::info("Pollinations AI generation successful", ['path' => $filename]);
                return $filename;
            } else {
                Log::error("Pollinations AI failed. Status: " . $response->status() . " Body: " . $response->body());
                throw new \Exception("AI service returned an error status: " . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Pollinations AI Error: " . $e->getMessage());
            throw new \Exception("Failed to generate AI image: " . $e->getMessage());
        }
    }
}
