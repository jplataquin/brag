<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production' || env('FORCE_HTTPS', false)) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Verified::class,
            \App\Listeners\GrantWelcomeDiamonds::class,
        );

        // Share platform settings with all views globally if database is available
        try {
            $platformSettings = \App\Models\PlatformSetting::current();
            \Illuminate\Support\Facades\View::share('platformSettings', $platformSettings);
        } catch (\Exception $e) {
            // Ignore during migrations or when database is not set up
        }
    }
}
