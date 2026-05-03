<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$html = '<button wire:click="$set(\'a\', 1); $set(\'b\', 2)">Click</button>';
echo "Livewire 3 handles multiple \$set? Not supported.\n";
