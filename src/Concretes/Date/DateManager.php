<?php

namespace Fooino\Core\Concretes\Date;

use Fooino\Core\Enums\DateType;
use Fooino\Core\Exceptions\CanNotConvertDateException;
use DateTimeZone;
use Exception;

class DateManager extends DateHandler
{
    /**
     * Convert date base on timezone and the format you desire.
     *
     * @param  string|null  $date
     * @param  string  $format
     * @param  DateTimeZone  $from
     * @param  DateTimeZone  $to
     * @param bool $throwException
     * 
     * @return string|Exception
     */
    public function convert(
        string|null $date,
        string $format = 'Y-m-d H:i:s',
        DateTimeZone $from = new DateTimeZone('UTC'),
        DateTimeZone $to = new DateTimeZone('UTC'),
        bool $throwException = false
    ): string|Exception {

        try {

            $date = emptyToNullOrValue(value: $date);

            throw_if(
                ($throwException && (\is_null($date) || blank($date))),
                CanNotConvertDateException::class,
                'Can not convert date. The date is empty'
            );

            $date = replaceSlashToDash(value: (string) $date);

            if (blank($date)) return ''; // since we checked the throwException before, we return empty string

            $methods = $this->detectConvertMethodByTimezone(
                from: $from,
                to: $to
            );

            foreach ($methods as $method) {
                $date = $this->{$method}(
                    date: $date,
                    format: $format,
                    from: $from,
                    to: $to
                );
            }

            return $date;

            // 
        } catch (Exception $e) {

            $log = 'CAN-NOT-CONVERT-' . $date . '-FROM-' . $from->getName() . '-TO-' . $to->getName() . '-IN-' . $format . '-FORMAT-ERROR: ' . $e->getMessage() . ' ' . $e;

            logger()->error($log);

            throw_if(
                $throwException,
                CanNotConvertDateException::class,
                'Can not convert date. ' . $e->getMessage()
            );

            return '';
        }
    }

    /**
     * Validate date base on type you set.
     *
     * @param  string  $date
     * @param  DateType  $type
     * 
     * @return bool
     */
    public function validate(
        string $date,
        DateType $type = DateType::GREGORIAN
    ): bool {

        $date = replaceSlashToDash(value: (string) $date);

        $isDateAndTime = \preg_match(
            pattern: '/^[\d]{4}[-\/][\d]{2}[-\/][\d]{2} [\d]{2}:[\d]{2}:[\d]{2}$/',
            subject: $date
        );

        return (bool)$this->{'validate' . \ucfirst(str(\strtolower($type->value))->camel()->value())}(
            date: $date,
            isDateAndTime: $isDateAndTime
        );
    }

    private function detectConvertMethodByTimezone(
        DateTimeZone $from = new DateTimeZone('UTC'),
        DateTimeZone $to = new DateTimeZone('UTC')
    ): array {

        $methods = [];
        $fromMethod = $this->getTimezoneConvertMethod(timezone: $from);
        $toMethod   = $this->getTimezoneConvertMethod(timezone: $to);

        if (
            $fromMethod != 'UTC' &&
            $toMethod != 'UTC'
        ) {


            $methods[] = $fromMethod . 'ToUTC';
            $methods[] = 'UTCTo' .  \ucfirst(str($toMethod)->camel()->value());

            // 
        } else {
            $methods[] = $fromMethod . 'To' . \ucfirst(str($toMethod)->camel()->value());
        }


        return $methods;
    }

    private function getTimezoneConvertMethod(DateTimeZone $timezone): string
    {
        return match ($timezone->getName()) {
            'Asia/Tehran'           => 'shamsi',
            'Asia/Kabul'            => 'shamsi',
            // 'Asia/Muscat'           => 'hijri',
            // 'Asia/Riyadh'           => 'hijri',
            // 'Asia/Dubai'            => 'hijri',
            'UTC'                   => 'UTC',
            default                 => 'gregorian',
        };
    }
}
