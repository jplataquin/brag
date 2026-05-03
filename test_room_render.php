<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::factory()->create();
$game = App\Models\GameTitle::create(['title' => 'Test Game '.rand(), 'status' => 'active']);
$battle = App\Models\Battle::create([
    'game_title_id' => $game->id,
    'team_name_a' => 'Team A Old',
    'team_name_b' => 'Team B Old',
    'no_players_per_team' => 3,
    'status' => 'pending',
    'battle_terms' => 'test',
    'team_a_user_1' => $user->id
]);

Auth::login($user);
$controller = new \App\Http\Controllers\BattleActionController();
$html = $controller->show($battle)->render();
if(strpos($html, 'JOIN TEAM') !== false) {
    echo "Success!";
}
