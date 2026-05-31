<?php

namespace Fooino\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string convert(string|int|null $date, string $format = 'Y-m-d H:i:s', \DateTimeZone|string $from = 'UTC', \DateTimeZone|string $to = 'UTC', string $fallback = '', bool $throwException = false)
 * 
 * @method static array getTimezones()
 * @method static bool validateTimezone(string $timezone)
 *
 * @see \Fooino\Core\Concretes\Date\DateManager
 * @see \Fooino\Core\Concretes\Date\FooinoDateHandler
 * @see \Fooino\Core\Interfaces\Dateable
 */
class Date extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'fooino-date-facade';
    }
}
