<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Settings\GeneralSettings;
use App\Support\Notifications\Debounce;
use App\Support\Notifications\ShouldBeDebounce;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;

class TicketCommentCreated extends Notification implements ShouldQueue, ShouldBeDebounce
{
    use Queueable;
    use Debounce;

    protected $comment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
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
        return "{$className}-{$channel}-{$notifiable->id}:{$this->comment->id}";
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $siteTitle = app(GeneralSettings::class)->site_title;
        $subjectPrefix = "[{$siteTitle}] ";

        return (new MailMessage)
            ->subject($subjectPrefix . __('New comment on ticket #:ticket', [
                'ticket' => $this->comment->ticket->id, 
            ]))
            ->greeting((__("Ticket") . ": {$this->comment->ticket->title}"))
            ->line(new HtmlString(__("Comment") . ": {$this->comment->comment}"))
            ->action(__('View'), route('filament.admin.resources.tickets.view', $this->comment->ticket));
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title(__('New comment on ticket #:ticket', [
                'ticket' => $this->comment->ticket->id, 
            ]))
            ->body($this->comment->ticket->title)
            ->actions([
                Action::make('view')
                    ->translateLabel()
                    ->button()
                    ->url(route('filament.admin.resources.tickets.view', $this->comment->ticket)),
                ])
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
