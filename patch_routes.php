<?php
$content = file_get_contents('routes/web.php');
$routes = <<<HTML
    Route::post('/battles/{battle}/action/rename', [\App\Http\Controllers\BattleActionController::class, 'updateTeamName'])->name('battles.action.rename');
    Route::post('/battles/{battle}/action/join', [\App\Http\Controllers\BattleActionController::class, 'join'])->name('battles.action.join');
    Route::post('/battles/{battle}/action/standup', [\App\Http\Controllers\BattleActionController::class, 'standUp'])->name('battles.action.standup');
    Route::post('/battles/{battle}/action/ready', [\App\Http\Controllers\BattleActionController::class, 'teamBReady'])->name('battles.action.ready');
    Route::post('/battles/{battle}/action/start', [\App\Http\Controllers\BattleActionController::class, 'start'])->name('battles.action.start');
    Route::post('/battles/{battle}/action/cancel', [\App\Http\Controllers\BattleActionController::class, 'cancel'])->name('battles.action.cancel');
    Route::post('/battles/{battle}/action/respond-cancel', [\App\Http\Controllers\BattleActionController::class, 'respondCancel'])->name('battles.action.respond_cancel');
    Route::post('/battles/{battle}/action/declare-win', [\App\Http\Controllers\BattleActionController::class, 'declareWin'])->name('battles.action.declare_win');
    Route::post('/battles/{battle}/action/elect-marshall', [\App\Http\Controllers\BattleActionController::class, 'electMarshall'])->name('battles.action.elect_marshall');
    Route::post('/battles/{battle}/action/accept-marshall', [\App\Http\Controllers\BattleActionController::class, 'acceptMarshall'])->name('battles.action.accept_marshall');
    Route::post('/battles/{battle}/action/reject-marshall', [\App\Http\Controllers\BattleActionController::class, 'rejectMarshall'])->name('battles.action.reject_marshall');
    Route::post('/battles/{battle}/action/invite', [\App\Http\Controllers\BattleActionController::class, 'invite'])->name('battles.action.invite');
HTML;

$content = str_replace(
    "Route::get('/battles/{battle}/room', [BattleController::class, 'room'])->name('battles.room');",
    "Route::get('/battles/{battle}/room', [BattleController::class, 'room'])->name('battles.room');\n" . $routes,
    $content
);
file_put_contents('routes/web.php', $content);
