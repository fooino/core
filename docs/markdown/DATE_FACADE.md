# Date Facade

Convert dates between timezones and calendar systems (Gregorian, Jalali, Hijri) with timezone validation and fluent calendar mode switching.

## Architecture

```
Dateable (Interface — Fooino\Core\Interfaces\Dateable)
  └── FooinoDateHandler (Concrete — Fooino\Core\Concretes\Date\FooinoDateHandler)
      └── DateManager (Laravel driver manager — Fooino\Core\Concretes\Date\DateManager)
          └── Date (Facade — Fooino\Core\Facades\Date)
```

## Usage

### Basic date conversion

```php
Date::convert(date: '2022-12-24', format: 'Y/m/d', to: 'Asia/Tehran'); // '1401/10/03'

dateConvert(date: '2022-12-24', format: 'Y/m/d', to: 'Asia/Tehran'); // '1401/10/03' — global helper
```

### With time and timezone offset

```php
Date::convert(date: '2022-12-24 19:27:08', format: 'Y-m-d H:i:s', to: 'Asia/Tehran'); // '1401-10-03 22:57:08'
```

### Calendar modes

Official calendar (government-set) — Jalali for Iran/Afghanistan, Gregorian for other regions:

```php
Date::officialCalendar()->convert(date: '2022-12-24', format: 'Y/m/d', to: 'Asia/Tehran'); // '1401/10/03'
```

Unofficial calendar (religious/cultural) — adds Hijri (Islamic) support for Middle Eastern timezones:

```php
Date::unofficialCalendar()->convert(date: '2026-06-02', format: 'Y-m-d', to: 'Asia/Dubai'); // '1447-12-16'
```

### From Jalali to UTC

```php
Date::convert(date: '1401-10-03', format: 'Y/m/d', from: 'Asia/Tehran'); // '2022/12/24'
```

### From Hijri to UTC

```php
Date::unofficialCalendar()->convert(date: '1447-12-16', format: 'Y-m-d', from: 'Asia/Dubai'); // '2026-06-01'
```

### Between non-UTC timezones

The converter chains through UTC automatically:

```php
// Tokyo → UTC → Jalali (Tehran)
Date::convert(date: '2022-12-25 00:30:10', format: 'Y/m/d H:i:s', from: 'Asia/Tokyo', to: 'Asia/Tehran'); // '1401/10/03 19:00:10'
```

### Error handling

Return a fallback when the date is invalid:

```php
Date::convert(date: 'invalid', to: 'Asia/Tehran', fallback: '—'); // '—'
```

Throw an exception for programmatic error handling:

```php
try {
    
    Date::convert(date: 'invalid', to: 'Asia/Tehran', throwException: true);

    // 
} catch (CanNotConvertDateException $e) {

    $e->getMessage(); // 'msg.canNotConvertDateExceptionInvalidDate'
    $e->getWith();    // ['original_date' => 'invalid', ...]
}
```

### Timezone validation

```php
Date::validateTimezone('Asia/Tehran'); // true
Date::validateTimezone('Asia/Fooino'); // false

Date::getTimezones(); // all PHP-supported timezone identifiers
```

### Generate a range of dates

```php
Date::datesBetween(from: '2024-01-01', to: '2024-01-05');
// ['2024-01-01', '2024-01-02', '2024-01-03', '2024-01-04', '2024-01-05']

datesBetween(from: '2024-01-01', to: '2024-01-05');
// ['2024-01-01', '2024-01-02', '2024-01-03', '2024-01-04', '2024-01-05'] — global helper
```

Same start and end date returns a single-element array:

```php
Date::datesBetween(from: '2024-06-01', to: '2024-06-01');
// ['2024-06-01']
```

### Custom interval

Use any [DateInterval](https://www.php.net/manual/en/class.dateinterval.php) duration string. Every 4 hours:

```php
Date::datesBetween(from: '2024-01-01 00:00:00', to: '2024-01-02 00:00:00', format: 'Y-m-d H:i:s', interval: 'PT4H');
// ['2024-01-01 00:00:00', '2024-01-01 04:00:00', '2024-01-01 08:00:00', ..., '2024-01-02 00:00:00']
```

Compound intervals — every 2 days and 4 hours:

```php
Date::datesBetween(from: '2024-01-01 00:00:00', to: '2024-01-06 00:00:00', format: 'Y-m-d H:i:s', interval: 'P2DT4H');
// ['2024-01-01 00:00:00', '2024-01-03 04:00:00', '2024-01-05 08:00:00']
```

Weekly interval:

```php
Date::datesBetween(from: '2024-01-01 00:00:00', to: '2024-01-06 00:00:00', format: 'Y-m-d H:i:s', interval: 'P1W');
// ['2024-01-01 00:00:00']
```

### Custom output format

```php
Date::datesBetween(from: '2024-01-01', to: '2024-01-03', format: 'Y/m/d');
// ['2024/01/01', '2024/01/02', '2024/01/03']
```

### Integer (Unix timestamp) input

```php
Date::datesBetween(from: strtotime('2024-01-01'), to: strtotime('2024-01-03'), format: 'Y/m/d');
// ['2024/01/01', '2024/01/02', '2024/01/03']
```

### Error handling

Invalid dates throw `CanNotConvertDateException`:

```php
Date::datesBetween(from: 'foobar', to: '2024-01-05'); // throws CanNotConvertDateException
Date::datesBetween(from: '2024-01-05', to: 'foobar'); // throws CanNotConvertDateException
```

From date after to date throws `FooinoRuntimeException` (code `2`, level `warning`):

```php
Date::datesBetween(from: '2024-06-01', to: '2024-01-01'); // throws FooinoRuntimeException
```

Zero or null interval throws `InfiniteLoopException` (code `251`, level `critical`):

```php
Date::datesBetween(from: '2024-06-01', to: '2024-12-01', interval: 'P0D'); // throws InfiniteLoopException
```

## Calendars and Timezone Mappings

| Calendar | Official Regions | Unofficial Regions |
|---|---|---|
| **Jalali** (Solar Hijri) | Iran, Afghanistan | Iran, Afghanistan |
| **Hijri** (Lunar Hijri) | — | Dubai, Qatar, Riyadh, Muscat, Bahrain, Kuwait, Baghdad, Amman, Beirut, Damascus, Aden |
| **Gregorian** | Everywhere else | Everywhere else |
| **UTC** | UTC | UTC |
