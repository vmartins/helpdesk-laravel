<?php

namespace App\Providers;

use Livewire\Livewire;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;

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
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        if (env('LIVEWIRE_BASE_PATH')) {
            Livewire::setScriptRoute(function ($handle) {
                return Route::get(env('LIVEWIRE_BASE_PATH') . '/vendor/livewire.js', $handle);
            });

            Livewire::setUpdateRoute(function ($handle) {
                return Route::get(env('LIVEWIRE_BASE_PATH') . '/update', $handle);
            });
        }

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch->locales(['en', 'pt_BR']);
        });
    }
}
