<?php

namespace Fooino\Core\Interfaces;

use DateTimeZone;

interface Dateable
{
    /**
     * Convert date base on timezone and the format you desire.
     *
     * @param string|null $date
     * @param string $format
     * @param DateTimeZone|string $from
     * @param DateTimeZone|string $to
     * @param string $fallback
     * @param bool $throwException
     * 
     * @return string
     */
    public function convert(string|null $date, string $format = 'Y-m-d H:i:s', DateTimeZone|string $from = 'UTC', DateTimeZone|string $to = 'UTC', string $fallback = '', bool $throwException = false): string;

    /**
     * Validate Gregorian Date
     * 
     * @param string $date
     * 
     * @return bool
     */
    public function validateGregorian(string $date): bool;

    /**
     * Validate Jalali Date
     * 
     * @param string $date
     * 
     * @return bool
     */
    public function validateJalali(string $date): bool;

    /**
     * Validate Hijri Date
     * 
     * @param string $date
     * 
     * @return bool
     */
    public function validateHijri(string $date): bool;

    /**
     * Get timezones list
     * 
     * @return array
     */
    public function getTimezones(): array;

    /**
     * Validate timezone
     * 
     * @param string $timezone
     * 
     * @return bool
     */
    public function validateTimezone(string $timezone): bool;
}
