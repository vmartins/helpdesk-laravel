<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('ticket.autoclose_enabled', false);
        $this->migrator->add('ticket.autoclose_days', 7);
        $this->migrator->add('ticket.autoclose_from_status', []);
        $this->migrator->add('ticket.autoclose_to_status');
    }
};
