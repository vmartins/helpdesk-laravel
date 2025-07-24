<?php

namespace App\Support\Activitylog;

use Closure;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Rmsramos\Activitylog\Actions\Concerns\ActionContent;
use Rmsramos\Activitylog\Infolists\Components\TimeLineIconEntry;
// use Rmsramos\Activitylog\Infolists\Components\TimeLinePropertiesEntry;
use Rmsramos\Activitylog\Infolists\Components\TimeLineRepeatableEntry;
use Rmsramos\Activitylog\Infolists\Components\TimeLineTitleEntry;

class ActivityLogTimelineSimpleAction extends Action
{
    use ActionContent;

    protected ?Closure $modifyProperties;

    public function modifyProperties(Closure $closure): static
    {
        $this->modifyProperties = $closure;

        return $this;
    }

    private function getSchema(): array
    {
        return [
            TimeLineRepeatableEntry::make('activities')
                ->schema([
                    TimeLineIconEntry::make('activityData.event')
                        ->icon(function ($state) {
                            return $this->getTimelineIcons()[$state] ?? 'heroicon-m-check';
                        })
                        ->color(function ($state) {
                            return $this->getTimelineIconColors()[$state] ?? 'primary';
                        }),
                    TimeLineTitleEntry::make('activityData')
                        ->configureTitleUsing($this->modifyTitleUsing)
                        ->shouldConfigureTitleUsing($this->shouldModifyTitleUsing),
                    TimeLinePropertiesEntry::make('activityData')
                        ->modifyProperties($this->modifyProperties),
                    TextEntry::make('log_name')
                        ->hiddenLabel()
                        ->badge(),
                    TextEntry::make('updated_at')
                        ->hiddenLabel()
                        ->since()
                        ->badge(),
                ]),
        ];
    }
}
