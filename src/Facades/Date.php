<?php

namespace Fooino\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string convert(string|null $date , string $format = 'Y-m-d H:i:s' , DateTimeZone $from = new DateTimeZone('UTC') , DateTimeZone $to = new DateTimeZone('UTC'), bool $throwException = false)
 * @method static bool validate(string $date, DateType $type = DateType::SHAMSI)
 *
 * @see \Fooino\Core\Concretes\Date\DateManager
 */
class Date extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'fooino-date-facade';
    }
}
