<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\DigitalCardController;
use App\Http\Controllers\BattleController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Fallback GET route for logout
Route::get('/logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
});

// Dashboard
Route::get('/home', [DashboardController::class, 'index'])->name('home')->middleware('auth');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// Templates
Route::middleware('auth')->group(function () {
    Route::post('/templates/ai-preview', [TemplateController::class, 'generateAiPreview'])->name('templates.ai-preview');
    Route::resource('templates', TemplateController::class);
    Route::post('/upload/chunk', [App\Http\Controllers\UploadController::class, 'uploadChunk'])->name('upload.chunk');
});

// Digital Cards
Route::middleware('auth')->group(function () {
    Route::get('/cards', [DigitalCardController::class, 'index'])->name('cards.index');
    Route::get('/cards/{digitalCard}', [DigitalCardController::class, 'show'])->name('cards.show');
    Route::get('/cards/{digitalCard}/history', [DigitalCardController::class, 'history'])->name('cards.history');
    Route::post('/cards/forge/{template}', [DigitalCardController::class, 'forge'])->name('cards.forge');
});

// Battles
Route::middleware('auth')->group(function () {
    Route::get('/battles', [BattleController::class, 'index'])->name('battles.index');
    Route::get('/battles/create', [BattleController::class, 'create'])->name('battles.create');
    Route::post('/battles', [BattleController::class, 'store'])->name('battles.store');
    Route::get('/battles/room/{battle:room_id}', [BattleController::class, 'room'])->name('battles.room');
    Route::get('/battles/{battle}/json', [BattleController::class, 'json'])->name('battles.json');
    Route::post('/battles/{battle:room_id}/join', [BattleController::class, 'join'])->name('battles.join');
    Route::post('/battles/{battle:room_id}/start', [BattleController::class, 'start'])->name('battles.start');
    Route::post('/battles/{battle:room_id}/reject-opponent', [BattleController::class, 'rejectOpponent'])->name('battles.rejectOpponent');
    
    // Adjudicators
    Route::post('/battles/{battle:room_id}/elect-adjudicator', [BattleController::class, 'electAdjudicator'])->name('battles.electAdjudicator');
    Route::post('/battles/{battle:room_id}/accept-adjudicator', [BattleController::class, 'acceptAdjudicator'])->name('battles.acceptAdjudicator');
    Route::post('/battles/{battle:room_id}/reject-adjudicator', [BattleController::class, 'rejectAdjudicator'])->name('battles.rejectAdjudicator');
    Route::post('/battles/{battle:room_id}/leave-adjudicator', [BattleController::class, 'leaveAdjudicator'])->name('battles.leaveAdjudicator');
    
    Route::post('/battles/{battle:room_id}/invite', [BattleController::class, 'invite'])->name('battles.invite');
    Route::post('/battles/{battle:room_id}/declare-winner', [BattleController::class, 'declareWinner'])->name('battles.declareWinner');
    Route::post('/battles/{battle:room_id}/cancel', [BattleController::class, 'cancel'])->name('battles.cancel');
    Route::post('/battles/{battle:room_id}/respond-to-cancellation', [BattleController::class, 'respondToCancellation'])->name('battles.respondToCancellation');
    Route::post('/battles/{battle:room_id}/poke', [BattleController::class, 'poke'])->name('battles.poke');
    Route::post('/invites/{invite}/decline', [BattleController::class, 'declineInvite'])->name('battles.invites.decline');
    Route::get('/battles/{battle:room_id}/join', [BattleController::class, 'showJoinReadyPage'])->name('battles.join.ready');
    Route::post('/battles/{battle:room_id}/confirm-join', [BattleController::class, 'confirmJoin'])->name('battles.confirmJoin');

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
});

// Profiles & Search
Route::get('/search', [ProfileController::class, 'search'])->name('search');
Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
Route::get('/user/{username}', [ProfileController::class, 'show'])->name('profile.show');
