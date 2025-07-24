<?php

namespace App\Providers\Filament;

use App\Filament\Resources\TicketResource;
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
use Filament\Navigation\NavigationItem;
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
use Illuminate\Support\Facades\Storage;
use Rmsramos\Activitylog\ActivitylogPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $generalSettings = app(GeneralSettings::class);
        $accountSettings = app(AccountSettings::class);

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
                    ->visible(function (): bool {
                        return auth()->user()->exists() 
                            && !auth()->user()->socialiteUsers()->exists();
                    }),
            ])
            ->plugins([
                FilamentEditProfilePlugin::make()
                    ->slug('my-profile')
                    ->setTitle(__('My Profile', locale: $generalSettings->site_locale))
                    ->setNavigationLabel(__('My Profile', locale: $generalSettings->site_locale))
                    ->setNavigationGroup(__('Group Profile', locale: $generalSettings->site_locale))
                    ->setIcon('heroicon-o-user')
                    ->setSort(10)
                    ->shouldRegisterNavigation(false)
                    ->shouldShowEmailForm(false)
                    ->shouldShowDeleteAccountForm(true)
                    ->shouldShowBrowserSessionsForm(true),

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

                ActivitylogPlugin::make()
                    ->navigationItem(false),
                    
            ])
            ->navigationItems([
                NavigationItem::make('my_tickets')
                    ->label(__('My Tickets', locale: $generalSettings->site_locale))
                    ->icon('heroicon-o-ticket')
                    ->url(fn (): string => TicketResource::getUrl('index', ['tableFilters[only_my_tickets][isActive]' => true]))
                    ->sort(4)
                    ->isActiveWhen(fn () => 
                        request()->routeIs('filament.admin.resources.tickets.index')
                        && collect(request()->query())->dot()->get('tableFilters.only_my_tickets.isActive')
                    ),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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

        if ($generalSettings->site_logo_image) {
            $panel->brandLogo(Storage::disk('public')->url($generalSettings->site_logo_image));
        }

        if ($generalSettings->site_logo_height) {
            $panel->brandLogoHeight($generalSettings->site_logo_height);
        }

        if ($generalSettings->site_favicon_image) {
            $panel->favicon(Storage::disk('public')->url($generalSettings->site_favicon_image));
        }

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