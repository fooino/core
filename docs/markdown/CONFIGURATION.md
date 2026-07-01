# Configuration

## System Requirements

| Requirement | Minimum | Notes |
|---|---|---|
| PHP | 8.5+ | |
| Laravel | 13.x (via Orchestra Testbench) | Required — uses Facades, Managers, ServiceProvider, JsonResponse |
| `ext-bcmath` | * | Required for all arbitrary-precision math operations |
| `ext-intl` | * | Required for Hijri (Islamic lunar) calendar conversions |
| `ext-json` | * | Required for JSON validation and serialization |
| `ext-mbstring` | * | Required for multibyte string operations in Sanitizer |
| `morilog/jalali` | ^3.5 | Required for Jalali (Solar Hijri) calendar |

---

## Runtime Configuration

### 1. System Default Timezone Must Be UTC

**Critical.** The system default timezone **must** be set to `UTC` before any date operations. This is enforced at runtime — every `FooinoDateHandler` instantiation checks `date_default_timezone_get()` and throws `CanNotConvertDateException` (code 1004, emergency level) if the timezone is not UTC.

Set this in your application bootstrap:

```php
date_default_timezone_set('UTC');
```

Or in `config/app.php`:

```php
'timezone' => 'UTC',
```

### 2. Application Locale

The locale defaults to Persian (`fa`) in `getDefaultLocale()`. Override it at runtime:

```php
setDefaultLocale('en'); // or any supported locale
```

### 3. User Timezone

Store and retrieve the current user's timezone for date formatting:

```php
setUserTimezone('Asia/Tehran');
$tz = getUserTimezone(); // falls back to 'UTC'
```

No config file is published by this package. These values are stored directly in Laravel's config using `config()`.

---

## Package Constants

Defined in `helpers.php` when `FOOINO_CORE_CONSTANTS_DEFINED` is not already defined.

| Constant | Value | Used By |
|---|---|---|
| `STANDARD_DATE_TIME_FORMAT` | `Y-m-d H:i:s` | All date conversions |
| `STANDARD_DATE_FORMAT` | `Y-m-d` | Date-only conversions |
| `FOOINO_PER_PAGE` | 30 | `perPage()` helper default |
| `FOOINO_MAX_PER_PAGE` | 300 | `perPage()` helper maximum |
| `FOOINO_PRIORITY_STEP` | 10000 | Priority ordering |
