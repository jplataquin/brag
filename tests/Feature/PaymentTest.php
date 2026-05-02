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

        $response = $this->post(route('payments.store'), [
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

    public function test_user_can_initiate_checkout_with_payment_methods()
    {
        config(['hitpay.payment_methods' => [
            'card' => true, 
            'gcash' => true, 
            'paymaya' => true,
            'grabpay' => false // ensure false values are filtered out
        ]]);

        Http::fake([
            'api.sandbox.hit-pay.com/*' => function ($request) {
                // Verify that payment_methods[] are in the request
                // In asForm, it looks like payment_methods[0]=card&payment_methods[1]=gcash...
                $data = $request->data();
                return isset($data['payment_methods']) && 
                       array_values($data['payment_methods']) === ['card', 'gcash', 'paymaya']
                    ? Http::response(['id' => 'hitpay_req_123', 'url' => 'https://sandbox.hit-pay.com/checkout/123'], 200)
                    : Http::response(['error' => 'Missing payment methods'], 400);
            }
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('payments.store'), [
            'package_id' => 'package_10'
        ]);

        $response->assertRedirect('https://sandbox.hit-pay.com/checkout/123');
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
        config(['hitpay.salt' => 'test_salt']);
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

    public function test_payment_callback_redirects_to_success_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('payments.callback', [
            'reference' => 'TEST-REF-123',
            'status' => 'completed'
        ]));

        $response->assertRedirect(route('payments.success', ['reference' => 'TEST-REF-123']));
    }

    public function test_success_page_shows_payment_details()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payment = Payment::create([
            'user_id' => $user->id,
            'reference' => 'SUCCESS-REF-456',
            'hitpay_id' => 'a19ceec1-44a0-4546-9a66-4838a3b183fe',
            'amount' => 100,
            'currency' => 'PHP',
            'shards_amount' => 25,
            'status' => 'completed'
        ]);

        // Test with internal reference
        $response1 = $this->get(route('payments.success', ['reference' => 'SUCCESS-REF-456']));
        $response1->assertStatus(200);
        $response1->assertSee('SUCCESS-REF-456');

        // Test with HitPay ID reference (which is what HitPay sends back)
        $response2 = $this->get(route('payments.success', ['reference' => 'a19ceec1-44a0-4546-9a66-4838a3b183fe']));
        $response2->assertStatus(200);
        $response2->assertSee('SUCCESS-REF-456'); // The view should still display our internal reference
    }
}
