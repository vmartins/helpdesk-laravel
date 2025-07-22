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
                ->withRelations(['comments'])
                ->modifyProperties(function(array $properties) {
                    $newProperties = array_map(fn() => [], array_flip(array_keys($properties)));

                    $mappedProperties = [
                        'title' => __('Title'),
                        'description' => __('Description'),
                        'comment' => __('Comment'),
                        'attachments' => __('Attachments'),
                        'priority.name' => __('Priority'),
                        'unit.name' => __('Unit'),
                        'owner.name' => __('Owner'),
                        'category.name' => __('Category'),
                        'ticketStatus.name' => __('Status'),
                        'responsible.name' => __('Responsible'),
                    ];

                    foreach (array_keys($properties) as $type) {
                        foreach ($mappedProperties as $key => $value) {
                            if (array_key_exists($type, $properties)
                                && array_key_exists($key, $properties[$type])
                            ) {
                                $newProperties[$type][$value] = $properties[$type][$key];
                            }
                        }
                    }

                    return $newProperties;
                }),

            Actions\EditAction::make(),
        ];
    }
}
