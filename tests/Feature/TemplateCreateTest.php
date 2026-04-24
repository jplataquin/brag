<?php

namespace Tests\Feature;

use App\Models\GameTitle;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TemplateCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Laravel 11 CSRF middleware
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    public function test_user_cannot_create_template_without_shards()
    {
        $user = User::factory()->create();
        $gameTitle = GameTitle::create(['title' => 'Test Game']);

        $this->actingAs($user);

        $response = $this->post(route('templates.store'), [
            'card_title' => 'TEST CARD',
            'game_title_id' => $gameTitle->id,
            'quote' => 'Test quote',
            'image_mode' => 'upload',
            'temporary_photo_path' => 'dummy.jpg',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertStringContainsString('You need at least ' . config('shards.costs.template_creation') . ' Shard', session('error'));
        $this->assertEquals(0, $user->templates()->count());
    }

    public function test_user_can_create_template_with_enough_shards()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->addShards(5, 'system', 'Gift');
        
        $gameTitle = GameTitle::create(['title' => 'Test Game']);

        $this->actingAs($user);

        // create a dummy file to bypass the file copy logic if it relies on file existence
        Storage::disk('public')->put('dummy.jpg', 'content');

        $response = $this->post(route('templates.store'), [
            'card_title' => 'TEST CARD',
            'game_title_id' => $gameTitle->id,
            'quote' => 'Test quote',
            'image_mode' => 'upload',
            'temporary_photo_path' => 'dummy.jpg',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertEquals(1, $user->templates()->count());
        $user->refresh();
        $this->assertEquals(5 - config('shards.costs.template_creation'), $user->shards_balance);
    }

    public function test_template_creation_is_wrapped_in_transaction()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->addShards(config('shards.costs.template_creation'), 'system', 'Exact amount');
        
        $gameTitle = GameTitle::create(['title' => 'Test Game']);

        $this->actingAs($user);

        Storage::disk('public')->put('dummy.jpg', 'content');

        $response = $this->post(route('templates.store'), [
            'card_title' => 'TEST CARD',
            'game_title_id' => $gameTitle->id,
            'quote' => 'Test quote',
            'image_mode' => 'upload',
            'temporary_photo_path' => 'dummy.jpg',
        ]);
        
        $response->assertStatus(302);

        $user->refresh();
        $this->assertEquals(0, $user->shards_balance);
        $this->assertEquals(1, $user->templates()->count());
    }
}
