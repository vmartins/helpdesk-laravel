<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Notifications\TicketStatusUpdated;

class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        
    }

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

            if ($subscribers->has($authUser->id)) {
                $subscribers->pull($authUser->id);
            }

            $subscribers->each(fn ($subscriber) => $subscriber->notify(new TicketStatusUpdated($ticket)));
        }
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "restored" event.
     */
    public function restored(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "force deleted" event.
     */
    public function forceDeleted(Ticket $ticket): void
    {
        //
    }
}
