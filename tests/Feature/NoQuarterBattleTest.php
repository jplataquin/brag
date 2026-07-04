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
        // Elo rules: Winner starts at 1000 and gets +16 points
        $this->assertEquals(1016, $cardA->elo_score);

        // Standard rules: Loser loses 1 life point (3 -> 2)
        $this->assertEquals(2, $cardB->life_points);
        $this->assertEquals(1, $cardB->losses);
        // Elo rules: Loser starts at 1000 and loses 16 points
        $this->assertEquals(984, $cardB->elo_score);
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

    public function test_concession_auto_declaration_resolves_instantly()
    {
        // Mock the BattleUpdated broadcasting event to avoid Pusher driver connections
        \Illuminate\Support\Facades\Event::fake([\App\Events\BattleUpdated::class]);

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

        // Log in as User A and declare Team B as winner (concede/Lost)
        $this->actingAs($userA);

        $response = $this->post(route('battles.action.declare_win', $battle), [
            'team' => 'B',
        ]);

        $response->assertStatus(302); // Redirect back

        $battle->refresh();
        $cardA->refresh();
        $cardB->refresh();

        // Assert battle finalized with Team B as winner
        $this->assertEquals('completed', $battle->status);
        $this->assertEquals('B', $battle->winner_team);
        
        // Both votes should be automatically set to 'B'
        $this->assertEquals('B', $battle->team_a_declare_win);
        $this->assertEquals('B', $battle->team_b_declare_win);

        // Verification of match results: Team B wins, Team A loses 1 heart
        $this->assertEquals(2, $cardA->life_points); // Lost 1 heart
        $this->assertEquals(1, $cardB->wins); // Got 1 win
    }

    public function test_anti_duping_integrity_prevents_premature_level_up()
    {
        // Mock the BattleUpdated broadcasting event to avoid Pusher driver connections
        \Illuminate\Support\Facades\Event::fake([\App\Events\BattleUpdated::class]);

        // Setup users
        $userMain = User::factory()->create();
        $dummy1 = User::factory()->create();
        $dummy2 = User::factory()->create();

        // Setup game
        $gameTitle = GameTitle::create(['title' => 'Test Game']);

        // Setup template
        $template = Template::create([
            'user_id' => $userMain->id,
            'game_title_id' => $gameTitle->id,
            'card_title' => 'EXPLOIT CARD',
            'quote' => 'Test quote',
            'photo' => 'test.jpg'
        ]);

        // Setup digital cards
        $cardMain = DigitalCard::create([
            'template_id' => $template->id,
            'owner_id' => $userMain->id,
            'original_owner_id' => $userMain->id,
            'wins' => 0,
            'losses' => 0,
            'life_points' => 3,
            'level' => 1,
            'forged_at' => now(),
        ]);

        $cardDummy1 = DigitalCard::create([
            'template_id' => $template->id,
            'owner_id' => $dummy1->id,
            'original_owner_id' => $dummy1->id,
            'wins' => 0,
            'losses' => 0,
            'life_points' => 3,
            'forged_at' => now(),
        ]);

        $cardDummy2 = DigitalCard::create([
            'template_id' => $template->id,
            'owner_id' => $dummy2->id,
            'original_owner_id' => $dummy2->id,
            'wins' => 0,
            'losses' => 0,
            'life_points' => 3,
            'forged_at' => now(),
        ]);

        // Create 3 "No Quarter" battles and 1 Standard battle (total 4 matches, total 10 wins, only 2 unique opponents)
        // Battle 1 (No Quarter): Main wins against Dummy 1 (+3 Wins)
        $battle1 = Battle::create([
            'game_title_id' => $gameTitle->id,
            'team_name_a' => "Main",
            'team_name_b' => "Dummy 1",
            'battle_terms' => "some terms",
            'no_players_per_team' => 1,
            'status' => 'completed',
            'winner_team' => 'A',
            'mode' => 'no_quarter',
            'team_a_user_1' => $userMain->id,
            'team_a_card_1' => $cardMain->id,
            'team_b_user_1' => $dummy1->id,
            'team_b_card_1' => $cardDummy1->id,
        ]);
        $cardMain->wins += 3;

        // Battle 2 (No Quarter): Main wins against Dummy 1 (+3 Wins)
        $battle2 = Battle::create([
            'game_title_id' => $gameTitle->id,
            'team_name_a' => "Main",
            'team_name_b' => "Dummy 1",
            'battle_terms' => "some terms",
            'no_players_per_team' => 1,
            'status' => 'completed',
            'winner_team' => 'A',
            'mode' => 'no_quarter',
            'team_a_user_1' => $userMain->id,
            'team_a_card_1' => $cardMain->id,
            'team_b_user_1' => $dummy1->id,
            'team_b_card_1' => $cardDummy1->id,
        ]);
        $cardMain->wins += 3;

        // Battle 3 (No Quarter): Main wins against Dummy 2 (+3 Wins)
        $battle3 = Battle::create([
            'game_title_id' => $gameTitle->id,
            'team_name_a' => "Main",
            'team_name_b' => "Dummy 2",
            'battle_terms' => "some terms",
            'no_players_per_team' => 1,
            'status' => 'completed',
            'winner_team' => 'A',
            'mode' => 'no_quarter',
            'team_a_user_1' => $userMain->id,
            'team_a_card_1' => $cardMain->id,
            'team_b_user_1' => $dummy2->id,
            'team_b_card_1' => $cardDummy2->id,
        ]);
        $cardMain->wins += 3;

        // Battle 4 (Standard): Main wins against Dummy 2 (+1 Win)
        $battle4 = Battle::create([
            'game_title_id' => $gameTitle->id,
            'team_name_a' => "Main",
            'team_name_b' => "Dummy 2",
            'battle_terms' => "some terms",
            'no_players_per_team' => 1,
            'status' => 'completed',
            'winner_team' => 'A',
            'mode' => 'standard',
            'team_a_user_1' => $userMain->id,
            'team_a_card_1' => $cardMain->id,
            'team_b_user_1' => $dummy2->id,
            'team_b_card_1' => $cardDummy2->id,
        ]);
        $cardMain->wins += 1;
        $cardMain->save();

        // Calculate and update leaderboard stats
        $cardMain->updateLeaderboardStats();

        // Under the old system:
        // - Total Matches = 4
        // - Unique Opponents = 2 (Dummy 1, Dummy 2)
        // - Integrity = (2 / 4) * 100 = 50%
        // Under the new system:
        // - Denominator = max(Total Matches [4], Total Wins [10]) = 10
        // - Unique Opponents = 2
        // - Integrity = (2 / 10) * 100 = 20%
        $this->assertEquals(20.0, (float)$cardMain->integrity_stat);

        // Run promotion check
        $promoted = $cardMain->checkPromotion();

        // Since integrity is only 20% (Level 3 requires 50%), the promotion should be denied!
        $this->assertFalse($promoted);
        $this->assertEquals(1, $cardMain->level); // Still Casual (Level 1)
    }
}
