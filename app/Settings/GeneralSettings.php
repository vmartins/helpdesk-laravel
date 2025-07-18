<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_title;
    public string $site_url;
    public string $site_timezone = 'UTC';
    public string $site_locale = 'en';

    public static function group(): string
    {
        return 'general';
    }
}