<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Payment;
use App\Models\DiamondPackage;
use App\Models\ManualPaymentAgreement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
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
            '*hit-pay.com*' => Http::response([
                'id' => 'hitpay_req_123',
                'url' => 'https://sandbox.hit-pay.com/checkout/123'
            ], 200)
        ]);

        $user = User::factory()->create(['can_purchase_diamonds' => true]);
        $this->actingAs($user);

        $package = DiamondPackage::create([
            'name' => '10 Diamonds',
            'diamonds' => 10,
            'price' => 50.00,
            'currency' => 'PHP',
            'is_active' => true,
            'allow_manual' => true,
            'allow_hitpay' => true,
        ]);

        $response = $this->post(route('payments.store'), [
            'package_id' => $package->id,
            'payment_method' => 'hitpay'
        ]);

        $response->assertRedirect('https://sandbox.hit-pay.com/checkout/123');

        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'hitpay_id' => 'hitpay_req_123',
            'diamonds_amount' => 10,
            'amount' => 50.00,
            'status' => 'pending',
            'payment_method' => 'hitpay'
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
            '*hit-pay.com*' => function ($request) {
                // Verify that payment_methods[] are in the request
                // In asForm, it looks like payment_methods[0]=card&payment_methods[1]=gcash...
                $data = $request->data();
                return isset($data['payment_methods']) && 
                       array_values($data['payment_methods']) === ['card', 'gcash', 'paymaya']
                    ? Http::response(['id' => 'hitpay_req_123', 'url' => 'https://sandbox.hit-pay.com/checkout/123'], 200)
                    : Http::response(['error' => 'Missing payment methods'], 400);
            }
        ]);

        $user = User::factory()->create(['can_purchase_diamonds' => true]);
        $this->actingAs($user);

        $package = DiamondPackage::create([
            'name' => '10 Diamonds',
            'diamonds' => 10,
            'price' => 50.00,
            'currency' => 'PHP',
            'is_active' => true,
            'allow_manual' => true,
            'allow_hitpay' => true,
        ]);

        $response = $this->post(route('payments.store'), [
            'package_id' => $package->id,
            'payment_method' => 'hitpay'
        ]);

        $response->assertRedirect('https://sandbox.hit-pay.com/checkout/123');
    }

    public function test_webhook_completes_payment_and_adds_shards()
    {
        $user = User::factory()->create(['can_purchase_diamonds' => true]);
        $payment = Payment::create([
            'user_id' => $user->id,
            'reference' => 'TEST-REF-123',
            'amount' => 50,
            'currency' => 'PHP',
            'diamonds_amount' => 10,
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
        $this->assertEquals(10, $user->diamonds_balance);
    }

    public function test_payment_callback_redirects_to_success_page()
    {
        $user = User::factory()->create(['can_purchase_diamonds' => true]);
        $this->actingAs($user);

        $response = $this->get(route('payments.callback', [
            'reference' => 'TEST-REF-123',
            'status' => 'completed'
        ]));

        $response->assertRedirect(route('payments.success', ['reference' => 'TEST-REF-123']));
    }

    public function test_success_page_shows_payment_details()
    {
        $user = User::factory()->create(['can_purchase_diamonds' => true]);
        $this->actingAs($user);

        $payment = Payment::create([
            'user_id' => $user->id,
            'reference' => 'SUCCESS-REF-456',
            'hitpay_id' => 'a19ceec1-44a0-4546-9a66-4838a3b183fe',
            'amount' => 100,
            'currency' => 'PHP',
            'diamonds_amount' => 10,
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

    public function test_user_can_access_manual_checkout()
    {
        $user = User::factory()->create(['can_purchase_diamonds' => true]);
        $this->actingAs($user);

        $package = DiamondPackage::create([
            'name' => '10 Diamonds',
            'diamonds' => 10,
            'price' => 50.00,
            'currency' => 'PHP',
            'is_active' => true,
            'allow_manual' => true,
            'allow_hitpay' => true,
        ]);

        $agreement = ManualPaymentAgreement::create([
            'content' => 'Test Agreement Content'
        ]);

        $response = $this->get(route('payments.manual', $package->id));
        $response->assertStatus(200);
        $response->assertSee('Test Agreement Content');
    }

    public function test_user_can_submit_manual_proof_directly()
    {
        Storage::fake('public');

        $user = User::factory()->create(['can_purchase_diamonds' => true]);
        $this->actingAs($user);

        $package = DiamondPackage::create([
            'name' => '10 Diamonds',
            'diamonds' => 10,
            'price' => 50.00,
            'currency' => 'PHP',
            'is_active' => true,
            'allow_manual' => true,
            'allow_hitpay' => true,
        ]);

        $file = UploadedFile::fake()->create('proof.png', 100, 'image/png');

        $response = $this->post(route('payments.manual.proof', $package->id), [
            'proof' => $file,
            'i_agree' => '1'
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        // Assert file was stored
        Storage::disk('public')->assertExists('proofs/' . $file->hashName());

        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'diamond_package_id' => $package->id,
            'payment_method' => 'manual',
            'status' => 'pending',
            'proof_path' => 'proofs/' . $file->hashName()
        ]);
    }

    public function test_user_can_submit_manual_proof_via_temp_path()
    {
        Storage::fake('public');

        $user = User::factory()->create(['can_purchase_diamonds' => true]);
        $this->actingAs($user);

        $package = DiamondPackage::create([
            'name' => '10 Diamonds',
            'diamonds' => 10,
            'price' => 50.00,
            'currency' => 'PHP',
            'is_active' => true,
            'allow_manual' => true,
            'allow_hitpay' => true,
        ]);

        // Place a fake file in the temp upload folder
        $tempPath = 'tmp/uploads/proof_temp_123.png';
        Storage::disk('public')->put($tempPath, 'fake-file-content');

        $response = $this->post(route('payments.manual.proof', $package->id), [
            'proof_temp_path' => $tempPath,
            'i_agree' => '1'
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        // Assert file was moved
        Storage::disk('public')->assertMissing($tempPath);
        
        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'diamond_package_id' => $package->id,
            'payment_method' => 'manual',
            'status' => 'pending'
        ]);
    }

    public function test_user_can_reupload_proof_directly()
    {
        Storage::fake('public');

        $user = User::factory()->create(['can_purchase_diamonds' => true]);
        $this->actingAs($user);

        $package = DiamondPackage::create([
            'name' => '10 Diamonds',
            'diamonds' => 10,
            'price' => 50.00,
            'currency' => 'PHP',
            'is_active' => true,
            'allow_manual' => true,
            'allow_hitpay' => true,
        ]);

        $payment = Payment::create([
            'user_id' => $user->id,
            'diamond_package_id' => $package->id,
            'reference' => 'RE-UPLOAD-REF',
            'amount' => 50,
            'currency' => 'PHP',
            'diamonds_amount' => 10,
            'status' => 'flagged',
            'payment_method' => 'manual',
            'proof_path' => 'proofs/old_proof.png'
        ]);

        Storage::disk('public')->put('proofs/old_proof.png', 'old-content');

        $file = UploadedFile::fake()->create('new_proof.png', 100, 'image/png');

        $response = $this->post(route('payments.reupload', $payment->id), [
            'proof' => $file
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        Storage::disk('public')->assertMissing('proofs/old_proof.png');
        Storage::disk('public')->assertExists('proofs/' . $file->hashName());

        $payment->refresh();
        $this->assertEquals('pending', $payment->status);
        $this->assertEquals('proofs/' . $file->hashName(), $payment->proof_path);
    }
}
