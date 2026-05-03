<?php
$content = file_get_contents('routes/web.php');
$content = str_replace(
    "Route::get('/battles/{battle}/room', [\App\Http\Controllers\BattleActionController::class, 'show'])->name('battles.room');",
    "Route::get('/battles/{battle}/room', [\App\Http\Controllers\BattleActionController::class, 'show'])->name('battles.room');\n    Route::get('/battles/{battle}/search', [\App\Http\Controllers\UserSearchController::class, 'search'])->name('battles.search');",
    $content
);
file_put_contents('routes/web.php', $content);
