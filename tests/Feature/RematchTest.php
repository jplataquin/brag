<?php

namespace Tests\Feature;

use App\Models\Battle;
use App\Models\GameTitle;
use App\Models\User;
use App\Models\DigitalCard;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RematchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['broadcasting.default' => 'null']);
    }

    protected function createGame()
    {
        return GameTitle::create([
            'title' => 'Test Game',
            'slug' => 'test-game',
            'status' => 'active'
        ]);
    }

    public function test_leaders_can_propose_rematch()
    {
        $this->withoutExceptionHandling();
        $game = $this->createGame();
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $battle = Battle::create([
            'game_title_id' => $game->id,
            'team_name_a' => 'Team A',
            'team_name_b' => 'Team B',
            'battle_terms' => 'Test terms',
            'no_players_per_team' => 1,
            'status' => 'completed',
            'team_a_user_1' => $userA->id,
            'team_b_user_1' => $userB->id,
        ]);

        $response = $this->actingAs($userA)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('battles.action.rematch', $battle));
            
        $response->assertStatus(302);

        $this->assertDatabaseHas('battles', [
            'id' => $battle->id,
            'team_a_rematch_user_id' => $userA->id,
        ]);
    }

    public function test_consensus_creates_new_battle_and_redirects()
    {
        $game = $this->createGame();
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $battle = Battle::create([
            'game_title_id' => $game->id,
            'team_name_a' => 'Team Alpha',
            'team_name_b' => 'Team Beta',
            'battle_terms' => 'Test terms',
            'no_players_per_team' => 1,
            'status' => 'completed',
            'team_a_user_1' => $userA->id,
            'team_b_user_1' => $userB->id,
        ]);

        // User A proposes
        $this->actingAs($userA)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('battles.action.rematch', $battle));

        // User B accepts
        $this->actingAs($userB)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('battles.action.rematch', $battle));

        $battle->refresh();
        $this->assertNotNull($battle->rematch_battle_id);

        $newBattle = Battle::find($battle->rematch_battle_id);
        $this->assertEquals('Team Alpha', $newBattle->team_name_a);
        $this->assertEquals($userA->id, $newBattle->team_a_user_1);
        $this->assertEquals($userB->id, $newBattle->team_b_user_1);
    }
}
