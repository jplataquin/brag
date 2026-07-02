<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Laravel 11 CSRF middleware
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        
        // Mock Turnstile by default for all tests
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true]),
        ]);
    }

    public function test_user_receives_welcome_shards_upon_email_verification()
    {
        $response = $this->post('/register', [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'username' => 'newplayer123',
            'email' => 'newplayer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => 'on',
            'privacy' => 'on',
            'cf-turnstile-response' => 'fake-token',
        ]);

        $response->assertRedirect('/dashboard'); // The controller dictates redirecting to /dashboard

        $user = User::where('email', 'newplayer@example.com')->first();
        
        $this->assertNotNull($user);
        $this->assertEquals(0, $user->diamonds_balance); // No shards yet
        
        $this->assertDatabaseMissing('diamond_ledgers', [
            'user_id' => $user->id,
            'remarks' => 'Welcome Gift',
        ]);

        // Manually trigger the verified event
        event(new \Illuminate\Auth\Events\Verified($user));

        $this->assertEquals(10, $user->fresh()->diamonds_balance); // Diamonds granted after verification

        $this->assertDatabaseHas('diamond_ledgers', [
            'user_id' => $user->id,
            'credit' => 10,
            'debit' => 0,
            'type' => 'system',
            'remarks' => 'Welcome Gift',
        ]);
    }

    public function test_registration_does_not_allow_plus_sign_in_email()
    {
        $response = $this->post('/register', [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'username' => 'newplayerplus',
            'email' => 'newplayer+alias@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => 'on',
            'privacy' => 'on',
            'cf-turnstile-response' => 'fake-token',
        ]);

        $response->assertSessionHasErrors('email');
        
        $this->assertDatabaseMissing('users', [
            'username' => 'newplayerplus',
        ]);
    }
}
