<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent as BaseProfile;

class MyProfile extends BaseProfile
{
    protected static ?int $navigationSort = 500;

    protected function getUpdateProfileFormSchema(): array
    {
        return  [
            Forms\Components\TextInput::make('name')
                ->required()
                ->label(__('filament-breezy::default.fields.name')),
        ];
    }
}
