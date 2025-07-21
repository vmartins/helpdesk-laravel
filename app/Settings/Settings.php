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
            if (app(\Symfony\Component\Console\Input\ArgvInput::class)->getFirstArgument() !== "migrate") {
                throw $e;
            } else {
                return '';
            }
        }
    }
}