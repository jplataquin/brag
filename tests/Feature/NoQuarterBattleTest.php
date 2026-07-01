<?php

namespace Tests\Feature;

use App\Models\Battle;
use App\Models\GameTitle;
use App\Models\Template;
use App\Models\User;
use App\Models\DigitalCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoQuarterBattleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    public function test_standard_battle_result_processing()
    {
        // Setup users
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        // Setup game
        $gameTitle = GameTitle::create(['title' => 'Test Game']);

        // Setup template
        $template = Template::create([
            'user_id' => $userA->id,
            'game_title_id' => $gameTitle->id,
            'card_title' => 'TEST CARD',
            'quote' => 'Test quote',
            'photo' => 'test.jpg'
        ]);

        // Setup digital cards
        $cardA = DigitalCard::create([
            'template_id' => $template->id,
            'owner_id' => $userA->id,
            'original_owner_id' => $userA->id,
            'wins' => 0,
            'losses' => 0,
            'life_points' => 3,
            'forged_at' => now(),
        ]);

        $cardB = DigitalCard::create([
            'template_id' => $template->id,
            'owner_id' => $userB->id,
            'original_owner_id' => $userB->id,
            'wins' => 0,
            'losses' => 0,
            'life_points' => 3,
            'forged_at' => now(),
        ]);

        // Create standard battle
        $battle = Battle::create([
            'game_title_id' => $gameTitle->id,
            'team_name_a' => "Team A",
            'team_name_b' => "Team B",
            'battle_terms' => "Standard stakes",
            'no_players_per_team' => 1,
            'status' => 'active',
            'mode' => 'standard',
            'team_a_user_1' => $userA->id,
            'team_a_card_1' => $cardA->id,
            'team_b_user_1' => $userB->id,
            'team_b_card_1' => $cardB->id,
        ]);

        // Process standard battle results (A wins, B loses)
        $result = $battle->processBattleResult($cardA, $cardB, $userA);

        $cardA->refresh();
        $cardB->refresh();

        // Standard rules: Winner gets +1 win
        $this->assertEquals(1, $cardA->wins);

        // Standard rules: Loser loses 1 life point (3 -> 2)
        $this->assertEquals(2, $cardB->life_points);
        $this->assertEquals(1, $cardB->losses);
        $this->assertFalse($result['cardTransferred']);
        $this->assertEquals($userB->id, $cardB->owner_id); // Not transferred yet
    }

    public function test_no_quarter_battle_result_processing()
    {
        // Setup users
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        // Setup game
        $gameTitle = GameTitle::create(['title' => 'Test Game']);

        // Setup template
        $template = Template::create([
            'user_id' => $userA->id,
            'game_title_id' => $gameTitle->id,
            'card_title' => 'TEST CARD',
            'quote' => 'Test quote',
            'photo' => 'test.jpg'
        ]);

        // Setup digital cards
        $cardA = DigitalCard::create([
            'template_id' => $template->id,
            'owner_id' => $userA->id,
            'original_owner_id' => $userA->id,
            'wins' => 0,
            'losses' => 0,
            'life_points' => 3,
            'forged_at' => now(),
        ]);

        $cardB = DigitalCard::create([
            'template_id' => $template->id,
            'owner_id' => $userB->id,
            'original_owner_id' => $userB->id,
            'wins' => 0,
            'losses' => 0,
            'life_points' => 3, // 3 hearts
            'forged_at' => now(),
        ]);

        // Create No Quarter battle
        $battle = Battle::create([
            'game_title_id' => $gameTitle->id,
            'team_name_a' => "Team A",
            'team_name_b' => "Team B",
            'battle_terms' => "Winner takes all",
            'no_players_per_team' => 1,
            'status' => 'active',
            'mode' => 'no_quarter',
            'team_a_user_1' => $userA->id,
            'team_a_card_1' => $cardA->id,
            'team_b_user_1' => $userB->id,
            'team_b_card_1' => $cardB->id,
        ]);

        // Process battle results (A wins, B loses)
        $result = $battle->processBattleResult($cardA, $cardB, $userA);

        $cardA->refresh();
        $cardB->refresh();

        // No Quarter rules: Winner gets 3 * 1 wins since opponent had 3 life points
        $this->assertEquals(3, $cardA->wins);

        // No Quarter rules: Loser's card is instantly transferred
        $this->assertTrue($result['cardTransferred']);
        
        // Loser's card is now owned by winner, marked as trophy, and life points reset to 3 for the new owner
        $this->assertEquals($userA->id, $cardB->owner_id);
        $this->assertTrue($cardB->is_trophy);
        $this->assertEquals(3, $cardB->life_points);
        $this->assertEquals(1, $cardB->losses);
    }
}
