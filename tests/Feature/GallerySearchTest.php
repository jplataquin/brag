<?php

namespace Tests\Feature;

use App\Models\DigitalCard;
use App\Models\GameTitle;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GallerySearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_search_filters_by_card_title()
    {
        // Setup
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $game = GameTitle::create(['title' => 'Test Game', 'slug' => 'test-game']);
        
        $template1 = Template::create([
            'user_id' => $user1->id,
            'game_title_id' => $game->id,
            'card_title' => 'Epic Dragon',
            'quote' => 'Fear the fire.',
        ]);

        $template2 = Template::create([
            'user_id' => $user2->id,
            'game_title_id' => $game->id,
            'card_title' => 'Shadow Assassin',
            'quote' => 'Death from above.',
        ]);

        $card1 = DigitalCard::create([
            'template_id' => $template1->id,
            'owner_id' => $user1->id,
            'original_owner_id' => $user1->id,
            'level' => 1,
            'status' => 'Maintained',
            'forged_at' => now(),
        ]);

        $card2 = DigitalCard::create([
            'template_id' => $template2->id,
            'owner_id' => $user2->id,
            'original_owner_id' => $user2->id,
            'level' => 1,
            'status' => 'Maintained',
            'forged_at' => now(),
        ]);

        // Search for "Dragon"
        $response = $this->get(route('gallery', ['search' => 'Dragon']));

        $response->assertStatus(200);
        $response->assertSee('Epic Dragon');
        $response->assertDontSee('Shadow Assassin');

        // Search for "Assassin"
        $response = $this->get(route('gallery', ['search' => 'Assassin']));

        $response->assertStatus(200);
        $response->assertSee('Shadow Assassin');
        $response->assertDontSee('Epic Dragon');

        // Search for something that doesn't exist
        $response = $this->get(route('gallery', ['search' => 'Phoenix']));

        $response->assertStatus(200);
        $response->assertSee('No cards found in circulation');
        $response->assertDontSee('Epic Dragon');
        $response->assertDontSee('Shadow Assassin');
    }
}
