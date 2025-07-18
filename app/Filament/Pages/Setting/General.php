<?php

namespace App\Filament\Pages\Setting;

use App\Models\Setting;
use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Gate;

class General extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GeneralSettings::class;

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public static function canAccess(): bool
    {
        return Gate::check('viewAny', Setting::class);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General')
                    ->schema([
                        Forms\Components\TextInput::make('site_title')
                            ->label(__('Allow User Registration'))
                            ->helperText('Title of site. Used as page title')
                            ->required(),

                        Forms\Components\TextInput::make('site_url')
                            ->label(__('Site URL'))
                            ->helperText('Publicly accessible URL of this site')
                            ->required(),

                        Forms\Components\Select::make('site_timezone')
                            ->label(__('Timezone'))
                            ->helperText('Set the local server timezone for date display')
                            ->options(collect(\DateTimeZone::listIdentifiers(\DateTimeZone::ALL))->mapWithKeys(fn($item) => [$item => $item]))
                            ->required(),

                        Forms\Components\Select::make('site_locale')
                            ->label(__('Locale'))
                            ->options(collect(config('filament-language-switch.locales'))->map(fn($item) => $item['name']))
                            ->required(),
                    ])
            ]);
    }
}
