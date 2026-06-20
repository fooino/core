<?php


namespace Fooino\Core\Concretes\Date;

use Fooino\Core\Exceptions\CanNotConvertDateException;
use Morilog\Jalali\Jalalian;
use Morilog\Jalali\CalendarUtils;
use DateTime;
use DateTimeZone;
use IntlDateFormatter;
use IntlCalendar;

abstract class DateHandler
{
    protected array $instances = [];

    protected const string OFFICIAL   = 'OFFICIAL';

    protected const string UNOFFICIAL = 'UNOFFICIAL';

    protected array $validTimezones = [];

    protected array $validatedTimezones = [];

    protected array $dateTimeZones = [];

    public function __construct(protected string $calendarUsage = self::OFFICIAL) {}

    /**
     * Get the current calendar mode: official (government-set) or unofficial (religious/cultural)
     */
    public function getCalendarUsage(): string
    {
        return $this->calendarUsage;
    }

    /**
     * Switch to the official calendar (government-set) for date conversions
     */
    public function officialCalendar(): static
    {
        return $this->instances[self::OFFICIAL] ??= (new static(calendarUsage: self::OFFICIAL));
    }

    /**
     * Switch to the unofficial calendar (religious/cultural) for date conversions
     */
    public function unofficialCalendar(): static
    {
        return $this->instances[self::UNOFFICIAL] ??= (new static(calendarUsage: self::UNOFFICIAL));
    }

    /**
     * Get all supported timezone identifiers, cached after the first call
     */
    public function getTimezones(): array
    {
        if (is_null($this->validTimezones[0] ?? null)) {

            $this->validTimezones = DateTimeZone::listIdentifiers();
        }

        return $this->validTimezones;
    }

    /**
     * Check whether a timezone string is a valid PHP timezone identifier, with caching
     */
    public function validateTimezone(string $timezone): bool
    {
        return $this->validatedTimezones[$timezone] ??= in_array($timezone, $this->getTimezones());
    }

    /**
     * Convert a UTC datetime string to the Jalali (Solar Hijri) calendar used in Iran and Afghanistan
     */
    protected function UTCToJalali(
        string|null $date,
        string $format = STANDARD_DATE_TIME_FORMAT,
        DateTimeZone|string $from = 'UTC',
        DateTimeZone|string $to = 'Asia/Tehran'
    ): string {

        return Jalalian::forge(
            timestamp: strtotime($this->normalize(date: $date, timezone: $this->getDateTimeZone(timezone: 'UTC'))),
            timeZone: $this->getDateTimeZone(timezone: $to)
        )
            ->format(format: $format);
    }

    /**
     * Convert a Jalali (Solar Hijri) datetime string back to UTC
     */
    protected function jalaliToUTC(
        string|null $date,
        string $format = STANDARD_DATE_TIME_FORMAT,
        DateTimeZone|string $from = 'Asia/Tehran',
        DateTimeZone|string $to = 'UTC'
    ): string {

        $date = $this->normalize(date: $date, timezone: $this->getDateTimeZone(timezone: $from));

        $hasTimePart = $this->hasTimePart(date: $date);

        $baseFormat = $hasTimePart ? STANDARD_DATE_TIME_FORMAT : STANDARD_DATE_FORMAT;

        $converted = CalendarUtils::createCarbonFromFormat(
            format: $baseFormat,
            str: $date
        )
            ->format(format: $hasTimePart ? $baseFormat : $format);

        // since Morilog package does not convert the time we change the timezone to UTC if the user asks for time part
        if ($hasTimePart) {

            $converted = (new DateTime(
                datetime: $converted,
                timezone: $this->getDateTimeZone(timezone: $from)
            ))
                ->setTimezone(timezone: $this->getDateTimeZone(timezone: 'UTC'))
                ->format(format: $format);
        }

        return $converted;
    }

