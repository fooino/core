<?php

namespace Fooino\Core\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;

class ModelPriorityChangedEvent
{
    use
        Dispatchable;

    public function __construct(
        public Model $model,
        public int|float $oldPriority,
        public int|float $newPriority,
    ) {
    }
}
