<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\Event;

class ConfigServiceProvider extends ServiceProvider
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
        if (app()->runningInConsole()) return;
        
        $generalSettings = app(\App\Settings\GeneralSettings::class);

        if ($generalSettings->site_title) {
            config(['app.name' => $generalSettings->site_title]);
        }
        
        if ($generalSettings->site_url) {
            config(['app.url' => $generalSettings->site_url]);
        }

        if ($generalSettings->site_timezone) {
            config(['app.timezone' => $generalSettings->site_timezone]);
        }

        if ($generalSettings->site_locale) {
            config(['app.locale' => $generalSettings->site_locale]);
        }

        $accountSettings = app(\App\Settings\AccountSettings::class);

        if ($accountSettings->auth_google_enabled) {
            config(['services.google' => [
                'client_id' => $accountSettings->auth_google_client_id,
                'client_secret' => $accountSettings->auth_google_client_secret,
                'redirect' => $accountSettings->auth_google_redirect,
            ]]);
        }
        
        if ($accountSettings->auth_oauth0_enabled) {
            config(['services.auth0' => [
                'client_id' => $accountSettings->auth_oauth0_client_id,
                'client_secret' => $accountSettings->auth_oauth0_client_secret,
                'redirect' => $accountSettings->auth_oauth0_redirect,
                'base_url' => $accountSettings->auth_oauth0_base_url,
            ]]);
        }

        if ($accountSettings->auth_laravelpassport_enabled) {
            config(['services.laravelpassport' => [
                'client_id' => $accountSettings->auth_laravelpassport_client_id,
                'client_secret' => $accountSettings->auth_laravelpassport_client_secret,
                'redirect' => $accountSettings->auth_laravelpassport_redirect,
                'host' => $accountSettings->auth_laravelpassport_host,
                'authorize_uri' => $accountSettings->auth_laravelpassport_authorize_uri ?? 'oauth/authorize',
                'token_uri'     => $accountSettings->auth_laravelpassport_token_uri ?? 'oauth/token',
                'userinfo_uri'  => $accountSettings->auth_laravelpassport_userinfo_uri ?? 'api/user',
            ]]);
        }
    }
}
