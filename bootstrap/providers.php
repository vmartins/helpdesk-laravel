<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    // App\Providers\BroadcastServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    // App\Providers\RouteServiceProvider::class,
    App\Providers\ConfigServiceProvider::class,
    SocialiteProviders\Manager\ServiceProvider::class,
];