    /**
     * Convert a UTC datetime string to the Islamic (Lunar Hijri) calendar used in Middle Eastern countries
     */
    protected function UTCToHijri(
        string|null $date,
        string $format = STANDARD_DATE_TIME_FORMAT,
        DateTimeZone|string $from = 'UTC',
        DateTimeZone|string $to = 'Asia/Riyadh'
    ): string {

        $date = $this->normalize(date: $date, timezone: $this->getDateTimeZone(timezone: 'UTC'));

        $locale = $this->getDateTimeZone(timezone: $to)->getName() == 'Asia/Riyadh' ? 'en@calendar=islamic-umalqura' : 'en@calendar=islamic-civil';

        $islamicCal = IntlCalendar::createInstance(
            timezone: $this->getDateTimeZone(timezone: $to),
            locale: $locale
        );

        $islamicCal->setTime(timestamp: strtotime($date) * 1000);

        $formatter = new IntlDateFormatter(
            locale: $locale,
            dateType: IntlDateFormatter::FULL,
            timeType: IntlDateFormatter::FULL,
            timezone: $this->getDateTimeZone(timezone: $to),
            calendar: IntlDateFormatter::TRADITIONAL,
            pattern: $this->convertPhpDateFormatToICU(format: $format)
        );

        return $formatter->format(datetime: $islamicCal);
    }

    /**
     * Convert an Islamic (Lunar Hijri) datetime string back to UTC
     */
    protected function hijriToUTC(
        string|null $date,
        string $format = STANDARD_DATE_TIME_FORMAT,
        DateTimeZone|string $from = 'Asia/Riyadh',
        DateTimeZone|string $to = 'UTC'
    ): string {

        $date = $this->normalize(date: $date, timezone: $this->getDateTimeZone(timezone: $from));

        list($year, $month, $day, $hour, $minute, $second) = $this->parseDate(date: (string) $date);

        $locale = $this->getDateTimeZone(timezone: $from)->getName() == 'Asia/Riyadh' ? 'en@calendar=islamic-umalqura' : 'en@calendar=islamic-civil';

        $hijriCalendar = IntlCalendar::createInstance(
            $this->getDateTimeZone(timezone: $from),
            $locale
        );

        $hijriCalendar->set(
            year: $year,
            month: $month - 1, // Subtract 1 because of the 0-based index!
            dayOfMonth: $day === false ? null : $day,
            hour: $hour === false ? null : $hour,
            minute: $minute === false ? null : $minute,
            second: $second === false ? null : $second
        );

        $timestamp = (int)($hijriCalendar->getTime() / 1000);

        return (new DateTime(datetime: date($format, $timestamp)))
            ->setTimezone(timezone: $this->getDateTimeZone(timezone: 'UTC'))
            ->format(format: $format);
    }

    /**
     * Convert a Gregorian datetime string from a specific timezone to UTC
     */
    protected function gregorianToUTC(
        string|null $date,
        string $format = STANDARD_DATE_TIME_FORMAT,
        DateTimeZone|string $from = 'America/New_York',
        DateTimeZone|string $to = 'UTC'
    ): string {

        return (new DateTime(
            datetime: $this->normalize(date: $date, timezone: $this->getDateTimeZone(timezone: $from)),
            timezone: $this->getDateTimeZone(timezone: $from)
        ))
            ->setTimezone(timezone: $this->getDateTimeZone(timezone: 'UTC'))
            ->format(format: $format);
    }

    /**
     * Convert a UTC datetime string to a Gregorian timezone
     */
    protected function UTCToGregorian(
        string|null $date,
        string $format = STANDARD_DATE_TIME_FORMAT,
        DateTimeZone|string $from = 'UTC',
        DateTimeZone|string $to = 'America/New_York',
    ): string {

        return (new DateTime(
            datetime: $this->normalize(date: $date, timezone: $this->getDateTimeZone(timezone: 'UTC')),
            timezone: $this->getDateTimeZone(timezone: 'UTC')
        ))
            ->setTimezone(timezone: $this->getDateTimeZone(timezone: $to))
            ->format(format: $format);
    }

