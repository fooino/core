<?php

namespace Fooino\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string convert(string|int|null $date, string $format = 'Y-m-d H:i:s', \DateTimeZone|string $from = 'UTC', \DateTimeZone|string $to = 'UTC', string $fallback = '', bool $throwException = false)
 * 
 * @method static string getCalendarUsage()
 * @method static static officialCalendar()
 * @method static static unofficialCalendar()
 * 
 * @method static array getTimezones()
 * @method static bool validateTimezone(string $timezone)
 * 
 * @method static array datesBetween(string|int $from, string|int $to, string $format = 'Y-m-d', string $interval = 'P1D')
 *
 * @see \Fooino\Core\Concretes\Date\DateManager
 * @see \Fooino\Core\Concretes\Date\FooinoDateHandler
 * @see \Fooino\Core\Concretes\Date\DateHandler
 * @see \Fooino\Core\Interfaces\Dateable
 */
class Date extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'fooino-date-facade';
    }
}
