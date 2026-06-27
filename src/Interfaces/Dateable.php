<?php

namespace Fooino\Core\Interfaces;

use DateTimeZone;

interface Dateable
{
    /**
     * Convert a date between timezones and calendar systems (Gregorian, Jalali, Hijri)
     * 
     * @throws \Fooino\Core\Exceptions\CanNotConvertDateException
     */
    public function convert(string|int|null $date, string $format = STANDARD_DATE_TIME_FORMAT, DateTimeZone|string $from = 'UTC', DateTimeZone|string $to = 'UTC', string $fallback = '', bool $throwException = false): string;

    /**
     * Get the current calendar mode: official (government-set) or unofficial (religious/cultural)
     */
    public function getCalendarUsage(): string;

    /**
     * Switch to the official calendar (government-set) for date conversions
     */
    public function officialCalendar(): static;

    /**
     * Switch to the unofficial calendar (religious/cultural) for date conversions
     */
    public function unofficialCalendar(): static;

    /**
     * Get all supported timezone identifiers
     */
    public function getTimezones(): array;

    /**
     * Check whether a timezone string is a valid PHP timezone identifier
     */
    public function validateTimezone(string $timezone): bool;

    /**
     * Generate an array of dates within a given period(from, to) at specified intervals and format.
     * 
     * @throws \Fooino\Core\Exceptions\CanNotConvertDateException
     * 
     * @throws \Fooino\Core\Exceptions\FooinoRuntimeException
     * 
     * @throws \Fooino\Core\Exceptions\InfiniteLoopException
     */
    public function datesBetween(string|int $from, string|int $to, string $format = STANDARD_DATE_FORMAT, string $interval = 'P1D'): array;
}
