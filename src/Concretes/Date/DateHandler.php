<?php


namespace Fooino\Core\Concretes\Date;

use Fooino\Core\Exceptions\CanNotConvertDateException;
use Morilog\Jalali\Jalalian;
use Morilog\Jalali\CalendarUtils;
use Illuminate\Support\Facades\Validator;
use Assert\Assertion;
use DateTime;
use DateTimeZone;
use IntlDateFormatter;
use Exception;

class DateHandler
{
    protected array $validTimezones = [];

    protected array $validatedTimezones = [];

    protected array $dateTimeZones = [];

    /**
     * Get timezones list
     */
    public function getTimezones(): array
    {
        if (is_null($this->validTimezones[0] ?? null)) {

            $this->validTimezones = DateTimeZone::listIdentifiers();
        }

        return $this->validTimezones;
    }

    /**
     * Validate timezone
     */
    public function validateTimezone(string $timezone): bool
    {
        return $this->validatedTimezones[$timezone] ??= in_array($timezone, $this->getTimezones());
    }

    /**
     * Convert date from UTC to Jalali timezone
     */
    protected function UTCToJalali(
        string|null $date,
        string $format = 'Y-m-d H:i:s',
        DateTimeZone|string $from = 'UTC',
        DateTimeZone|string $to = 'Asia/Tehran'
    ): string {

        return (string) Jalalian::forge(
            timestamp: \strtotime($this->standardize(date: $date, timezone: $this->getDateTimeZone(timezone: $from))),
            timeZone: $this->getDateTimeZone(timezone: $to)
        )
            ->format(format: $format);
    }


