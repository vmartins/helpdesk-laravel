<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Forms;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
// use Rmsramos\Activitylog\Actions\ActivityLogTimelineSimpleAction;

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
