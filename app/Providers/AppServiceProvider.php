<?php

namespace App\Providers;

use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;
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
