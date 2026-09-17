<?php

namespace App\Settings;

class TicketSettings extends Settings
{
    public int $default_priority = 1;
    public array $closed_status = [];
    public bool $autoclose_enabled = false;
    public int $autoclose_days = 7;
    public array $autoclose_from_status = [];
    public ?int $autoclose_to_status;
    public bool $public_ticket_creation_enabled = false;

    public static function group(): string
    {
        return 'ticket';
    }
}