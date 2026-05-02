<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
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

Route::get('/', function () {
    return view('home');
})->name('home');

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

    // Forge & Templates
    Route::resource('templates', TemplateController::class);
    Route::post('/templates/{template}/forge', [TemplateController::class, 'forge'])->name('templates.forge');
    Route::get('/inventory', [DigitalCardController::class, 'index'])->name('inventory.index');
    Route::get('/cards/{card}', [DigitalCardController::class, 'show'])->name('cards.show');

    // Blog
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/{post}', [BlogController::class, 'show'])->name('blog.show');

    // Payments & Diamonds
    Route::get('/diamonds/purchase', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/success', [PaymentController::class, 'success'])->name('payments.success');
    Route::get('/payments/failure', [PaymentController::class, 'failure'])->name('payments.failure');
    Route::post('/payments/webhook', [PaymentController::class, 'webhook'])->name('payments.webhook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

    // Admin Routes
    Route::middleware(['can:admin'])->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
        Route::patch('/admin/users/{user}/suspend', [UserController::class, 'suspend'])->name('admin.users.suspend');
        Route::get('/admin/settings', [PlatformSettingController::class, 'edit'])->name('admin.settings.edit');
        Route::patch('/admin/settings', [PlatformSettingController::class, 'update'])->name('admin.settings.update');

        // Blog Management
        Route::resource('admin/blog', AdminBlogController::class)->except(['show'])->names([
            'index' => 'admin.blog.index',
            'create' => 'admin.blog.create',
            'store' => 'admin.blog.store',
            'edit' => 'admin.blog.edit',
            'update' => 'admin.blog.update',
            'destroy' => 'admin.blog.destroy',
        ]);
    });
});

// Static Pages
Route::get('/terms', [TermsOfServiceController::class, 'show'])->name('terms.show');
Route::get('/privacy', [PrivacyPolicyController::class, 'show'])->name('privacy.show');

require __DIR__.'/auth.php';
