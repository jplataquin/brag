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
    return view('welcome', [
        'cardsInCirculation' => \App\Models\DigitalCard::count(),
        'gameTitlesCount' => \App\Models\GameTitle::where('status', 'active')->count(),
    ]);
})->name('welcome');

// Auth Routes (Standard)
Auth::routes(['verify' => true]);

// Handle old /home redirect for compatibility
Route::get('/home', function() {
    return redirect()->route('dashboard');
});

// Google OAuth Routes
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
Route::get('/auth/google/setup', [SocialAuthController::class, 'showSetupProfile'])->name('auth.google.setup');
Route::post('/auth/google/setup', [SocialAuthController::class, 'saveSetupProfile']);

// Webhooks
Route::post('/payments/webhook', [PaymentController::class, 'webhook'])->name('payments.webhook');

// Public Gallery
Route::get('/cards/gallery', [DigitalCardController::class, 'gallery'])->name('gallery');

// PWA (Public)
Route::view('/pwa-instructions', 'pwa-instructions')->name('pwa.instructions');

// Feedback (Public)
Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
Route::post('/feedback', [FeedbackController::class, 'send'])->name('feedback.send');

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profiles
    Route::get('/search', [ProfileController::class, 'search'])->name('search');
    Route::get('/profile/{username}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Arena & Battles
    Route::get('/arena', [BattleController::class, 'index'])->name('battles.index');
    Route::get('/battles/create', [BattleController::class, 'create'])->name('battles.create');
    Route::get('/battles/{battle}', [\App\Http\Controllers\BattleActionController::class, 'show'])->name('battles.show');
    Route::get('/battles/{battle}/join', [BattleController::class, 'join'])->name('battles.join');
    Route::get('/battles/{battle}/room', [BattleController::class, 'room'])->name('battles.room');
    Route::get('/battles/{battle}/search', [\App\Http\Controllers\UserSearchController::class, 'search'])->name('battles.search');
    Route::get('/battles/{battle}/partial-slot/{team}/{slot}', [\App\Http\Controllers\BattleController::class, 'partialSlot'])->name('battles.partial_slot');
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
    Route::post('/battles/{battle}/action/rematch', [\App\Http\Controllers\BattleActionController::class, 'rematch'])->name('battles.action.rematch');

    // Forge & Templates
    Route::post('/templates/ai-preview', [TemplateController::class, 'generateAiPreview'])->name('templates.ai-preview');
    Route::resource('templates', TemplateController::class);
    Route::post('/templates/{template}/forge', [DigitalCardController::class, 'forge'])->name('templates.forge');
    Route::post('/upload-chunk', [UploadController::class, 'uploadChunk'])->name('upload.chunk');
    
    // Inventory & Cards
    Route::get('/inventory', [DigitalCardController::class, 'index'])->name('cards.index');
    Route::get('/trophies', [DigitalCardController::class, 'trophies'])->name('cards.trophies');
    Route::get('/cards/{card}/history', [DigitalCardController::class, 'history'])->name('cards.history');
    Route::post('/cards/{card}/heal', [DigitalCardController::class, 'heal'])->name('cards.heal');
    Route::get('/cards/{card}', [DigitalCardController::class, 'show'])->name('cards.show');
    Route::post('/cards/{card}/burn', [DigitalCardController::class, 'burn'])->name('cards.burn');
    Route::post('/cards/{card}/report', [DigitalCardController::class, 'report'])->name('cards.report');

    // Blog
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/{post}', [BlogController::class, 'show'])->name('blog.show');

    // Wallet & Payments
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/manual/{package}', [PaymentController::class, 'manualCheckout'])->name('payments.manual');
    Route::post('/payments/manual/{package}/proof', [PaymentController::class, 'submitManualProof'])->name('payments.manual.proof');
    Route::get('/payments/success', [PaymentController::class, 'success'])->name('payments.success');
    Route::get('/payments/callback', [PaymentController::class, 'callback'])->name('payments.callback');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{payment}/comments', [PaymentController::class, 'addComment'])->name('payments.comments.store');
    Route::post('/payments/{payment}/reupload', [PaymentController::class, 'reuploadProof'])->name('payments.reupload');


    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    // Admin Routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Card Reports
        Route::get('/card-reports', [Admin\CardReportController::class, 'index'])->name('card_reports.index');
        Route::patch('/card-reports/{report}/resolve', [Admin\CardReportController::class, 'resolve'])->name('card_reports.resolve');
        Route::post('/cards/{card}/censor', [Admin\DigitalCardController::class, 'toggleCensor'])->name('cards.censor');

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
        Route::resource('game_titles', AdminGameTitleController::class)->except(['show']);

        // Diamond Packages
        Route::resource('diamond-packages', \App\Http\Controllers\Admin\DiamondPackageController::class)->except(['show']);

        // Manual Payment Agreements
        Route::resource('manual-payment-agreements', \App\Http\Controllers\Admin\ManualPaymentAgreementController::class)->except(['show']);

        // Template Management
        Route::post('/templates/premium', [AdminTemplateController::class, 'storePremium'])->name('templates.store_premium');
        Route::get('/premium-templates/{premiumTemplate}/edit', [AdminTemplateController::class, 'editPremium'])->name('premium-templates.edit');
        Route::put('/premium-templates/{premiumTemplate}', [AdminTemplateController::class, 'updatePremium'])->name('premium-templates.update');
        Route::post('/templates/{premiumTemplate}/toggle-status', [AdminTemplateController::class, 'toggleStatus'])->name('templates.toggle_status');
        Route::resource('templates', AdminTemplateController::class)->only(['index', 'edit', 'update']);

        // Card Management
        Route::resource('cards', AdminDigitalCardController::class)->only(['index', 'edit', 'update']);

        // Payments
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/users-suggest', [AdminPaymentController::class, 'usersSuggest'])->name('payments.users_suggest');
        Route::post('/payments/mass-approve', [AdminPaymentController::class, 'massApprove'])->name('payments.mass_approve');
        Route::post('/payments/mass-reject', [AdminPaymentController::class, 'massReject'])->name('payments.mass_reject');
        Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/approve', [AdminPaymentController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');
        Route::post('/payments/{payment}/revert', [AdminPaymentController::class, 'revertToFlagged'])->name('payments.revert');
        Route::post('/payments/{payment}/flag', [AdminPaymentController::class, 'flag'])->name('payments.flag');
        Route::post('/payments/{payment}/comments', [AdminPaymentController::class, 'addComment'])->name('payments.comments.store');

        // Terms & Privacy Admin
        Route::get('/terms', [TermsOfServiceController::class, 'index'])->name('terms.index');
        Route::post('/terms', [TermsOfServiceController::class, 'store'])->name('terms.store');
        Route::get('/terms/history/{id}', [TermsOfServiceController::class, 'showPrevious'])->name('terms.show_previous');
        
        Route::get('/privacy', [PrivacyPolicyController::class, 'index'])->name('privacy.index');
        Route::post('/privacy', [PrivacyPolicyController::class, 'store'])->name('privacy.store');
        Route::get('/privacy/history/{id}', [PrivacyPolicyController::class, 'showPrevious'])->name('privacy.show_previous');
    });
});

// Static Pages
Route::view('/maintenance', 'maintenance')->name('maintenance');
Route::get('/terms', [TermsOfServiceController::class, 'show'])->name('terms.show');
Route::post('/terms/agree', [TermsOfServiceController::class, 'agree'])->name('terms.agree');
Route::get('/privacy', [PrivacyPolicyController::class, 'show'])->name('privacy.show');
Route::post('/privacy/agree', [PrivacyPolicyController::class, 'agree'])->name('privacy.agree');

// Redirect for old announcement links (optional but good for SEO/UX)
Route::get('/announcements', function() {
    return redirect()->route('blog.index');
})->name('announcements.list');
Route::get('/announcements/{post}', function($post) {
    return redirect()->route('blog.show', $post);
});
