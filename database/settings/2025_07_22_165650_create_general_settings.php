<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_logo_image', '');
        $this->migrator->add('general.site_logo_height', '3rem');
        $this->migrator->add('general.site_favicon_image', '');
    }
};
