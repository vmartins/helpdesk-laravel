<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Ticket;
use App\Observers\CommentObserver;
use App\Observers\TicketObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Ticket::observe(TicketObserver::class);
        Comment::observe(CommentObserver::class);

        Event::listen(function (\Illuminate\Notifications\Events\NotificationSent $event) {
            if ($event->notification instanceof \App\Support\Notifications\ShouldBeDebounce) {
                Cache::lock($event->notification->getDebounceCacheKey($event->notifiable, $event->channel))
                    ->forceRelease();
            }
        });

        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('auth0', \SocialiteProviders\Auth0\Provider::class);
        });

        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('laravelpassport', \SocialiteProviders\LaravelPassport\Provider::class);
        });

        Event::listen(function (\DutchCodingCompany\FilamentSocialite\Events\Login $event) {
            $event->socialiteUser->getUser()->update([
                'name' => $event->oauthUser->getName(),
                'email' => $event->oauthUser->getEmail(),
            ]);
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
