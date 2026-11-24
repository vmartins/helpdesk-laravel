<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Notifications\TicketAssigned;
use App\Notifications\TicketDeleted;
use App\Notifications\TicketRestored;
use App\Notifications\TicketStatusUpdated;
use App\Settings\AccountSettings;

class TicketObserver
{
    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        if (array_key_exists('ticket_statuses_id', $ticket->getDirty())
            && (
                (
                    array_key_exists('ticket_statuses_id', $ticket->getOriginal())
                    && $ticket->getDirty()['ticket_statuses_id'] != $ticket->getOriginal()['ticket_statuses_id']
                )
                || !array_key_exists('ticket_statuses_id', $ticket->getOriginal())
            )
        ) {
            $authUser = auth()->user();
            $subscribers = $ticket->getSubscribers();

            if ($authUser && $subscribers->has($authUser->id)) {
                $subscribers->pull($authUser->id);
            }

            // a newly assigned responsible already gets TicketAssigned below
            if ($ticket->wasChanged('responsible_id')) {
                $subscribers->pull($ticket->responsible_id);
            }

            $subscribers->each(fn ($subscriber) => $subscriber->notify(new TicketStatusUpdated($ticket)));
        }

        // query the relation instead of using $ticket->responsible, which may still hold the previous responsible
        $responsible = $ticket->wasChanged('responsible_id') ? $ticket->responsible()->first() : null;

        if ($responsible) {
            $authUser = auth()->user();

            $verified = ! app(AccountSettings::class)->user_email_verification || $responsible->email_verified_at;

            if ($verified && $responsible->notify_ticket_assigned && $responsible->email && (! $authUser || $authUser->id != $responsible->id)) {
                $responsible->notify(new TicketAssigned($ticket));
            }
        }
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        $authUser = auth()->user();
        if ($authUser && ! $ticket->owner->is_guest && $ticket->owner->id != $authUser->id) {
            $ticket->owner->notify(new TicketDeleted($ticket));
        }
    }

    /**
     * Handle the Ticket "restored" event.
     */
    public function restored(Ticket $ticket): void
    {
        $authUser = auth()->user();
        if ($authUser && ! $ticket->owner->is_guest && $ticket->owner->id != $authUser->id) {
            $ticket->owner->notify(new TicketRestored($ticket));
        }
    }

    /**
     * Handle the Ticket "force deleted" event.
     */
    public function forceDeleted(Ticket $ticket): void
    {
        //
    }
}
