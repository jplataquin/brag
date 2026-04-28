<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$b = App\Models\TeamBattle::first();
if ($b) {
    $b->team_b_ready = false;
    $b->save();
    echo "saved\n";
} else {
    echo "No team battles\n";
}