    protected function jalaliToUTC(
        string|null $date,
        string $format = 'Y-m-d H:i:s',
        DateTimeZone|string $from = 'Asia/Tehran',
        DateTimeZone|string $to = 'UTC'
    ): string {

        $date = $this->standardize(date: $date, timezone: $this->getDateTimeZone($from));
        $hasTimePart = $this->hasTimePart(date: $date);
        $baseFormat = $hasTimePart ? 'Y-m-d H:i:s' : 'Y-m-d';

        $converted = CalendarUtils::createCarbonFromFormat(format: $baseFormat, str: $date)->format(format: $hasTimePart ? $baseFormat : $format);

        // since Morilog package does not convert the time we change the timezone to utc if the user asks for time part
        if ($hasTimePart) {

            $converted = (new DateTime(
                datetime: $converted,
                timezone: $from
            ))
                ->setTimezone($this->getDateTimeZone('UTC'))
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

        $date = $this->standardize(date: $date, timezone: $this->getDateTimeZone($from));

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

        $date = $this->standardize(date: $date, timezone: $this->getDateTimeZone($from));

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

        $date = $this->standardize(date: \jdtogregorian($julianDay) . ' ' . $timePart, timezone: $this->getDateTimeZone($from));

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
        DateTimeZone|string $from = 'America/New_York',
        DateTimeZone|string $to = 'UTC'
    ): string {

        return (new DateTime(
            datetime: $this->standardize(date: $date, timezone: $this->getDateTimeZone($from)),
            timezone: $this->getDateTimeZone($from)
        ))
            ->setTimezone(timezone: $this->getDateTimeZone('UTC'))
            ->format(format: $format);
    }

    protected function UTCToGregorian(
        string|null $date,
        string $format = 'Y-m-d H:i:s',
        DateTimeZone|string $from = new DateTimeZone('UTC'),
        DateTimeZone|string $to = new DateTimeZone('America/New_York'),
    ): string {

        return (new DateTime(
            datetime: $this->standardize(date: $date, timezone: $this->getDateTimeZone($from)),
            timezone: $this->getDateTimeZone('UTC')
        ))
            ->setTimezone(timezone: $this->getDateTimeZone($to))
            ->format(format: $format);
    }

    protected function UTCToUTC(
        string|null $date,
        string $format = 'Y-m-d H:i:s',
        DateTimeZone|string $from = 'UTC',
        DateTimeZone|string $to = 'UTC'
    ): string {

        return date($format, \strtotime($this->standardize(date: $date, timezone: $this->getDateTimeZone($from))));
    }


    public function validateJalali(string $date): bool
    {
        try {

            list($year, $month, $day, $hour, $minute, $second) = $this->parseDate(date: $date);

            Assertion::between((int)$year, 1, 10000);
            Assertion::between((int)$month, 1, 12);
            Assertion::between((int)$day, 1, 31);

            if ($month > 6) {
                Assertion::between((int)$day, 1, 30);
            }

            if (
                !CalendarUtils::isLeapJalaliYear($year) && $month === 12
            ) {
                Assertion::between((int)$day, 1, 29);
            }

            return $this->validateTime((int)$hour, (int)$minute, (int)$second);

            //
        } catch (Exception $e) {
            return false;
        }
    }

    public function validateHijri(string $date): bool
    {
        try {

            list($year, $month, $day, $hour, $minute, $second) = $this->parseDate(date: $date);

            Assertion::between((int)$year, 1, 10000);
            Assertion::between((int)$month, 1, 12);
            Assertion::between((int)$day, 1, 31);

            return $this->validateTime((int)$hour, (int)$minute, (int)$second);

            //
        } catch (Exception $e) {
            return false;
        }
    }

    public function validateGregorian(string $date): bool
    {
        $date = $this->standardize(date: $date);

        $validator = Validator::make(
            ['date' => $date],
            ['date' => "date_format:" . ($this->hasTimePart(date: $date) ? 'Y-m-d H:i:s' : 'Y-m-d')]
        );
        return !$validator->fails();
    }

    private function validateTime(int $hour, int $minute, int $second): bool
    {
        return ($hour >= 0 && $hour < 24) && ($minute >= 0 && $minute <= 59) && ($second >= 0 && $second <= 59);
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



    public function dateParts(string $date): array
    {
        $parts = \explode(" ", $date);

        return [
            $parts[0] ?? null,
            $parts[1] ?? null
        ];
    }

    public function hasTimePart(string $date): bool
    {
        return filled($this->dateParts($date)[1]);
    }

    /**
     * Standardize given date to Y-m-d H:i:s format
     *
     * @throws \Fooino\Core\Exceptions\CanNotConvertDateException when non parts of date is valid
     */
    protected function standardize(string|null $date, DateTimeZone $timezone): string
    {
        list($year, $month, $day, $hour, $minute, $second) = $this->parseDate(date: (string)$date);

        $timePart = '';
        $datePart = $year . '-' . $month . '-' . $day;

        if (
            $hour != '00' ||
            $minute != '00' ||
            $second != '00'
        ) {
            $timePart = $hour . ':' . $minute . ':' . $second;
        }

        if (
            $datePart == '00-00-00' &&
            (blank($timePart) || $timePart == '00:00:00')
        ) {
            app(CanNotConvertDateException::class)
                ->setMessage('msg.canNotConvertDateExceptionInvalidDate')
                ->setCode(10053)
                ->error()
                ->shouldReport()
                ->throw();
        }

        if ($datePart == '00-00-00') {

            if ($this->getCalendarTypeByTimezone($timezone) == 'jalali') {

                $datePart = Jalalian::forge(timestamp: \strtotime(date('Y-m-d')), timeZone: $timezone)->format('Y-m-d');

                // 
            } else {

                $datePart = (new DateTime(
                    datetime: date('Y-m-d'),
                    timezone: $this->getDateTimeZone('UTC')
                ))
                    ->setTimezone(timezone: $timezone)
                    ->format(format: 'Y-m-d');
            }
        }

        $date = trim($datePart . ' ' . $timePart);

        return $date;
    }

    /**
     * Parse date with date_parse php function
     */
    protected function parseDate(string $date): array
    {
        $parsed  = \date_parse(\trim($date));

        return [
            $this->addZeroToBeginning($parsed['year']),
            $this->addZeroToBeginning($parsed['month']),
            $this->addZeroToBeginning($parsed['day']),
            $this->addZeroToBeginning($parsed['hour']),
            $this->addZeroToBeginning($parsed['minute']),
            $this->addZeroToBeginning($parsed['second']),
        ];
    }

    /**
     * Adds a zero at the beginning of the value if the length is 1.
     */
    protected function addZeroToBeginning(string|false|int|null $value): string
    {
        $value = (int) $value;

        if (strlen($value) == 1) $value = '0' . $value;

        return $value;
    }

    /**
     * Make DateTimeZone object if the timezone is string.
     *
     * @throws \Fooino\Core\Exceptions\CanNotConvertDateException when the timezone is invalid
     */
    protected function getDateTimeZone(DateTimeZone|string $timezone): DateTimeZone
    {
        if (is_string($timezone)) {

            if (!is_null($this->dateTimeZones[$timezone] ?? null)) {

                return $this->dateTimeZones[$timezone];
            }

            if (!$this->validateTimezone(timezone: $timezone)) {

                app(CanNotConvertDateException::class)
                    ->setMessage('msg.canNotConvertDateExceptionInvalidTimezone')
                    ->setCode(10051)
                    ->error()
                    ->shouldReport()
                    ->with([
                        'invalid_timezone'  => $timezone
                    ])
                    ->throw();
            }

            return $this->dateTimeZones[$timezone] ??= new DateTimeZone($timezone);
        }

        return $timezone;
    }

    /**
     * Get calendar type base on timezone
     */
    protected function getCalendarTypeByTimezone(DateTimeZone $timezone): string
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
