<?php

namespace App\Settings;

use Spatie\LaravelSettings\Exceptions\MissingSettings;

abstract class Settings extends \Spatie\LaravelSettings\Settings
{
    abstract public static function group(): string;

    public function __get($name)
    {
        try {
            return parent::__get($name);
        } catch (MissingSettings $e) {
            if (!in_array(
                app(\Symfony\Component\Console\Input\ArgvInput::class)->getFirstArgument(), 
                ['migrate', 'package:discover', 'filament:upgrade', 'key:generate']
            )) {
                throw $e;
            } else {
                return '';
            }
        }
    }
}