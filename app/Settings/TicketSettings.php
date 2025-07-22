<?php

namespace App\Settings;

class TicketSettings extends Settings
{
    public int $default_priority = 1;
    public array $closed_status = [];

    public static function group(): string
    {
        return 'ticket';
    }
}