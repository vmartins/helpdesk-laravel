<?php

namespace App\Settings;

class GeneralSettings extends Settings
{
    public string $site_title;
    public ?string $site_logo_image;
    public ?string $site_logo_height;
    public ?string $site_favicon_image;
    public string $site_url;
    public string $site_timezone = 'UTC';
    public string $site_locale = 'en';
    public string $datetime_format = 'Y-m-d H:i:s';

    public static function group(): string
    {
        return 'general';
    }
}