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
     * @param string $artStyle
     * @param string|null $originalPhotoPath
     * @return string The path to the generated AI photo
     */
    public function generateImage(string $prompt, string $artStyle = 'neon', ?string $originalPhotoPath = null): string
    {
        Log::info("Pollinations AI generation requested", ['prompt' => $prompt, 'style' => $artStyle]);

        $apiKey = config('services.pollinations.key');
        
        // Constructing a detailed prompt based on the selected art style
        // Injecting framing keywords to ensure lots of background space for zooming/panning
        $framingKeywords = "wide angle environmental portrait, full body shot, expansive background, ";
        $fullPrompt = $prompt;

        switch ($artStyle) {
            case 'neon':
                $fullPrompt = $framingKeywords . "highly detailed video game character, " . $prompt . ", digital art, vibrant neon colors, masterpiece 8k";
                break;
            case 'anime':
                $fullPrompt = $framingKeywords . "90s Anime, Hand Drawn, high quality anime character, vibrant cell shading, " . $prompt . ", masterpiece 8k";
                break;
            case 'fantasy':
                $fullPrompt = $framingKeywords . "grimdark fantasy concept art, high quality RPG character, moody lighting, " . $prompt . ", masterpiece 8k";
                break;
            case 'raw':
            default:
                $fullPrompt = "full body, eye level angle, expansive background, " . $prompt;
                break;
        }

        try {
            $seed = rand(1, 999999);
            $baseUrl = "https://image.pollinations.ai/prompt/" . urlencode($fullPrompt);
            
            // Build query parameters with larger dimensions to support zooming out
            $params = [
                'width' => 1400,
                'height' => 1000,
                'nologo' => 'true',
                'seed' => $seed,
                'model' => 'flux', // Defaulting to Flux for better quality
            ];

            Log::info("Pollinations AI: Sending request", [
                'url' => $baseUrl,
                'params' => $params,
                'timeout' => 120
            ]);

            // If an API key is provided, we use it to bypass rate limits.
            // Pollinations usually handles keys via Authorization header or a specific param.
            // For the purpose of this implementation, we will pass it as a Bearer token if present.
            $request = Http::withoutVerifying()->timeout(240);

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
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Pollinations AI Timeout/Connection Error: " . $e->getMessage(), [
                'prompt' => $fullPrompt,
                'duration' => 'Request exceeded 120 seconds'
            ]);
            throw new \Exception("The AI generation service timed out. Please try a simpler prompt or try again later.");
        } catch (\Exception $e) {
            Log::error("Pollinations AI Error: " . $e->getMessage());
            throw new \Exception("Failed to generate AI image: " . $e->getMessage());
        }
    }
}