    /**
     * Re-format a UTC datetime string without changing the timezone
     */
    protected function UTCToUTC(
        string|null $date,
        string $format = STANDARD_DATE_TIME_FORMAT,
        DateTimeZone|string $from = 'UTC',
        DateTimeZone|string $to = 'UTC'
    ): string {

        return date($format, \strtotime($this->normalize(date: $date, timezone: $this->getDateTimeZone(timezone: 'UTC'))));
    }

    /**
     * Convert a PHP date() format string to an ICU/Unicode date pattern for the IntlDateFormatter
     */
    protected function convertPhpDateFormatToICU(string $format): string
    {
        /** Mapping from PHP date() specifier → ICU pattern.
         *  Order matters: longer keys (like escaped ones) are processed first.
         *  Escaped characters – ICU requires them to be wrapped in single quotes.
         *  These mappings preserve literal characters that should not be interpreted.
         */
        $map = [
            '\d' => "'d'",
            '\D' => "'D'",
            '\j' => "'j'",
            '\l' => "'l'",
            '\N' => "'N'",
            '\S' => "'S'",
            '\w' => "'w'",
            '\z' => "'z'",
            '\W' => "'W'",
            '\F' => "'F'",
            '\m' => "'m'",
            '\M' => "'M'",
            '\n' => "'n'",
            '\t' => "'t'",
            '\L' => "'L'",
            '\o' => "'o'",
            '\Y' => "'Y'",
            '\y' => "'y'",
            '\a' => "'a'",
            '\A' => "'A'",
            '\B' => "'B'",
            '\g' => "'g'",
            '\G' => "'G'",
            '\h' => "'h'",
            '\H' => "'H'",
            '\i' => "'i'",
            '\s' => "'s'",
            '\u' => "'u'",
            '\e' => "'e'",
            '\I' => "'I'",
            '\O' => "'O'",
            '\P' => "'P'",
            '\T' => "'T'",
            '\Z' => "'Z'",
            '\c' => "'c'",
            '\r' => "'r'",

            // Day -----------------------------------------------------------------
            'd' => 'dd',      // Day of month, 2 digits with leading zeros (01–31)
            'D' => 'eee',     // Textual day, three letters (Mon–Sun)
            'j' => 'd',       // Day of month without leading zeros (1–31)
            'l' => 'eeee',    // Full textual day (Sunday–Saturday)
            'N' => 'e',       // ISO-8601 numeric day (1=Monday, 7=Sunday)
            'S' => '',        // Ordinal suffix (st, nd, rd, th) – not supported in ICU
            'w' => '',        // Numeric day of week (0=Sunday, 6=Saturday) – no ICU equivalent
            'z' => 'D',       // Day of year (0–365)

            // Week ----------------------------------------------------------------
            'W' => 'w',       // ISO-8601 week number of year

            // Month ---------------------------------------------------------------
            'F' => 'MMMM',    // Full month name (January–December)
            'm' => 'MM',      // Month with leading zeros (01–12)
            'M' => 'MMM',     // Short month name (Jan–Dec)
            'n' => 'M',       // Month without leading zeros (1–12)
            't' => '',        // Days in month (28–31) – not supported in ICU

            // Year ----------------------------------------------------------------
            'L' => '',        // Leap year indicator – no ICU equivalent
            'o' => 'Y',       // ISO-8601 year number
            'Y' => 'yyyy',    // 4-digit year
            'y' => 'yy',      // 2-digit year

            // Time ----------------------------------------------------------------
            'a' => 'a',       // Lowercase am/pm
            'A' => 'a',       // Uppercase AM/PM (ICU only has lowercase 'a')
            'B' => '',        // Swatch Internet time – not supported in ICU
            'g' => 'h',       // 12-hour hour without leading zeros (1–12)
            'G' => 'H',       // 24-hour hour without leading zeros (0–23)
            'h' => 'hh',      // 12-hour hour with leading zeros (01–12)
            'H' => 'HH',      // 24-hour hour with leading zeros (00–23)
            'i' => 'mm',      // Minutes with leading zeros (00–59)
            's' => 'ss',      // Seconds with leading zeros (00–59)
            'u' => '',        // Microseconds – no ICU equivalent (use 'A' for milliseconds)
            'e' => 'VV',      // Timezone identifier (e.g., Europe/Paris) – ICU uses 'VV'
            'I' => '',        // DST indicator – not supported in ICU
            'O' => 'Z',       // Difference to GMT in hours – ICU uses 'Z'
            'P' => 'ZZZZZ',   // Difference to GMT with colon – ICU uses 'ZZZZZ'
            'T' => 'zzzz',    // Timezone abbreviation – ICU uses 'zzzz'
            'Z' => 'XXXXX',   // Timezone offset in seconds – ICU uses 'XXXXX'

            // Full date/time -------------------------------------------------------
            'c' => "yyyy-MM-dd'T'HH:mm:ssXXX", // ISO 8601 date – ICU equivalent
            'r' => 'EEE, dd MMM yyyy HH:mm:ss Z', // RFC 2822 – ICU equivalent
        ];

        // Escape percent signs to prevent any unexpected interpretation.
        $format = str_replace('%', '%%', $format);

        // Replace each PHP specifier with its ICU equivalent.
        return strtr($format, $map);
    }

