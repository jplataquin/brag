<?php

namespace Tests\Feature;

use App\Models\GameTitle;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardForgeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Laravel 11 CSRF middleware
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    public function test_user_cannot_forge_without_shards()
    {
        $user = User::factory()->create();
        $gameTitle = GameTitle::create(['title' => 'Test Game']);
        $template = Template::create([
            'user_id' => $user->id,
            'game_title_id' => $gameTitle->id,
            'card_title' => 'TEST CARD',
            'quote' => 'Test quote',
            'photo' => 'test.jpg'
        ]);

        $this->actingAs($user);

        $response = $this->post(route('cards.forge', $template));

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertStringContainsString('You need at least 2 Shards', session('error'));
        $this->assertEquals(0, $user->digitalCards()->count());
    }

    public function test_user_can_forge_with_enough_shards()
    {
        $user = User::factory()->create();
        $user->addShards(5, 'system', 'Gift');
        
        $gameTitle = GameTitle::create(['title' => 'Test Game']);
        $template = Template::create([
            'user_id' => $user->id,
            'game_title_id' => $gameTitle->id,
            'card_title' => 'TEST CARD',
            'quote' => 'Test quote',
            'photo' => 'test.jpg'
        ]);

        $this->actingAs($user);

        $response = $this->post(route('cards.forge', $template));

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertEquals(1, $user->digitalCards()->count());
        $user->refresh();
        $this->assertEquals(3, $user->shards_balance);
    }

    public function test_forge_is_wrapped_in_transaction()
    {
        $user = User::factory()->create();
        $user->addShards(2, 'system', 'Exact amount');
        
        $gameTitle = GameTitle::create(['title' => 'Test Game']);
        $template = Template::create([
            'user_id' => $user->id,
            'game_title_id' => $gameTitle->id,
            'card_title' => 'TEST CARD',
            'quote' => 'Test quote',
            'photo' => 'test.jpg'
        ]);

        $this->actingAs($user);

        $response = $this->post(route('cards.forge', $template));
        $response->assertStatus(302);

        $user->refresh();
        $this->assertEquals(0, $user->shards_balance);
        $this->assertEquals(1, $user->digitalCards()->count());
    }
}
