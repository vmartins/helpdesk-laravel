<?php

namespace App\Providers\Filament;

use App\Settings\AccountSettings;
use App\Settings\GeneralSettings;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;
use Filament\Navigation\MenuItem;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Illuminate\Contracts\Auth\Authenticatable;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn() => auth()->user()->name)
                    ->url(fn (): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-user-circle')
                    //If you are using tenancy need to check with the visible method where ->company() is the relation between the user and tenancy model as you called
                    ->visible(function (): bool {
                        return auth()->user()->exists() 
                            && !auth()->user()->socialiteUsers()->exists();
                    }),
            ])
            ->plugins([
                FilamentEditProfilePlugin::make()
                    ->slug('my-profile')
                    ->setTitle(__('My Profile'))
                    ->setNavigationLabel(__('My Profile'))
                    ->setNavigationGroup(__('Group Profile'))
                    ->setIcon('heroicon-o-user')
                    ->setSort(10)
                    ->shouldRegisterNavigation(false)
                    ->shouldShowEmailForm()
                    ->shouldShowDeleteAccountForm(false)
                    ->shouldShowBrowserSessionsForm()
                    ,

                FilamentApexChartsPlugin::make(),

                FilamentSocialitePlugin::make()
                    ->providers($this->getSocialiteProviders())
                    ->slug('admin')
                    ->registration(function (string $provider, SocialiteUserContract $oauthUser, ?Authenticatable $user) {
                        $accountSettings = app(AccountSettings::class);
                        return match($provider) {
                            'google' => $accountSettings->auth_google_registration,
                            'oauth0' => $accountSettings->auth_oauth0_registration,
                            'laravelpassport' => $accountSettings->auth_laravelpassport_registration,
                        };
                        return (bool) $user;
                    }),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->databaseNotifications()
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);


        $accountSettings = app(AccountSettings::class);

        if ($accountSettings->user_registration) {
            $panel->registration();
        }

        if ($accountSettings->user_email_verification) {
            $panel->emailVerification();
        }

        if ($accountSettings->user_password_reset) {
            $panel->passwordReset();
        }

        return $panel;
    }

    private function getSocialiteProviders(): array
    {
        $providers = [];
        $accountSettings = app(AccountSettings::class);

        if ($accountSettings->auth_google_enabled) {
            $providers[] = Provider::make('google')
                ->label('Google')
                ->icon('fab-google')
                ->color(Color::hex('#4285f4'))
                ->outlined(false)
                ->stateless($accountSettings->auth_google_stateless)
                ->scopes($accountSettings->auth_google_scopes ?? []);
        }

        if ($accountSettings->auth_oauth0_enabled) {
            $providers[] = Provider::make('auth0')
                ->label($accountSettings->auth_oauth0_title)
                ->color(Color::hex($accountSettings->auth_oauth0_color))
                ->outlined(false)
                ->stateless($accountSettings->auth_oauth0_stateless)
                ->scopes($accountSettings->auth_oauth0_scopes)
                ->with($accountSettings->auth_oauth0_extra_parameters ?? []);
        }

        if ($accountSettings->auth_laravelpassport_enabled) {
            $providers[] = Provider::make('laravelpassport')
                ->label($accountSettings->auth_laravelpassport_title)
                ->color(Color::hex($accountSettings->auth_laravelpassport_color))
                ->outlined(false)
                ->stateless($accountSettings->auth_laravelpassport_stateless)
                ->scopes($accountSettings->auth_laravelpassport_scopes ?? [])
                ->with($accountSettings->auth_laravelpassport_extra_parameters ?? []);
        }

        return $providers;
    }
}