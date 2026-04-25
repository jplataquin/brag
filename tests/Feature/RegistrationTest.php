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

    public function test_user_receives_welcome_shards_upon_registration()
    {
        $response = $this->post('/register', [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'username' => 'newplayer123',
            'email' => 'newplayer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'cf-turnstile-response' => 'fake-token',
        ]);

        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'newplayer@example.com')->first();
        
        $this->assertNotNull($user);
        $this->assertEquals(10, $user->shards_balance);

        $this->assertDatabaseHas('shard_ledgers', [
            'user_id' => $user->id,
            'credit' => 10,
            'debit' => 0,
            'type' => 'system',
            'remarks' => 'Welcome Gift',
        ]);
    }
}
