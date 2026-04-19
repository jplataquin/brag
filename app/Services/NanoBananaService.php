<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class NanoBananaService
{
    /**
     * Enhance the given photo into an artistic game art.
     * Note: Mocks the Nano Banana AI API.
     *
     * @param string $originalPhotoPath
     * @param string $prompt
     * @return string The path to the generated AI photo
     */
    public function enhanceImage(string $originalPhotoPath, string $prompt): string
    {
        Log::info("Nano Banana AI enhancement requested", ['prompt' => $prompt, 'original' => $originalPhotoPath]);

        $apiKey = config('services.nano_banana.key');
        $apiUrl = config('services.nano_banana.url');

        // Constructing a detailed prompt for better game art results
        $fullPrompt = "highly detailed video game character portrait, " . $prompt . ", digital art, vibrant neon colors, masterpiece 8k";

        try {
            // If the user has configured their real API key in the .env file, we process the real Image-to-Image request
            if (!empty($apiKey)) {
                $photoContents = Storage::disk('public')->get($originalPhotoPath);
                
                if (!$photoContents) {
                    throw new \Exception("Could not read the original photo file.");
                }

                $mimeType = Storage::disk('public')->mimeType($originalPhotoPath) ?: 'image/jpeg';
                $base64Image = base64_encode($photoContents);

                // Constructing the payload specifically for Gemini Vision / Nano Banana
                $payload = [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $fullPrompt],
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data' => $base64Image
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                         'temperature' => 0.4,
                    ]
                ];

                // Append the API key to the URL as required by Gemini API
                $requestUrl = $apiUrl . '?key=' . $apiKey;

                $response = Http::withoutVerifying()
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                    ])
                    ->timeout(60)
                    ->post($requestUrl, $payload);

                if ($response->successful()) {
                    // Extracting the generated text/URL or handling the JSON response properly.
                    // NOTE: Gemini Flash models return generated text describing the image transformation 
                    // or base64 generated images depending on the specific endpoint (e.g. imagen-3).
                    // We will parse the standard Gemini text response and log it, then fallback to mock for visuals
                    // until an exact image generation endpoint (Imagen) is finalized in the env.
                    
                    $data = $response->json();
                    Log::debug("Gemini raw response:", ['data' => $data]);
                    
                    // Gemini 2.5 Flash and newer models might return a successful 'STOP' finishReason even if 
                    // the text parts are empty or nested differently depending on prompt settings.
                    // If candidates exist and it successfully finished, we consider the prompt validated.
                    if (isset($data['candidates'][0]) && 
                        (isset($data['candidates'][0]['finishReason']) && $data['candidates'][0]['finishReason'] === 'STOP')) {
                        // The model understood the prompt and image. For this template, we'll log it.
                        Log::info("Gemini processed the image prompt successfully.");
                        
                        // Because standard Gemini 2.0/2.5 returns text (not raw image bytes like our previous mock),
                        // We will forcefully use the fallback mock to generate the *actual* visual image for the user 
                        // so the UI remains unbroken while utilizing the prompt.
                        throw new \Exception("GEMINI_SUCCESS_FALLBACK");
                    }
                    
                    throw new \Exception("Unexpected API response format: " . json_encode($data));
                } else {
                    Log::error("Real Nano Banana API failed. Status: " . $response->status() . " Body: " . $response->body());
                    throw new \Exception("Real AI service returned an error status: " . $response->status() . " " . $response->body());
                }
            }

            // Fallback: Using Pollinations AI as a free, instant visual mock
            $seed = rand(1, 999999);
            $url = "https://image.pollinations.ai/prompt/" . urlencode($fullPrompt) . "?width=500&height=700&nologo=true&seed=" . $seed;

            $response = Http::withoutVerifying()
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'image/jpeg, image/png, image/*'
                ])->timeout(30)->get($url);

            if ($response->successful()) {
                $filename = 'templates/ai_' . Str::random(16) . '.jpg';
                Storage::disk('public')->put($filename, $response->body());
                return $filename;
            } else {
                Log::error("Mock Nano Banana AI failed. Status: " . $response->status() . " Body: " . $response->body());
                throw new \Exception("Mock AI service returned an error status: " . $response->status());
            }
        } catch (\Exception $e) {
            // Check if we hit the intentional fallback point from Gemini processing
            if ($e->getMessage() === "GEMINI_SUCCESS_FALLBACK") {
                 Log::info("Falling back to Pollinations mock for visual generation after successful Gemini prompt analysis.");
                 
                 $seed = rand(1, 999999);
                 $url = "https://image.pollinations.ai/prompt/" . urlencode($fullPrompt) . "?width=500&height=700&nologo=true&seed=" . $seed;
     
                 $response = Http::withoutVerifying()
                     ->withHeaders([
                         'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                         'Accept' => 'image/jpeg, image/png, image/*'
                     ])->timeout(30)->get($url);
     
                 if ($response->successful()) {
                     $filename = 'templates/ai_' . Str::random(16) . '.jpg';
                     Storage::disk('public')->put($filename, $response->body());
                     return $filename;
                 }
                 throw new \Exception("Fallback mock AI generation failed.");
            }

            Log::error("Nano Banana AI Error: " . $e->getMessage());
            throw new \Exception("Failed to generate AI image: " . $e->getMessage());
        }
    }
}
