<?php

namespace App\Filament\Pages\Setting;

use App\Models\Priority;
use App\Models\Setting;
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

    public function getTitle(): string
    {
        return __('Ticket');
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
                    ])
            ]);
    }
}
