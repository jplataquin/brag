<?php

namespace Tests\Feature;

use App\Models\GameTitle;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateEditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Laravel 11 CSRF middleware
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    public function test_game_title_cannot_be_changed_during_template_update()
    {
        $user = User::factory()->create();
        $gameTitle1 = GameTitle::create(['title' => 'Original Game']);
        $gameTitle2 = GameTitle::create(['title' => 'New Game']);
        
        $template = Template::create([
            'user_id' => $user->id,
            'game_title_id' => $gameTitle1->id,
            'card_title' => 'MY AWESOME CARD',
            'quote' => 'Some quote',
            'photo' => 'test.jpg'
        ]);

        $this->actingAs($user);

        // Attempt to update the template with a new game_title_id
        $response = $this->put(route('templates.update', $template), [
            'card_title' => 'UPDATED TITLE',
            'quote' => 'Updated quote',
            'game_title_id' => $gameTitle2->id, // Attempt to maliciously change the game title
            'image_mode' => 'upload',
            'temporary_photo_path' => 'test.jpg', // Dummy path
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $template->refresh();
        
        // Assert other fields updated
        $this->assertEquals('UPDATED TITLE', $template->card_title);
        $this->assertEquals('Updated quote', $template->quote);
        
        // Assert game_title_id did NOT change
        $this->assertEquals($gameTitle1->id, $template->game_title_id);
    }
}