    /**
     * Split a date string into date and time parts separated by a space
     */
    protected function dateParts(string $date): array
    {
        $parts = \explode(" ", $date);

        return [
            $parts[0] ?? null,
            $parts[1] ?? null
        ];
    }

    /**
     * Determine whether the date string includes a time portion
     */
    protected function hasTimePart(string $date): bool
    {
        return !is_null($this->dateParts($date)[1]);
    }

    /**
     * Normalize a date to Y-m-d H:i:s format, using today's date when only a time is given
     */
    protected function normalize(string|null $date, DateTimeZone $timezone): string
    {
        list($year, $month, $day, $hour, $minute, $second) = $this->parseDate(date: (string) $date);

        $datePart = $this->addZeroToBeginning($year) . '-' . $this->addZeroToBeginning($month) . '-' . $this->addZeroToBeginning($day);
        $timePart = '';

        if (
            $hour   !== false ||
            $minute !== false ||
            $second !== false // since the timezone can change the day, add timePart when exists
        ) {
            $timePart = $this->addZeroToBeginning($hour) . ':' . $this->addZeroToBeginning($minute) . ':' . $this->addZeroToBeginning($second);
        }

        if (
            $datePart === '00-00-00' // the user want to just convert time part, so we make the datePart from now to have Y-m-d H:i:s
        ) {

            if ($this->getCalendarTypeByTimezone(timezone: $timezone) == 'jalali') {

                $datePart = Jalalian::forge(
                    timestamp: \strtotime(date(STANDARD_DATE_TIME_FORMAT)),
                    timeZone: $timezone
                )
                    ->format(format: STANDARD_DATE_FORMAT);

                // 
            } else {

                $datePart = (new DateTime(
                    datetime: date(STANDARD_DATE_TIME_FORMAT),
                    timezone: $this->getDateTimeZone(timezone: 'UTC')
                ))
                    ->setTimezone(timezone: $timezone)
                    ->format(format: STANDARD_DATE_FORMAT);
            }
        }

        $date = trim($datePart . ' ' . $timePart);

        return $date;
    }

