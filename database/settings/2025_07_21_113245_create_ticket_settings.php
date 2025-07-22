<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('ticket.default_priority', 1);
        $this->migrator->add('ticket.closed_status', []);
    }
};
