<?php
$content = file_get_contents('routes/web.php');
$content = str_replace(
    "Route::get('/battles/{battle}', BattleRoom::class)->name('battles.show');",
    "Route::get('/battles/{battle}', [\App\Http\Controllers\BattleActionController::class, 'show'])->name('battles.show');",
    $content
);
file_put_contents('routes/web.php', $content);
