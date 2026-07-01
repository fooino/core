<?php

namespace Fooino\Core\Concretes\Date;

use Fooino\Core\Interfaces\Dateable;
use Fooino\Core\Exceptions\CanNotConvertDateException;
use DateTimeZone;
use Exception;

class FooinoDateHandler extends DateHandler implements Dateable
{
    /**
     * Convert a date between timezones and calendar systems, with fallback and exception control
     * 
     * @throws \Fooino\Core\Exceptions\CanNotConvertDateException
     */
    public function convert(
        string|int|null $date,
        string $format = STANDARD_DATE_TIME_FORMAT,
        DateTimeZone|string $from = 'UTC',
        DateTimeZone|string $to = 'UTC',
        string $fallback = '',
        bool $throwException = false
    ): string {

        $originalDate = $date;

        $date = is_numeric($date) ? date(STANDARD_DATE_TIME_FORMAT, $date) : $date;

        $date = nullIfBlank(value: replaceSlashWithDash(value: (string) $date));

        if (
            !$throwException &&
            blank($date)
        ) {
            return $fallback;
        };

        try {

            $from = $this->resolveTimezone(timezone: $from);
            $to = $this->resolveTimezone(timezone: $to);

            if (
                $throwException &&
                blank($date)
            ) {
                $this->throwDateIsEmptyException();
            }

            foreach ($this->chainMethods($from, $to) as $method) {

                $date = $this->{$method}(
                    date: $date,
                    format: $format,
                    from: $from,
                    to: $to
                );
            }

            return $date;

            // 
        } catch (CanNotConvertDateException | Exception $e) {

            if ($throwException) {

                app(CanNotConvertDateException::class)
                    ->from(
                        e: $e,
                        with: [
                            'original_date' => $originalDate,
                            'date'          => $date,
                            'format'        => $format,
                            'from'          => is_string($from) ? $from : $from->getName(),
                            'to'            => is_string($to)   ? $to   : $to->getName(),
                        ]
                    )
                    ->throw();
            }

            return $fallback;
        }
    }

    /**
     * Chain the methods that should be called in order
     */
    private function chainMethods(DateTimeZone $from, DateTimeZone $to): array
    {
        $fromMethod = $this->getCalendarTypeByTimezone(timezone: $from);

        $toMethod   = $this->getCalendarTypeByTimezone(timezone: $to);

        return ($fromMethod !== 'UTC' && $toMethod !== 'UTC') ? [$fromMethod . 'ToUTC', 'UTCTo' .  ucfirst($toMethod)] : [$fromMethod . 'To' . ucfirst($toMethod)];
    }
}
