<?php

namespace Fooino\Core\Concretes\Date;

use Fooino\Core\Interfaces\Dateable;
use Fooino\Core\Exceptions\CanNotConvertDateException;
use DateTimeZone;
use Exception;

class FooinoDateHandler extends DateHandler implements Dateable
{
    public function convert(
        string|null $date,
        string $format = 'Y-m-d H:i:s',
        DateTimeZone|string $from = 'UTC',
        DateTimeZone|string $to = 'UTC',
        string $fallback = '',
        bool $throwException = false
    ): string {

        $date = nullIfBlank(value: replaceSlashToDash(value: (string) $date));

        if (
            !$throwException &&
            blank($date)
        ) {
            return $fallback;
        };

        try {

            $from = $this->getDateTimeZone(timezone: $from);
            $to = $this->getDateTimeZone(timezone: $to);

            if (
                $throwException &&
                blank($date)
            ) {
                app(CanNotConvertDateException::class)
                    ->setMessage('msg.canNotConvertDateExceptionTheDateIsEmpty')
                    ->setCode(10052)
                    ->throw();
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
                    ->setMessage($e->getMessage())
                    ->setCode($e->getCode())
                    ->setLevel(callMethodIfExists(object: $e, method: 'getLevel', fallback: 'error'))
                    ->report(callMethodIfExists(object: $e, method: 'reportable', fallback: true))
                    ->with(array_merge(
                        callMethodIfExists(object: $e, method: 'getWith', fallback: []),
                        [
                            'date'      => $date,
                            'format'    => $format,
                            'from'      => is_string($from) ? $from : $from->getName(),
                            'to'        => is_string($to)   ? $to   : $to->getName(),
                        ]
                    ))
                    ->throw();
            }

            return $fallback;
        }
    }

    private function chainMethods(DateTimeZone $from, DateTimeZone $to): array
    {
        $methods = [];
        $fromMethod = $this->getChainMethodByTimezone(timezone: $from);
        $toMethod   = $this->getChainMethodByTimezone(timezone: $to);

        if (
            $fromMethod != 'UTC' &&
            $toMethod != 'UTC'
        ) {


            $methods[] = $fromMethod . 'ToUTC';
            $methods[] = 'UTCTo' .  \ucfirst($toMethod);

            // 
        } else {
            $methods[] = $fromMethod . 'To' . \ucfirst($toMethod);
        }

        return $methods;
    }

    private function getChainMethodByTimezone(DateTimeZone $timezone): string
    {
        return match ($timezone->getName()) {
            'Asia/Tehran'           => 'jalali',
            'Asia/Kabul'            => 'jalali',
            // 'Asia/Muscat'           => 'hijri',
            // 'Asia/Riyadh'           => 'hijri',
            // 'Asia/Dubai'            => 'hijri',
            'UTC'                   => 'UTC',
            default                 => 'gregorian',
        };
    }
}
