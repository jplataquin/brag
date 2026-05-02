<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$room = new App\Livewire\BattleRoom();
$room->battle = App\Models\Battle::first();
$room->newTeamNameA = "Helloworld";
$room->updateTeamName("A");
