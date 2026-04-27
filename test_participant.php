<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tb = \App\Models\TeamBattle::first();
echo "TB ID: " . $tb->id . "\n";
echo "TB no_players: " . $tb->no_players_per_team . "\n";
for ($i=1; $i<=$tb->no_players_per_team; $i++) {
    echo "A{$i}: " . var_export($tb->{"team_a_user_{$i}"}, true) . "\n";
    echo "B{$i}: " . var_export($tb->{"team_b_user_{$i}"}, true) . "\n";
}
