<?php

namespace App\Filament\Pages\Setting;

use App\Models\Priority;
use App\Models\Setting;
use App\Models\TicketStatus;
use App\Settings\MailSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Gate;

class Mail extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = MailSettings::class;

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('Mail');
    }

    public function getTitle(): string
    {
        return self::getNavigationLabel();
    }

    public static function canAccess(): bool
    {
        return Gate::check('viewAny', Setting::class);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Mail'))
                    ->schema([
                        Forms\Components\TextInput::make('from_name')
                            ->translateLabel()
                            ->required(),

                        Forms\Components\TextInput::make('from_address')
                            ->translateLabel()
                            ->required(),

                        Forms\Components\Select::make('mailer')
                            ->translateLabel()
                            ->options([
                                'log' => 'Log',
                                'smtp' => 'SMTP',
                                'sendmail' => 'Sendmail',
                            ])
                            ->live()
                            ->required(),
                    ]),

                Forms\Components\Section::make(__('SMTP'))
                    ->schema([
                        Forms\Components\TextInput::make('smtp_host')
                            ->label(__('Host'))
                            ->required(),

                        Forms\Components\TextInput::make('smtp_port')
                            ->label(__('Port'))
                            ->required(),

                        Forms\Components\TextInput::make('smtp_username')
                            ->label(__('Username'))
                            ->required(),

                        Forms\Components\TextInput::make('smtp_password')
                            ->label(__('Password'))
                            ->required(),

                        Forms\Components\TextInput::make('smtp_localdomain')
                            ->label(__('Local Domain'))
                            ->required(),

                        Forms\Components\Select::make('smtp_scheme')
                            ->label(__('Schema'))
                            ->options([
                                'smtp' => 'smtp',
                                'smtps' => 'smtps',
                            ])
                            ->required(),
                    ])
                    ->visible(fn ($get) => $get('mailer') == 'smtp'),

                Forms\Components\Section::make(__('Sendmail'))
                    ->schema([
                        Forms\Components\TextInput::make('sendmail_path')
                            ->label(__('Path'))
                            ->required(),
                    ])
                    ->visible(fn ($get) => $get('mailer') == 'sendmail'),
            ]);
    }
}
