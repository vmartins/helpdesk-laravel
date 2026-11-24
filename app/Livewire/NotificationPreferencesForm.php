<?php

namespace App\Livewire;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasUser;
use Joaopaulolndev\FilamentEditProfile\Livewire\BaseProfileForm;

class NotificationPreferencesForm extends BaseProfileForm
{
    use HasUser;

    protected string $view = 'livewire.notification-preferences-form';

    public ?array $data = [];

    protected static int $sort = 15;

    public function mount(): void
    {
        $this->user = $this->getUser();

        $this->form->fill($this->user->only(['notify_ticket_created', 'notify_ticket_assigned']));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Email notifications'))
                    ->aside()
                    ->description(__('Choose which ticket emails you want to receive.'))
                    ->schema(static::getFormSchema()),
            ])
            ->statePath('data');
    }

    /**
     * Shared with UserResource, so admins can change other users' preferences.
     */
    public static function getFormSchema(): array
    {
        return [
            Toggle::make('notify_ticket_created')
                ->label(__('Tickets created that I can view'))
                ->helperText(__('Unit roles receive tickets of their units; Super Admin and Global Staff receive tickets of all units.')),

            Toggle::make('notify_ticket_assigned')
                ->label(__('Tickets assigned to me as responsible')),
        ];
    }

    public function save(): void
    {
        $this->user->update($this->form->getState());

        Notification::make()
            ->success()
            ->title(__('filament-edit-profile::default.saved_successfully'))
            ->send();
    }
}
