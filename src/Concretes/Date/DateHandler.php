<?php


namespace Fooino\Core\Concretes\Date;

use Morilog\Jalali\Jalalian;
use Morilog\Jalali\CalendarUtils;
use Illuminate\Support\Facades\Validator;
use Assert\Assertion;
use DateTime;
use DateTimeZone;
use Exception;
use IntlDateFormatter;

class DateHandler
{
    protected function UTCToShamsi(
        string|null $date,
        string $format = 'Y-m-d H:i:s',
        DateTimeZone $from = new DateTimeZone('UTC'),
        DateTimeZone $to = new DateTimeZone('Asia/Tehran')
    ): string {

        $dateToTime = \strtotime((string) $this->parseDate(date: $date));

        throw_if(
            !\is_numeric($dateToTime),
            Exception::class,
            'strtotime can not convert date to timestamp'
        );

        $converted = Jalalian::forge(
            timestamp: $dateToTime,
            timeZone: $to
        )
            ->format(
                format: $format
            );

        return (string) $converted;
    }


    protected function shamsiToUTC(
        string|null $date,
        string $format = 'Y-m-d H:i:s',
        DateTimeZone $from = new DateTimeZone('Asia/Tehran'),
        DateTimeZone $to = new DateTimeZone('UTC')
    ): string {

        $date = $this->parseDate(date: $date);

        throw_if(
            \is_null($date),
            Exception::class,
            'The date is empty'
        );

        $parts = \explode(" ", $date);
        $datePart = $parts[0] ?? null;
        $timePart = $parts[1] ?? null;
        $baseFormat = filled($timePart) ? 'Y-m-d H:i:s' : 'Y-m-d';

        $converted = CalendarUtils::createCarbonFromFormat(
            format: $baseFormat,
            str: $date
        )
            ->format(format: (filled($timePart) ? $baseFormat : $format));

        // since Morilog package does not convert the time we change the timezone to utc if the user asks for time part
        if (
            filled($timePart)
        ) {
            $converted = (new DateTime(
                datetime: $converted,
                timezone: $from
            ))
                ->setTimezone(new DateTimeZone('UTC'))
                ->format(format: $format);
        }

        return (string) $converted;
    }

    protected function UTCToHijri(
        string|null $date,
        string $format = 'Y-m-d H:i:s',
        DateTimeZone $from = new DateTimeZone('UTC'),
        DateTimeZone $to = new DateTimeZone('Asia/Riyadh')
    ): string {

        $date = $this->parseDate(date: $date);

        throw_if(
            \is_null($date),
            Exception::class,
            'The date is empty'
        );

        $dateToTime = \strtotime($date);

        throw_if(
            !\is_numeric($dateToTime),
            Exception::class,
            'strtotime can not convert date to timestamp'
        );

        $pattern = $this->convertFormatToPattern(format: $format);

        $hijriDate = IntlDateFormatter::create(
            locale: 'en_US@calendar=islamic-civil',
            dateType: IntlDateFormatter::FULL,
            timeType: IntlDateFormatter::FULL,
            timezone: $to,
            calendar: IntlDateFormatter::TRADITIONAL,
            pattern: $pattern
        );

        return $hijriDate->format($dateToTime);
    }

    protected function hijriToUTC(
        string|null $date,
        string $format = 'Y-m-d H:i:s',
        DateTimeZone $from = new DateTimeZone('Asia/Riyadh'),
        DateTimeZone $to = new DateTimeZone('UTC')
    ): string {

        $date = $this->parseDate(date: $date);

        throw_if(
            \is_null($date),
            Exception::class,
            'The date is empty'
        );

        $parts = \explode(' ', $date);
        $datePart = $parts[0];
        $timePart = $parts[1] ?? null;
        $dateParts = \explode('-', $datePart);
        $year = $dateParts[0];
        $month = $dateParts[1];
        $day = $dateParts[2];

        $julianDay = \floor((11 * $year + 3) / 30) + \floor(354 * $year) + \floor(30 * $month) - \floor(($month - 1) / 2) + $day + 1948440 - 385;

        $date = $this->parseDate(\jdtogregorian($julianDay) . ' ' . $timePart);

        $date = date($format, \strtotime($date));

        if (
            filled($timePart)
        ) {
            $date = (new DateTime(datetime: $date, timezone: $from))
                ->setTimezone(new DateTimeZone('UTC'))
                ->format(format: $format);
        }

        return $date;
    }

    protected function gregorianToUTC(
        string|null $date,
        string $format = 'Y-m-d H:i:s',
        DateTimeZone $from = new DateTimeZone('America/New_York'),
        DateTimeZone $to = new DateTimeZone('UTC')
    ): string {
        return (new DateTime(
            datetime: $date,
            timezone: $from
        ))
            ->setTimezone(timezone: (new DateTimeZone('UTC')))
            ->format(format: $format);
    }

    protected function UTCToGregorian(
        string|null $date,
        string $format = 'Y-m-d H:i:s',
        DateTimeZone $from = new DateTimeZone('UTC'),
        DateTimeZone $to = new DateTimeZone('America/New_York'),
    ): string {
        return (new DateTime(
            datetime: $date,
            timezone: new DateTimeZone('UTC')
        ))
            ->setTimezone(timezone: $to)
            ->format(format: $format);
    }

