<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActivityLog\ActivityLogTimelineSimpleAction::make('timeline')
                ->label(__('Histórico'))
                ->withRelations(['comments']),

            Actions\EditAction::make(),
        ];
    }
}
