<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\BattleRoom;
use App\Http\Controllers\BattleController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\DigitalCardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlatformSettingController;
use App\Http\Controllers\TermsOfServiceController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PlatformSettingController as AdminPlatformSettingController;
use App\Http\Controllers\Admin\GameTitleController as AdminGameTitleController;
use App\Http\Controllers\Admin\TemplateController as AdminTemplateController;
use App\Http\Controllers\Admin\DigitalCardController as AdminDigitalCardController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;

// Base Routes
Route::get('/', function () {
    return view('home');
})->name('home');

// Auth Routes (Standard)
Auth::routes(['verify' => true]);

// Google OAuth Routes
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
Route::get('/auth/google/setup', [SocialAuthController::class, 'showSetupProfile'])->name('auth.google.setup');
Route::post('/auth/google/setup', [SocialAuthController::class, 'saveSetupProfile']);

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profiles
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Arena & Battles
    Route::get('/arena', [BattleController::class, 'index'])->name('arena.index');
    Route::get('/battles/create', [BattleController::class, 'create'])->name('battles.create');
    Route::post('/battles', [BattleController::class, 'store'])->name('battles.store');
    Route::get('/battles/{battle}', BattleRoom::class)->name('battles.show');
    Route::get('/battles/{battle}/join', [BattleController::class, 'join'])->name('battles.join');

    // Forge & Templates
    Route::resource('templates', TemplateController::class);
    Route::post('/templates/{template}/forge', [TemplateController::class, 'forge'])->name('templates.forge');
    Route::post('/upload-chunk', [UploadController::class, 'uploadChunk'])->name('upload.chunk');
    
    // Inventory & Cards
    Route::get('/inventory', [DigitalCardController::class, 'index'])->name('cards.index');
    Route::get('/cards/gallery', [DigitalCardController::class, 'gallery'])->name('cards.gallery');
    Route::get('/cards/{card}', [DigitalCardController::class, 'show'])->name('cards.show');

    // Blog
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/{post}', [BlogController::class, 'show'])->name('blog.show');

    // Wallet & Payments
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::get('/diamonds/purchase', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/success', [PaymentController::class, 'success'])->name('payments.success');
    Route::get('/payments/failure', [PaymentController::class, 'failure'])->name('payments.failure');
    Route::post('/payments/webhook', [PaymentController::class, 'webhook'])->name('payments.webhook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

    // Feedback
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::post('/feedback', [FeedbackController::class, 'send'])->name('feedback.send');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    // Admin Routes
    Route::middleware(['can:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Users
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/diamonds', [AdminUserController::class, 'updateDiamonds'])->name('users.diamonds');

        // Settings
        Route::get('/settings', [AdminPlatformSettingController::class, 'edit'])->name('settings.edit');
        Route::patch('/settings', [AdminPlatformSettingController::class, 'update'])->name('settings.update');

        // Blog Management
        Route::resource('blog', AdminBlogController::class)->except(['show']);

        // Game Titles
        Route::resource('game-titles', AdminGameTitleController::class);

        // Template Management
        Route::resource('templates', AdminTemplateController::class)->only(['index', 'edit', 'update']);

        // Card Management
        Route::resource('cards', AdminDigitalCardController::class)->only(['index', 'edit', 'update']);

        // Payments
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
    });
});

// Static Pages
Route::get('/terms', [TermsOfServiceController::class, 'show'])->name('terms.show');
Route::get('/privacy', [PrivacyPolicyController::class, 'show'])->name('privacy.show');

// Redirect for old announcement links (optional but good for SEO/UX)
Route::get('/announcements', function() {
    return redirect()->route('blog.index');
})->name('announcements.list');
Route::get('/announcements/{post}', function($post) {
    return redirect()->route('blog.show', $post);
});
