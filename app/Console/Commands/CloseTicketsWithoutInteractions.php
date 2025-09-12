<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Settings\TicketSettings;
use Illuminate\Console\Command;

class CloseTicketsWithoutInteractions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:close-tickets-without-interactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close tickets without interactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!app(TicketSettings::class)->autoclose_enabled) {
            return;
        }

        $days = app(TicketSettings::class)->autoclose_days;
        $tickets = Ticket::whereIn('ticket_statuses_id', app(TicketSettings::class)->autoclose_from_status)->get();
        if ($tickets->isNotEmpty()) {
            foreach ($tickets as $ticket) {
                $lastInteraction = $ticket->updated_at > $ticket->status_updated_at
                    ? $ticket->updated_at
                    : $ticket->status_updated_at;

                $lastComment = $ticket->comments()->latest()->first();
                if ($lastComment) {
                    if ($lastComment->updated_at > $lastInteraction) {
                        $lastInteraction = $lastComment->updated_at;
                    }
                }

                if ($lastInteraction->addDays($days) < now()) {
                    $this->info("Automatically closing ticket #{$ticket->id} after {$days} days of no interaction");
                    $ticket->update(['ticket_statuses_id' => app(TicketSettings::class)->autoclose_to_status]);
                }
            }
        }
    }
}
