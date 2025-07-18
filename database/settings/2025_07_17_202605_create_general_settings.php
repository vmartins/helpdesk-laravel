<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_title', 'Helpdesk Laravel');
        $this->migrator->add('general.site_url', config('app.url'));
        $this->migrator->add('general.site_timezone', config('app.timezone'));
        $this->migrator->add('general.site_locale', config('app.locale'));
    }
};
