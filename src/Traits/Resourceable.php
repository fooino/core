<?php

namespace Fooino\Core\Traits;

use Fooino\Core\Facades\Json;
use Illuminate\Database\Eloquent\Model;

trait Resourceable
{
    private $resourceObject;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->resourceObject = $resource;
        $this->custom();
    }

    public function custom(array $customWith = [])
    {
        $this->with = \array_merge(
            $this->with,
            \array_merge(Json::template(), $customWith)
        );

        return $this;
    }

    public function getDates(): array
    {
        $dates = [];
        if ($this->resourceObject instanceof Model) {

            foreach ($this->resourceObject->getAttributes() ?? [] as $key => $item) {

                if (
                    \substr($key, -3) === '_at' &&
                    !\in_array($key, (array)$this?->ignoreDates ?? [])
                ) {

                    $dates[$key] = $this->{$key};

                    if (
                        $this->resourceObject->hasGetMutator($key . '_tz')
                    ) {
                        $dates[$key . '_tz'] = $this->{$key . '_tz'};
                    }

                    if (
                        $this->resourceObject->hasGetMutator($key . '_ago')
                    ) {
                        $dates[$key . '_ago'] = $this->{$key . '_ago'};
                    }
                }
            }
        }
        return $dates;
    }
}
