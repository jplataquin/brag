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
    $cardsInCirculation = \App\Models\DigitalCard::count();
    return view('welcome', compact('cardsInCirculation'));
});

// Gallery
Route::get('/gallery', [DigitalCardController::class, 'gallery'])->name('gallery');

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
    Route::post('/cards/{digitalCard}/burn', [DigitalCardController::class, 'burn'])->name('cards.burn');
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
    
    // Marshalls
    Route::post('/battles/{battle:room_id}/elect-marshall', [BattleController::class, 'electMarshall'])->name('battles.electMarshall');
    Route::post('/battles/{battle:room_id}/accept-marshall', [BattleController::class, 'acceptMarshall'])->name('battles.acceptMarshall');
    Route::post('/battles/{battle:room_id}/reject-marshall', [BattleController::class, 'rejectMarshall'])->name('battles.rejectMarshall');
    Route::post('/battles/{battle:room_id}/leave-marshall', [BattleController::class, 'leaveMarshall'])->name('battles.leaveMarshall');
    
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
    
    // Wallet
    Route::get('/wallet', [\App\Http\Controllers\WalletController::class, 'index'])->name('wallet.index');
    
    // Payments / HitPay
    Route::post('/payments/checkout', [\App\Http\Controllers\PaymentController::class, 'checkout'])->name('payments.checkout');
    Route::get('/payments/callback', [\App\Http\Controllers\PaymentController::class, 'callback'])->name('payments.callback');
    Route::get('/payments/success', [\App\Http\Controllers\PaymentController::class, 'success'])->name('payments.success');
});

// HitPay Webhook (Must be outside auth middleware and should exclude CSRF)
Route::post('/payments/webhook', [\App\Http\Controllers\PaymentController::class, 'webhook'])->name('payments.webhook')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

Route::get('/offline', function () {
    return view('offline');
})->name('offline');

Route::get('/user/{username}', [ProfileController::class, 'show'])->name('profile.show');
