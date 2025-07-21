<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;

class TicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'tickets';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->translateLabel()
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('owner.name')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('responsible.name')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('ticket_status.name')
                    ->label(__("Ticket Status")),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
