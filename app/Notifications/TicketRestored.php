<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Settings\GeneralSettings;
use App\Support\Notifications\Debounce;
use App\Support\Notifications\ShouldBeDebounce;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;

class TicketRestored extends Notification implements ShouldQueue, ShouldBeDebounce
{
    use Queueable;
    use Debounce;

    protected $ticket;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function viaDebounce(object $notifiable): array
    {
        return [
            'mail' => true,
            'database' => false,
        ];
    }

    public function viaDebounceWait(object $notifiable): array
    {
        return [
            'mail' => 60,
        ];
    }

    /**
     * Determine which connections should be used for each notification channel.
     *
     * @return array<string, string>
     */
    public function viaConnections(): array
    {
        return [
            'mail' => 'database',
            'database' => 'sync',
        ];
    }

    public function getDebounceCacheKey(object $notifiable, string $channel): string
    {
        $className = Str::slug(__CLASS__);

        if ($notifiable instanceof AnonymousNotifiable) {
            if (is_array($notifiable->routes[$channel])) {
                $notifiableId = implode(';', array_keys($notifiable->routes[$channel]));
            } else {
                $notifiableId = $notifiable->routes[$channel];
            }
        } else {
            $notifiableId = $notifiable->id;
        }

        return "{$className}-{$channel}-{$notifiableId}:{$this->ticket->id}";
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $siteTitle = app(GeneralSettings::class)->site_title;
        $subjectPrefix = "[{$siteTitle}] ";
        $activityRestored = $this->ticket->activities()->where('event', 'restored')->get()->last();
        $restoredBy = '-';
        if ($activityRestored) {
            $restoredBy = $activityRestored->causer->name;
        }

        return (new MailMessage)
            ->subject($subjectPrefix . __('Ticket #:ticket restored', [
                'ticket' => $this->ticket->id, 
            ]))
            ->greeting(__("Ticket") . ": {$this->ticket->title}")
            ->line(__("Restored by") . ": {$restoredBy}");
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title(__('Ticket #:ticket restored', [
                'ticket' => $this->ticket->id, 
            ]))
            ->body($this->ticket->title)
            ->getDatabaseMessage();
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
