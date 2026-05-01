<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\DigitalCardController;
use App\Http\Controllers\BattleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TermsOfServiceController;
use App\Http\Controllers\PrivacyPolicyController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', function () {
    $cardsInCirculation = \App\Models\DigitalCard::count();
    $gameTitlesCount = \App\Models\GameTitle::where('status', 'active')->count();
    return view('welcome', compact('cardsInCirculation', 'gameTitlesCount'));
});

// Gallery
Route::get('/gallery', [DigitalCardController::class, 'gallery'])->name('gallery');

Auth::routes(['verify' => true]);

// Social Auth
Route::get('/auth/google', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'handleGoogleCallback']);
Route::get('/auth/google/setup', [App\Http\Controllers\Auth\SocialAuthController::class, 'showSetupProfile'])->name('auth.google.setup');
Route::post('/auth/google/setup', [App\Http\Controllers\Auth\SocialAuthController::class, 'saveSetupProfile']);

// Terms of Service (Public view)
Route::get('/terms', [TermsOfServiceController::class, 'show'])->name('terms.show');

// Privacy Policy (Public view)
Route::get('/privacy', [PrivacyPolicyController::class, 'show'])->name('privacy.show');

// Agreements (Only auth required)
Route::middleware(['auth'])->group(function () {
    Route::post('/terms/agree', [TermsOfServiceController::class, 'agree'])->name('terms.agree');
    Route::post('/privacy/agree', [PrivacyPolicyController::class, 'agree'])->name('privacy.agree');
});

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DigitalCardController as AdminDigitalCardController;
use App\Http\Controllers\Admin\TemplateController as AdminTemplateController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\GameTitleController as AdminGameTitleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PlatformSettingController as AdminPlatformSettingController;

use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\AnnouncementController;

// Admin Routes
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    // Announcements Management
    Route::resource('admin/announcements', AdminAnnouncementController::class)->names([
        'index' => 'announcements.index',
        'create' => 'announcements.create',
        'store' => 'announcements.store',
        'edit' => 'announcements.edit',
        'update' => 'announcements.update',
        'destroy' => 'announcements.destroy',
    ]);
    
    // Platform Settings
    Route::get('/admin/settings', [AdminPlatformSettingController::class, 'edit'])->name('admin.settings.edit');
    Route::put('/admin/settings', [AdminPlatformSettingController::class, 'update'])->name('admin.settings.update');
    
    // Terms of Service
    Route::get('/admin/terms', [TermsOfServiceController::class, 'index'])->name('admin.terms.index');
    Route::post('/admin/terms', [TermsOfServiceController::class, 'store'])->name('admin.terms.store');
    Route::get('/admin/terms/{id}', [TermsOfServiceController::class, 'showPrevious'])->name('admin.terms.show_previous');

    // Privacy Policy
    Route::get('/admin/privacy', [PrivacyPolicyController::class, 'index'])->name('admin.privacy.index');
    Route::post('/admin/privacy', [PrivacyPolicyController::class, 'store'])->name('admin.privacy.store');
    Route::get('/admin/privacy/{id}', [PrivacyPolicyController::class, 'showPrevious'])->name('admin.privacy.show_previous');
    
    // Users Management
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::post('/admin/users/{user}/diamonds', [AdminUserController::class, 'updateDiamonds'])->name('admin.users.diamonds');
    
    // Game Titles Management
    Route::resource('admin/game_titles', AdminGameTitleController::class)->except(['show'])->names([
        'index' => 'admin.game_titles.index',
        'create' => 'admin.game_titles.create',
        'store' => 'admin.game_titles.store',
        'edit' => 'admin.game_titles.edit',
        'update' => 'admin.game_titles.update',
        'destroy' => 'admin.game_titles.destroy',
    ]);
    
    // Digital Cards Management
    Route::get('/admin/cards', [AdminDigitalCardController::class, 'index'])->name('admin.cards.index');
    Route::get('/admin/cards/{id}/edit', [AdminDigitalCardController::class, 'edit'])->name('admin.cards.edit');
    Route::put('/admin/cards/{id}', [AdminDigitalCardController::class, 'update'])->name('admin.cards.update');

    // Templates Management
    Route::get('/admin/templates', [AdminTemplateController::class, 'index'])->name('admin.templates.index');
    Route::get('/admin/templates/{id}/edit', [AdminTemplateController::class, 'edit'])->name('admin.templates.edit');
    Route::put('/admin/templates/{id}', [AdminTemplateController::class, 'update'])->name('admin.templates.update');

    // Payments Management
    Route::get('/admin/payments', [AdminPaymentController::class, 'index'])->name('admin.payments.index');
    Route::get('/admin/payments/{payment}', [AdminPaymentController::class, 'show'])->name('admin.payments.show');
});

// Fallback GET route for logout
Route::get('/logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
});

// Dashboard
Route::middleware(['auth', 'verified', 'terms.agreed', 'privacy.agreed'])->group(function () {
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Templates
Route::middleware(['auth', 'verified', 'terms.agreed', 'privacy.agreed'])->group(function () {
    Route::post('/templates/ai-preview', [TemplateController::class, 'generateAiPreview'])->name('templates.ai-preview');
    Route::resource('templates', TemplateController::class);
    Route::post('/upload/chunk', [App\Http\Controllers\UploadController::class, 'uploadChunk'])->name('upload.chunk');
});

// Digital Cards
Route::middleware(['auth', 'verified', 'terms.agreed', 'privacy.agreed'])->group(function () {
    Route::get('/cards', [DigitalCardController::class, 'index'])->name('cards.index');
    Route::get('/cards/{digitalCard}', [DigitalCardController::class, 'show'])->name('cards.show');
    Route::post('/cards/{digitalCard}/burn', [DigitalCardController::class, 'burn'])->name('cards.burn');
    Route::post('/cards/{digitalCard}/heal', [DigitalCardController::class, 'heal'])->name('cards.heal');
    Route::get('/cards/{digitalCard}/history', [DigitalCardController::class, 'history'])->name('cards.history');
    Route::post('/cards/forge/{template}', [DigitalCardController::class, 'forge'])->name('cards.forge');
    
    // Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.list');
    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');
});

// Battles
Route::middleware(['auth', 'verified', 'terms.agreed', 'privacy.agreed'])->group(function () {

    Route::get('/battles', [BattleController::class, 'index'])->name('battles.index');
    Route::get('/battles/create', [BattleController::class, 'create'])->name('battles.create');
    Route::get('/battles/room/{battle}', [BattleController::class, 'room'])->name('battles.room');
    Route::get('/battles/room/{battle}/join', [BattleController::class, 'join'])->name('battles.join');

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
});

// Profiles & Search
Route::get('/search', [ProfileController::class, 'search'])->name('search');
Route::middleware(['auth', 'verified', 'terms.agreed', 'privacy.agreed'])->group(function () {
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
Route::post('/payments/webhook', [\App\Http\Controllers\PaymentController::class, 'webhook'])->name('payments.webhook')->withoutMiddleware([\IlluminateFoundation\Http\Middleware\ValidateCsrfToken::class]);

Route::get('/offline', function () {
    return view('offline');
})->name('offline');

Route::get('/install', function () {
    return view('pwa-instructions');
})->name('pwa.instructions');

Route::get('/user/{username}', [ProfileController::class, 'show'])->name('profile.show');
