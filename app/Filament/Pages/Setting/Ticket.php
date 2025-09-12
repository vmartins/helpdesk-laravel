<?php

namespace App\Filament\Pages\Setting;

use App\Models\Priority;
use App\Models\Setting;
use App\Models\TicketStatus;
use App\Settings\TicketSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Gate;

class Ticket extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = TicketSettings::class;

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('Ticket');
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
                Forms\Components\Section::make(__('Ticket'))
                    ->schema([
                        Forms\Components\Select::make('default_priority')
                            ->label(__('Default Priority'))
                            ->options(Priority::all()->pluck('name', 'id'))
                            ->required(),

                        Forms\Components\Select::make('closed_status')
                            ->label(__('Closed Status'))
                            ->multiple()
                            ->helperText(__('Statuses indicating that the ticket is closed and can no longer be edited'))
                            ->options(TicketStatus::all()->pluck('name', 'id'))
                            ->required(),
                    ]),

                Forms\Components\Section::make(__('Auto Close'))
                    ->schema([
                        Forms\Components\Toggle::make('autoclose_enabled')
                            ->label(__('Enabled'))
                            ->live()
                            ->helperText(__('Automatically close tickets without interactions'))
                            ->required(),

                        Forms\Components\TextInput::make('autoclose_days')
                            ->label(__('Days'))
                            ->helperText(__('Number of days without interaction to close the ticket'))
                            ->visible(fn ($get) => $get('autoclose_enabled'))
                            ->required(),

                        Forms\Components\Select::make('autoclose_from_status')
                            ->label(__('From Status'))
                            ->multiple()
                            ->options(TicketStatus::all()->pluck('name', 'id'))
                            ->helperText(__('Origin status of tickets that should be closed'))
                            ->visible(fn ($get) => $get('autoclose_enabled'))
                            ->required(),

                        Forms\Components\Select::make('autoclose_to_status')
                            ->label(__('To Status'))
                            ->options(TicketStatus::all()->pluck('name', 'id'))
                            ->helperText(__('Destination status of automatically closed tickets'))
                            ->visible(fn ($get) => $get('autoclose_enabled'))
                            ->required(),
                    ]),
            ]);
    }
}
