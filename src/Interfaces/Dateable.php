<?php

namespace Fooino\Core\Interfaces;

use DateTimeZone;

interface Dateable
{
    /**
     * Convert date base on timezone and the format you desire.
     *
     * @param string|int|null $date
     * @param string $format
     * @param DateTimeZone|string $from
     * @param DateTimeZone|string $to
     * @param string $fallback
     * @param bool $throwException
     * 
     * @throws \Fooino\Core\Exceptions\CanNotConvertDateException
     * 
     * @return string
     */
    public function convert(string|int|null $date, string $format = 'Y-m-d H:i:s', DateTimeZone|string $from = 'UTC', DateTimeZone|string $to = 'UTC', string $fallback = '', bool $throwException = false): string;

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
