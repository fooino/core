<?php

namespace Fooino\Core\Traits;

use Spatie\Activitylog\{
    LogOptions,
    Traits\LogsActivity
};

trait Loggable
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(ucfirst($this->objectName()))
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->logExcept([
                'created_at',
                'updated_at',
                'deleted_at'
            ])
            ->dontLogIfAttributesChangedOnly([
                'updated_at',
                'deleted_at'
            ]);
    }
}
