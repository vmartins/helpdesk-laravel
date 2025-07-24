<?php

namespace App\Filament\Resources\TicketStatusResource\RelationManagers;

use App\Models\Ticket;
use App\Settings\GeneralSettings;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;

class TicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'tickets';

    protected static ?string $recordTitleAttribute = 'title';

    public function table(Table $table): Table
    {
        return $table
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
                    ->searchable()
                    ->label(__('Category'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ticketStatus.name')
                    ->label('Status')
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Ticket $record): string => route('filament.admin.resources.tickets.view', $record)),
            ])
            ->bulkActions([]);
    }
}
