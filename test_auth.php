<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::first();
$tb = \App\Models\TeamBattle::first();

echo "User ID: " . $user->id . "\n";
echo "Team B User 1: " . var_export($tb->team_b_user_1, true) . "\n";
echo "Comparison: " . ($user->id == $tb->team_b_user_1 ? 'true' : 'false') . "\n";
