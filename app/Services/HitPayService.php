<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class HitPayService
{
    protected $apiKey;
    protected $salt;
    protected $env;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('hitpay.api_key');
        $this->salt = config('hitpay.salt');
        $this->env = config('hitpay.env', 'sandbox');
        
        $this->baseUrl = $this->env === 'production' 
            ? 'https://api.hit-pay.com/v1' 
            : 'https://api.sandbox.hit-pay.com/v1';
    }

    /**
     * Create a new payment request on HitPay
     */
    public function createPaymentRequest($amount, $currency, $reference, $email, $name, $redirectUrl, $webhookUrl, $paymentMethods = [])
    {
        $params = [
            'amount' => $amount,
            'currency' => $currency,
            'reference_number' => $reference,
            'email' => $email,
            'name' => $name,
            'redirect_url' => $redirectUrl,
            'webhook' => $webhookUrl,
        ];

        // If payment methods are provided, pass them as an array.
        // Laravel's Http client (Guzzle) will handle the conversion to payment_methods[] format.
        if (!empty($paymentMethods)) {
            $params['payment_methods'] = $paymentMethods;
        }

        $response = Http::withHeaders([
            'X-BUSINESS-API-KEY' => $this->apiKey,
        ])->asForm()->post($this->baseUrl . '/payment-requests', $params);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to create HitPay payment request: ' . $response->body());
    }

    /**
     * Get the status and details of a payment request.
     */
    public function getPaymentRequest($paymentRequestId)
    {
        $response = Http::withHeaders([
            'X-BUSINESS-API-KEY' => $this->apiKey,
        ])->get($this->baseUrl . '/payment-requests/' . $paymentRequestId);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Verify the HitPay webhook HMAC signature
     */
    public function verifySignature(array $payload): bool
    {
        if (!isset($payload['hmac'])) {
            return false;
        }

        $providedHmac = $payload['hmac'];
        unset($payload['hmac']); // Remove hmac before calculating

        // HitPay signature logic: sort by key, concatenate key and value, hash with salt
        ksort($payload);
        $baseString = '';
        foreach ($payload as $key => $val) {
            $baseString .= $key . $val;
        }

        $calculatedHmac = hash_hmac('sha256', $baseString, $this->salt);

        return hash_equals($calculatedHmac, $providedHmac);
    }
}
