<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Ticket;
use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

class TicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'tickets';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Tickets');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->translateLabel()
                    ->required()
                    ->maxLength(255),
            ])
        ;
    }

    public function table(Table $table): Table
    {
        return $table
            ->modelLabel(__('Ticket'))
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(app(GeneralSettings::class)->datetime_format)
                    ->translateLabel()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('Category'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ticketStatus.name')
                    ->label(__('Status'))
                    ->sortable(),
            ])
            ->filters([
            ])
            ->headerActions([
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Ticket $record): string => route('filament.admin.resources.tickets.view', $record)),
            ])
            ->bulkActions([
            ])
        ;
    }
}
