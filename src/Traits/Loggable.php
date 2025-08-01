<?php

namespace Fooino\Core\Traits;

use Spatie\Activitylog\{
    LogOptions,
    Traits\LogsActivity,
    Contracts\Activity
};

trait Loggable
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName($this->getLogName())
            ->logExcept(
                array_merge(
                    [
                        'created_at',
                        'updated_at',
                        'deleted_at'
                    ],
                    $this?->hidden ?? [],
                    $this?->getLogExceptions() ?? []
                )
            )
            ->dontLogIfAttributesChangedOnly([
                'created_at',
                'updated_at',
                'deleted_at'
            ]);
    }


    public function getLogName(): string
    {
        return lcfirst($this->objectClassName());
    }

    public function getLogExceptions(): array
    {
        return [
            // 'email_verified_at'
        ];
    }

    public function getJsonLogExceptions(): array
    {
        return [
            // 'info->url->0',
            // 'info->url->1->data',
            // 'info->url->style',
            // 'info->url->position->top'
        ];
    }


    public function tapActivity(Activity $activity, string $eventName)
    {
        $properties = $activity->properties->toArray();

        $properties = $this->removeNullsLogData(data: $properties, event: $eventName);

        $properties = $this->removeJsonLogExceptions(data: $properties, event: $eventName);

        $activity->properties = $properties;
    }


    protected function removeNullsLogData(array $data, string $event): array
    {

        if (
            !in_array($event, ['created', 'deleted', 'restored'])
        ) {
            return $data;
        }

        foreach ($data as $key => $props) {

            foreach ($props ?? [] as $propsKey => $attr) {

                if (
                    is_null(emptyToNullOrValue($attr))
                ) {
                    unset($data[$key][$propsKey]);
                }

                // 
            }
        }

        return $data;
    }


    protected function removeJsonLogExceptions(array $data, string $event): array
    {
        $jsonExceptions = $this->getJsonLogExceptions();

        if (
            blank($jsonExceptions)
        ) {
            return $data;
        }

        $removePaths = function (&$item, $path) {

            $keys = explode('.', $path);

            $lastKey = array_pop($keys);

            $current = &$item;

            foreach ($keys as $key) {
                if (
                    !isset($current[$key]) ||
                    !is_array($current[$key])
                ) {
                    return;
                }
                $current = &$current[$key];
            }

            unset($current[$lastKey]);
        };

        // Convert '->' to '.' in paths
        $paths = array_map(fn($path) => str_replace('->', '.', $path), $jsonExceptions);

        foreach (['attributes', 'old'] as $propertyType) {

            if (
                filled($data[$propertyType] ?? [])
            ) {
                foreach ($paths as $path) {
                    $removePaths($data[$propertyType], $path);
                }
            }
        }

        return $data;
    }
}
