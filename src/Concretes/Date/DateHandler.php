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
    protected array $dateTimeZones = [];

    public function getTimezones(): array
    {
        return once(fn() => DateTimeZone::listIdentifiers());
    }

    public function validateTimezone(string $timezone): bool
    {
        return $this->validTimezones[$timezone] ??= in_array($timezone, $this->getTimezones());
    }

    protected function UTCToJalali(
        string|null $date,
        string $format = 'Y-m-d H:i:s',
        DateTimeZone|string $from = 'UTC',
        DateTimeZone|string $to = 'Asia/Tehran'
    ): string {
        return (string) Jalalian::forge(timestamp: \strtotime($this->serializeDate(date: $date)), timeZone: $this->getDateTimeZone($to))->format(format: $format);
    }


    protected function jalaliToUTC(
        string|null $date,
        string $format = 'Y-m-d H:i:s',
        DateTimeZone|string $from = 'Asia/Tehran',
        DateTimeZone|string $to = 'UTC'
    ): string {

        $date = $this->serializeDate(date: $date);
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
        DateTimeZone|string $from = 'America/New_York',
        DateTimeZone|string $to = 'UTC'
    ): string {

        return (new DateTime(
            datetime: $this->serializeDate(date: $date),
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
            datetime: $this->serializeDate(date: $date),
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

        return date($format, \strtotime($this->serializeDate(date: $date)));
    }


    public function validateJalali(string $date): bool
    {
        try {

            list($year, $month, $day, $hour, $minute, $second) = $this->parsedDate(date: $date);

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

            list($year, $month, $day, $hour, $minute, $second) = $this->parsedDate(date: $date);

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
        $date = $this->serializeDate(date: $date);

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

    private function parsedDate(string $date): array
    {
        $parts  = \date_parse(\trim($date));

        return [
            $parts['year'],
            $parts['month'],
            $parts['day'],
            $parts['hour'],
            $parts['minute'],
            $parts['second'],
        ];
    }

    private function dateParts(string $date): array
    {
        $parts = \explode(" ", $date);

        return [
            $parts[0] ?? null,
            $parts[1] ?? null
        ];
    }

    private function hasTimePart(string $date): bool
    {
        return filled($this->dateParts($date)[1]);
    }

    private function serializeDate(string|null $date): string
    {
        $serializedDate = $this->parseDate(date: $date);

        app(CanNotConvertDateException::class)
            ->setMessage('msg.canNotConvertDateExceptionInvalidDate')
            ->setCode(10052)
            ->error()
            ->shouldReport()
            ->throwIf(condition: is_null($serializedDate));

        return $serializedDate;
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

        list($year, $month, $day, $hour, $minute, $second) = $this->parsedDate(date: $date);

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

    /**
     * Make \DateTimeZone object if the timezone is string.
     *
     * @param \DateTimeZone|string $timezone
     * 
     * @throws \Fooino\Core\Exceptions\CanNotConvertDateException when the timezone is invalid
     * 
     * @return \DateTimeZone
     */
    protected function getDateTimeZone(DateTimeZone|string $timezone): DateTimeZone
    {
        if (is_string($timezone)) {

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
}