    /**
     * Parse a date string into its components and throw if no valid part is found
     * 
     * @throws \Fooino\Core\Exceptions\CanNotConvertDateException with 1003 code
     */
    protected function parseDate(string $date): array
    {
        $parsed = date_parse(datetime: trim($date));

        $parsed = [
            $parsed['year'],
            $parsed['month'],
            $parsed['day'],
            $parsed['hour'],
            $parsed['minute'],
            $parsed['second']
        ];

        if (
            count(array_filter($parsed, fn($p) => $p !== false)) === 0 // non part of the date is valid
        ) {
            $this->throwInvalidDateException();
        }

        return $parsed;
    }

    /**
     * Pad single-digit values with a leading zero for consistent date string formatting
     */
    protected function addZeroToBeginning(string|false|int|null $value): string
    {
        $value = (int) $value;

        if (strlen($value) == 1) $value = '0' . $value;

        return $value;
    }

    /**
     * Resolve a timezone string to a DateTimeZone object, with caching and validation
     *
     * @throws \Fooino\Core\Exceptions\CanNotConvertDateException with 1001 code
     */
    protected function getDateTimeZone(DateTimeZone|string $timezone): DateTimeZone
    {
        if (is_string($timezone)) {

            if (!is_null($this->dateTimeZones[$timezone] ?? null)) {

                return $this->dateTimeZones[$timezone];
            }

            if (!$this->validateTimezone(timezone: $timezone)) {

                $this->throwInvalidTimezoneException(timezone: $timezone);
            }

            return $this->dateTimeZones[$timezone] ??= new DateTimeZone($timezone);
        }

        return $timezone;
    }

    /**
     * Determine which calendar system (jalali, hijri, gregorian, UTC) applies for a timezone and the current usage mode
     */
    protected function getCalendarTypeByTimezone(DateTimeZone $timezone): string
    {
        return $this->{'get' . ucfirst(strtolower($this->getCalendarUsage()) . 'CalendarTypeByTimezone')}(timezone: $timezone);
    }

    /**
     * Map timezones to their official calendar system (government-set)
     */
    protected function getOfficialCalendarTypeByTimezone(DateTimeZone $timezone): string
    {
        return match ($timezone->getName()) {

            'Asia/Tehran'           => 'jalali',    // +3:30
            'Asia/Kabul'            => 'jalali',    // +4:30

            'UTC'                   => 'UTC',
            default                 => 'gregorian',
        };
    }

    /**
     * Map timezones to their unofficial calendar system (religious/cultural)
     */
    protected function getUnofficialCalendarTypeByTimezone(DateTimeZone $timezone): string
    {
        return match ($timezone->getName()) {

            'Asia/Tehran'           => 'jalali',    // +3:30
            'Asia/Kabul'            => 'jalali',    // +4:30

            'Asia/Dubai'            => 'hijri',     // +4:00
            'Asia/Qatar'            => 'hijri',     // +3:00
            'Asia/Riyadh'           => 'hijri',     // +3:00
            'Asia/Muscat'           => 'hijri',     // +4:00
            'Asia/Bahrain'          => 'hijri',     // +3:00
            'Asia/Kuwait'           => 'hijri',     // +3:00
            'Asia/Baghdad'          => 'hijri',     // +3:00
            'Asia/Amman'            => 'hijri',     // +3:00
            'Asia/Beirut'           => 'hijri',     // +3:00
            'Asia/Damascus'         => 'hijri',     // +3:00
            'Asia/Aden'             => 'hijri',     // +3:00

            'UTC'                   => 'UTC',
            default                 => 'gregorian',
        };
    }

    protected function throwInvalidTimezoneException(string $timezone): never
    {
        app(CanNotConvertDateException::class)
            ->_1001()
            ->with([
                'invalid_timezone'  => $timezone
            ])
            ->throw();
    }

    protected function throwDateIsEmptyException(): never
    {
        app(CanNotConvertDateException::class)
            ->_1002()
            ->throw();
    }

    protected function throwInvalidDateException(): never
    {
        app(CanNotConvertDateException::class)
            ->_1003()
            ->throw();
    }
}
