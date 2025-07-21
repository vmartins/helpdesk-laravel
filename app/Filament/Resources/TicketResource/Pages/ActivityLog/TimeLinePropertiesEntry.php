<?php

namespace App\Filament\Resources\TicketResource\Pages\ActivityLog;

use Filament\Infolists\Components\Entry;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;
use Rmsramos\Activitylog\Infolists\Components\TimeLinePropertiesEntry as RmsramosTimeLinePropertiesEntry;
use Rmsramos\Activitylog\Infolists\Concerns\HasModifyState;

class TimeLinePropertiesEntry extends RmsramosTimeLinePropertiesEntry
{
    use HasModifyState;

    protected $originalState;
    
    protected function setup(): void
    {
        parent::setup();

        $this->configurePropertieEntry();
    }

    private function configurePropertieEntry(): void
    {
        $this
            ->hiddenLabel()
            ->modifyState(fn ($state) => $this->modifiedProperties($state));
    }

    private function modifiedProperties($state): ?HtmlString
    {
        $this->originalState = $state;
        
        $properties = $state['properties'];

        if (! empty($properties)) {
            $changes    = $this->getPropertyChanges($properties);
            $causerName = $this->getCauserName($state['causer']);

            return new HtmlString(trans('activitylog::infolists.components.updater_updated', [
                'causer'  => $causerName,
                'event'   => __('activitylog::action.event.' . $state['event']),
                'changes' => implode('<br>', $changes),
            ]));
        }

        return null;
    }

    private function getPropertyChanges(array $properties): array
    {
        $changes = [];

        if (isset($properties['old'], $properties['attributes'])) {
            $changes = $this->compareOldAndNewValues($properties['old'], $properties['attributes']);
        } elseif (isset($properties['attributes'])) {
            $changes = $this->getNewValues($properties['attributes']);
        } elseif (isset($properties['old'])) {
            $changes = $this->getNewValues($properties['old']);
        }

        return $changes;
    }

    private function compareOldAndNewValues(array $oldValues, array $newValues): array
    {
        $changes = [];

        foreach ($newValues as $key => $newValue) {
            $oldValue = is_array($oldValues[$key]) ? json_encode($oldValues[$key]) : $oldValues[$key] ?? '-';
            $newValue = $this->formatNewValue($newValue);

            if (isset($oldValues[$key]) && $oldValues[$key] != $newValue) {
                $changes[] = trans('activitylog::infolists.components.from_oldvalue_to_newvalue',
                    [
                        'key'       => $this->transKey($key),
                        'old_value' => htmlspecialchars($oldValue),
                        'new_value' => htmlspecialchars($newValue),
                    ]);
            } else {
                $changes[] = trans('activitylog::infolists.components.to_newvalue',
                    [
                        'key'       => $this->transKey($key),
                        'new_value' => htmlspecialchars($newValue),
                    ]);
            }
        }

        return $changes;
    }

    private function getNewValues(array $newValues): array
    {
        return array_map(
            fn ($key, $value) => sprintf(
                __('activitylog::timeline.properties.getNewValues'),
                $this->transKey($key),
                htmlspecialchars($this->formatNewValue($value))
            ),
            array_keys($newValues),
            $newValues
        );
    }

    private function formatNewValue($value): string
    {
        return is_array($value) ? json_encode($value) : $value ?? '—';
    }

    private function transKey($key): string
    {
        $className = (Str::kebab(class_basename(get_class($this->originalState['subject']))));

        if (trans()->has("filament-activitylog.fields.{$className}.{$key}")) {
            return trans("filament-activitylog.fields.{$className}.{$key}");
        } else {
            return $key;
        }
    }
}