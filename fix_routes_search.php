<?php
$content = file_get_contents('routes/web.php');

$route = "    Route::get('/battles/{battle}/search', [\App\Http\Controllers\UserSearchController::class, 'search'])->name('battles.search');\n    Route::get('/battles/{battle}/partial-slot/{team}/{slot}', [\App\Http\Controllers\BattleController::class, 'partialSlot'])->name('battles.partial_slot');";

$content = str_replace("    Route::get('/battles/{battle}/search', [\App\Http\Controllers\UserSearchController::class, 'search'])->name('battles.search');", $route, $content);
file_put_contents('routes/web.php', $content);
