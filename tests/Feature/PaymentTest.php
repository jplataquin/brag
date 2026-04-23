<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Laravel 11 CSRF middleware
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    public function test_user_can_initiate_checkout()
    {
        Http::fake([
            'api.sandbox.hit-pay.com/*' => Http::response([
                'id' => 'hitpay_req_123',
                'url' => 'https://sandbox.hit-pay.com/checkout/123'
            ], 200)
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('payments.checkout'), [
            'package_id' => 'package_10'
        ]);

        $response->assertRedirect('https://sandbox.hit-pay.com/checkout/123');

        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'hitpay_id' => 'hitpay_req_123',
            'shards_amount' => 10,
            'amount' => 50.00,
            'status' => 'pending'
        ]);
    }

    public function test_webhook_completes_payment_and_adds_shards()
    {
        $user = User::factory()->create();
        $payment = Payment::create([
            'user_id' => $user->id,
            'reference' => 'TEST-REF-123',
            'amount' => 50,
            'currency' => 'PHP',
            'shards_amount' => 10,
            'status' => 'pending'
        ]);

        // Manually simulate HitPay webhook payload
        $payload = [
            'reference_number' => 'TEST-REF-123',
            'status' => 'completed',
            'amount' => 50,
        ];

        // Generate valid HMAC using our test salt
        config(['services.hitpay.salt' => 'test_salt']);
        ksort($payload);
        $baseString = '';
        foreach ($payload as $key => $val) {
            $baseString .= $key . $val;
        }
        $payload['hmac'] = hash_hmac('sha256', $baseString, 'test_salt');

        // Webhook shouldn't require CSRF or Auth
        $response = $this->postJson(route('payments.webhook'), $payload);

        $response->assertStatus(200);

        $payment->refresh();
        $this->assertEquals('completed', $payment->status);

        $user->refresh();
        $this->assertEquals(10, $user->shards_balance);
    }
}