    protected function UTCToUTC(
        string|null $date,
        string $format = 'Y-m-d H:i:s',
        DateTimeZone $from = new DateTimeZone('UTC'),
        DateTimeZone $to = new DateTimeZone('UTC')
    ): string {

        $date = $this->parseDate(date: $date);

        throw_if(
            \is_null($date),
            Exception::class,
            'The date is empty'
        );

        return date($format, \strtotime($date));
    }


    protected function validateShamsi(
        string $date,
        bool $isDateAndTime
    ): bool {

        $section = \explode(" ", $date);

        try {

            $dateSection = \explode("-", $section[0]);
            $year =  (int) $dateSection[0];
            $month = (int) $dateSection[1];
            $day =   (int) $dateSection[2];

            Assertion::between($year, 0, 1000000);
            Assertion::between($month, 1, 12);
            Assertion::between($day, 1, 31);

            if ($month > 6) {
                Assertion::between($day, 1, 30);
            }

            if (
                !CalendarUtils::isLeapJalaliYear($year) && $month === 12
            ) {
                Assertion::between($day, 1, 29);
            }

            //
        } catch (Exception $e) {
            return false;
        }

        if ($isDateAndTime) {
            try {
                $timeSection = \explode(":", $section[1]);

                $hour =   (int) $timeSection[0];
                $minute = (int) $timeSection[1];
                $second = (int) $timeSection[2];

                Assertion::between($hour, 0, 24);
                Assertion::between($minute, 0, 59);
                Assertion::between($second, 0, 59);

                //
            } catch (Exception $e) {
                return false;
            }
        }

        return true;
    }

    protected function validateHijri(
        string $date,
        bool $isDateAndTime
    ): bool {

        $section = \explode(" ", $date);

        try {
            $dateSection = \explode("-", $section[0]);
            $year =  (int) $dateSection[0];
            $month = (int) $dateSection[1];
            $day =   (int) $dateSection[2];

            Assertion::between($year, 0, 1000000);
            Assertion::between($month, 1, 12);
            Assertion::between($day, 1, 31);

            //
        } catch (Exception $e) {
            return false;
        }

        if ($isDateAndTime) {
            try {
                $timeSection = \explode(":", $section[1]);

                $hour =   (int) $timeSection[0];
                $minute = (int) $timeSection[1];
                $second = (int) $timeSection[2];

                Assertion::between($hour, 0, 24);
                Assertion::between($minute, 0, 59);
                Assertion::between($second, 0, 59);

                //
            } catch (Exception $e) {
                return false;
            }
        }

        return true;
    }

    protected function validateGregorian(
        string $date,
        bool $isDateAndTime
    ): bool {

        $validator = Validator::make(
            ['date' => $date],
            ['date' => "date_format:" . ($isDateAndTime ? 'Y-m-d H:i:s' : 'Y-m-d')]
        );
        return !$validator->fails();
    }

    private function convertFormatToPattern(string $format): string
    {
        $replaces = [
            // year
            ' y '   => ' yy ',
            'y-'    => 'yy-',
            'y/'    => 'yy/',

            ' Y '   => ' yyyy ',
            'Y-'    => 'yyyy-',
            'Y/'    => 'yyyy/',

            // month
            ' M '   => ' MMM ',
            'M-'    => 'MMM-',
            'M/'    => 'MMM/',

            ' m '   => ' MM ',
            'm-'    => 'MM-',
            'm/'    => 'MM/',

            // day
            ' d '   => ' dd ',
            'd'     => 'dd',

            // hour
            ' H '   => ' HH ',
            'H:'    => 'HH:',

            ' h '   => ' hh ',
            'h:'    => 'hh:',

            // minute
            ' i '   => ' mm ',
            ':i'    => ':mm',

            // seconds
            ' s '   => ' ss ',
            ':s'    => ':ss',
        ];

        foreach ($replaces as $key => $intl) {
            $format = \str_replace($key, $intl, $format);
        }
        return $format;
    }

    /**
     * Parses the given date string and formats it with zero-padded year, month, and day.
     * Combines the date with zero-padded hour, minute, and second if available.
     *
     * @param string|null $date The date string to parse and format.
     * @return string|null The formatted date string or null if the date parts are invalid.
     */
    private function parseDate(string|null $date): string|null
    {
        if (\is_null($date)) return $date;

        $parts  = \date_parse(\trim($date));
        $year   = $parts['year'];
        $month  = $parts['month'];
        $day    = $parts['day'];
        $hour   = $parts['hour'];
        $minute = $parts['minute'];
        $second = $parts['second'];

        if (
            $year == false  &&
            $month == false &&
            $day == false
        ) {
            return null;
        }

        $date = $this->addZeroToBeginning(value: $year) . '-' . $this->addZeroToBeginning(value: $month) . '-' . $this->addZeroToBeginning(value: $day);
        if (
            $hour != false  ||
            $minute != false ||
            $second != false
        ) {
            $date .= ' ' . $this->addZeroToBeginning(value: $hour) . ':' . $this->addZeroToBeginning(value: $minute) . ':' . $this->addZeroToBeginning(value: $second);
        }

        return $date;
    }

    /**
     * Adds a zero at the beginning of the value if the length is 1.
     *
     * @param string|false|int|null $value The value to add a zero to.
     * @return string The value with a zero added at the beginning.
     */
    private function addZeroToBeginning(string|false|int|null $value): string
    {
        $value = (int) $value;

        if (strlen($value) == 1) $value = '0' . $value;

        return $value;
    }
}
