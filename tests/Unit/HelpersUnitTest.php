<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Exceptions\FooinoRuntimeException;
use Fooino\Core\Tests\Data\Datasets;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

use stdClass;
use Stringable;
use Exception;

class CustomClass
{
    public function __construct(public int $precision = 0) {}

    public function pi(): float
    {
        return 3.14;
    }

    public function abs(int $a): int
    {
        return abs($a);
    }

    public function getPrecision(): int
    {
        return $this->precision;
    }
};

enum WrappedBackedEnum: string
{
    case ACTIVE   = 'ACTIVE';
    case INACTIVE = 'INACTIVE';
};

enum WrappedNumberBackedEnum: int
{
    case ONE    = 1;
    case TWO    = 2;
};

enum WrappedPureEnum
{
    case ACTIVE;
    case INACTIVE;
};

class TestFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => [
                'required',
                'max:255'
            ],
            'email' => [
                'required',
                'email'
            ]
        ];
    }
}

class UnauthorizedFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return false;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
            ],
        ];
    }
}

describe('Helpers unit tests', function () {

    test('isZero returns true', function () {

        foreach (
            [
                ...Datasets::zeros(),
                new class implements Stringable {

                    public function __toString()
                    {
                        return '0.0E+10';
                    }
                },
            ] as $zero
        ) {

            expect(isZero(value: $zero))->toBeTrue();

            //
        }

        //
    });

    test('isZero returns false', function () {

        foreach (
            [
                ...Datasets::nonZero(),
                true,
                false,
                null,
                ['foo' => 'bar'],
                [],
                [0],
                fn() => 0,
                new stdClass,
                new class implements Stringable {

                    public function __toString()
                    {
                        return '.e+10';
                    }
                },
            ]
            as $nonZero
        ) {

            expect(isZero(value: $nonZero))->toBeFalse();

            //
        }

        //
    });

    test('nullIfBlank returns value when it is filled', function () {

        expect(nullIfBlank(value: 0, fallback: 'fooino'))->toBe(0);
        expect(nullIfBlank(value: 5, fallback: 'fooino'))->toBe(5);
        expect(nullIfBlank(value: 5_000_000_000_000, fallback: 'fooino'))->toBe(5_000_000_000_000);
        expect(nullIfBlank(value: 4E+6, fallback: 'fooino'))->toBe(4E+6);

        expect(nullIfBlank(value: 0.0, fallback: 'fooino'))->toBe(0.0);
        expect(nullIfBlank(value: -5.5, fallback: 'fooino'))->toBe(-5.5);
        expect(nullIfBlank(value: 0.0000101, fallback: 'fooino'))->toBe(0.0000101);
        expect(nullIfBlank(value: 4.21E-6, fallback: 'fooino'))->toBe(4.21E-6);

        expect(nullIfBlank(value: true, fallback: 'fooino'))->toBe(true);
        expect(nullIfBlank(value: false, fallback: 'fooino'))->toBe(false);

        expect(nullIfBlank(value: '0', fallback: 'fooino'))->toBe('0');
        expect(nullIfBlank(value: '0.0', fallback: 'fooino'))->toBe('0.0');
        expect(nullIfBlank(value: 'false', fallback: 'fooino'))->toBe('false');
        expect(nullIfBlank(value: 'true', fallback: 'fooino'))->toBe('true');
        expect(nullIfBlank(value: 'nill', fallback: 'fooino'))->toBe('nill');
        expect(nullIfBlank(value: 'notnull', fallback: 'fooino'))->toBe('notnull');
        expect(nullIfBlank(value: ' foobar ', fallback: 'fooino'))->toBe(' foobar ');
        expect(nullIfBlank(value: ' null"ish ', fallback: 'fooino'))->toBe(' null"ish ');
        expect(nullIfBlank(value: " ' ` \" \n \t null nan undefined foobar", fallback: 'fooino'))->toBe(" ' ` \" \n \t null nan undefined foobar");

        expect(nullIfBlank(value: [0], fallback: 'fooino'))->toBe([0]);
        expect(nullIfBlank(value: [null], fallback: 'fooino'))->toBe([null]);
        expect(nullIfBlank(value: [true], fallback: 'fooino'))->toBe([true]);
        expect(nullIfBlank(value: [false], fallback: 'fooino'))->toBe([false]);
        expect(nullIfBlank(value: [""], fallback: 'fooino'))->toBe([""]);
        expect(nullIfBlank(value: [1, 'foobar', true], fallback: 'fooino'))->toBe([1, 'foobar', true]);

        expect(nullIfBlank(value: collect([null]),  fallback: 'fooino'))->toEqual(collect([null]));
        expect(nullIfBlank(value: collect([false]),  fallback: 'fooino'))->toEqual(collect([false]));
        expect(nullIfBlank(value: collect([""]),  fallback: 'fooino'))->toEqual(collect([""]));
        expect(nullIfBlank(value: collect([1, "foobar", true]),  fallback: 'fooino'))->toEqual(collect([1, "foobar", true]));

        expect(value(nullIfBlank(value: fn() => 'foobar',  fallback: 'fooino')))->toBe('foobar');

        $object = new class implements Stringable {
            public function __toString(): string
            {
                return 'foobar';
            }
        };

        expect((string) nullIfBlank(value: $object, fallback: 'fooino'))->toBe('foobar');

        expect(nullIfBlank(value: new stdClass, fallback: 'fooino'))->toBeInstanceOf(stdClass::class);
    });

    test('nullIfBlank returns null when the value is blank', function () {

        expect(nullIfBlank(value: null))->toBeNull();

        expect(nullIfBlank(value: 'null'))->toBeNull();
        expect(nullIfBlank(value: 'NULL'))->toBeNull();
        expect(nullIfBlank(value: 'NULl'))->toBeNull();
        expect(nullIfBlank(value: ' NaN'))->toBeNull();
        expect(nullIfBlank(value: 'undefined '))->toBeNull();
        expect(nullIfBlank(value: '" \'null ` nan undefined'))->toBeNull();
        expect(nullIfBlank(value: 'null', fallback: 'fooino'))->toEqual('fooino');

        expect(nullIfBlank(value: 'nan'))->toBeNull();
        expect(nullIfBlank(value: 'undefined'))->toBeNull();
        expect(nullIfBlank(value: 'nullnull'))->toBeNull();
        expect(nullIfBlank(value: 'nanundefined'))->toBeNull();
        expect(nullIfBlank(value: "nu\tll"))->toBeNull();
        expect(nullIfBlank(value: "nu\nll"))->toBeNull();

        expect(nullIfBlank(value: ''))->toBeNull();
        expect(nullIfBlank(value: '      '))->toBeNull();
        expect(nullIfBlank(value: '  "" '))->toBeNull();
        expect(nullIfBlank(value: '  " '))->toBeNull();
        expect(nullIfBlank(value: "  ' "))->toBeNull();
        expect(nullIfBlank(value: "  ``` "))->toBeNull();
        expect(nullIfBlank(value: "  '' "))->toBeNull();
        expect(nullIfBlank(value: "  ' \" ' "))->toBeNull();
        expect(nullIfBlank(value: "  ' \" ' ", fallback: 'fooino'))->toEqual('fooino');
        expect(nullIfBlank(value: " ' ` \" \n \t null nan undefined "))->toBeNull();

        expect(nullIfBlank(value: []))->toBeNull();
        expect(nullIfBlank(value: [], fallback: 'fooino'))->toEqual('fooino');
        expect(nullIfBlank(value: collect([])))->toBeNull();

        $object = new class implements Stringable {
            public function __toString(): string
            {
                return '';
            }
        };

        expect(nullIfBlank(value: $object))->toBeNull();
    });

    test('nullIfBlankOrZero returns value when it is filled and not zero', function () {

        expect(nullIfBlankOrZero(value: 5, fallback: 'fooino'))->toBe(5);
        expect(nullIfBlankOrZero(value: 0.0000001, fallback: 'fooino'))->toBe(0.0000001);
        expect(nullIfBlankOrZero(value: 4.12E-10, fallback: 'fooino'))->toBe(4.12E-10);
        expect(nullIfBlankOrZero(value: -5.5, fallback: 'fooino'))->toBe(-5.5);

        expect(nullIfBlankOrZero(value: true, fallback: 'fooino'))->toBe(true);
        expect(nullIfBlankOrZero(value: false, fallback: 'fooino'))->toBe(false);

        expect(nullIfBlankOrZero(value: '0.25', fallback: 'fooino'))->toBe('0.25');
        expect(nullIfBlankOrZero(value: 'false', fallback: 'fooino'))->toBe('false');
        expect(nullIfBlankOrZero(value: ' foobar ', fallback: 'fooino'))->toBe(' foobar ');

        expect(nullIfBlankOrZero(value: [null], fallback: 'fooino'))->toBe([null]);
        expect(nullIfBlankOrZero(value: [false], fallback: 'fooino'))->toBe([false]);
        expect(nullIfBlankOrZero(value: [""], fallback: 'fooino'))->toBe([""]);
        expect(nullIfBlankOrZero(value: [1, 'foobar', true], fallback: 'fooino'))->toBe([1, 'foobar', true]);
        expect(nullIfBlankOrZero(value: collect([1, 'foobar', true]),  fallback: 'fooino'))->toEqual(collect([1, 'foobar', true]));

        expect(value(nullIfBlankOrZero(value: fn() => 'foobar',  fallback: 'fooino')))->toEqual('foobar');

        $object = new class implements Stringable {
            public function __toString(): string
            {
                return 'foobar';
            }
        };

        expect((string) nullIfBlankOrZero(value: $object, fallback: 'fooino'))->toEqual('foobar');
    });

    test('nullIfBlankOrZero returns null when the value is blank or zero', function () {

        expect(nullIfBlankOrZero(value: 0))->toBeNull();
        expect(nullIfBlankOrZero(value: -0))->toBeNull();
        expect(nullIfBlankOrZero(value: '0'))->toBeNull();
        expect(nullIfBlankOrZero(value: '-0'))->toBeNull();
        expect(nullIfBlankOrZero(value: '0000'))->toBeNull();
        expect(nullIfBlankOrZero(value: 0, fallback: 0))->toBe(0);
        expect(nullIfBlankOrZero(value: -0, fallback: false))->toBe(false);
        expect(nullIfBlankOrZero(value: '0', fallback: 'fooino'))->toEqual('fooino');

        expect(nullIfBlankOrZero(value: 0.0))->toBeNull();
        expect(nullIfBlankOrZero(value: -0.0))->toBeNull();
        expect(nullIfBlankOrZero(value: '0.0'))->toBeNull();
        expect(nullIfBlankOrZero(value: '-0.0'))->toBeNull();
        expect(nullIfBlankOrZero(value: '0.000000'))->toBeNull();
        expect(nullIfBlankOrZero(value: '-000.000'))->toBeNull();


        expect(nullIfBlankOrZero(value: null))->toBeNull();
        expect(nullIfBlankOrZero(value: 'null'))->toBeNull();
        expect(nullIfBlankOrZero(value: 'NaN'))->toBeNull();
        expect(nullIfBlankOrZero(value: 'Undefined'))->toBeNull();

        expect(nullIfBlankOrZero(value: ''))->toBeNull();
        expect(nullIfBlankOrZero(value: '      '))->toBeNull();
        expect(nullIfBlankOrZero(value: " ` '' "))->toBeNull();
        expect(nullIfBlankOrZero(value: "  ' \" ' "))->toBeNull();
        expect(nullIfBlankOrZero(value: "  ' \" ' ", fallback: 'fooino'))->toEqual('fooino');

        expect(nullIfBlankOrZero(value: []))->toBeNull();
        expect(nullIfBlankOrZero(value: collect([])))->toBeNull();

        $object = new class implements Stringable {
            public function __toString(): string
            {
                return '';
            }
        };

        expect(nullIfBlankOrZero(value: $object))->toBeNull();

        $object = new class implements Stringable {
            public function __toString(): string
            {
                return '0';
            }
        };

        expect(nullIfBlankOrZero(value: $object))->toBeNull();
    });

    test('nullIfBlankInput helper', function () {

        expect(nullIfBlankInput(key: 'missing_key'))->toBeNull();

        request()->merge(['title' => '']);
        expect(nullIfBlankInput(key: 'title'))->toBeNull();

        request()->merge(['title' => 'foobar']);
        expect(nullIfBlankInput(key: 'title'))->toBe('foobar');

        request()->merge(['title' => '']);
        expect(nullIfBlankInput(key: 'title', fallback: 'fallback'))->toBe('fallback');

        request()->merge(['title' => 'null']);
        expect(nullIfBlankInput(key: 'title'))->toBeNull();

        $customRequest = new Request();
        $customRequest->merge(['custom' => 'value']);
        expect(nullIfBlankInput(key: 'custom', request: $customRequest))->toBe('value');
    });

    test('nullIfBlankOrZeroInput helper', function () {

        expect(nullIfBlankOrZeroInput(key: 'missing_key'))->toBeNull();

        request()->merge(['title' => '']);
        expect(nullIfBlankOrZeroInput(key: 'title'))->toBeNull();

        request()->merge(['title' => '0.0E-10']);
        expect(nullIfBlankOrZeroInput(key: 'title'))->toBeNull();

        request()->merge(['title' => 'foobar']);
        expect(nullIfBlankOrZeroInput(key: 'title'))->toBe('foobar');

        request()->merge(['title' => '']);
        expect(nullIfBlankOrZeroInput(key: 'title', fallback: 'fallback'))->toBe('fallback');

        request()->merge(['title' => '-.0']);
        expect(nullIfBlankOrZeroInput(key: 'title', fallback: 'fallback'))->toBe('fallback');

        request()->merge(['title' => 'null']);
        expect(nullIfBlankOrZeroInput(key: 'title'))->toBeNull();

        $customRequest = new Request();
        $customRequest->merge(['custom' => '0']);
        expect(nullIfBlankOrZeroInput(key: 'custom', request: $customRequest))->toBeNull();
    });

    test('unwrapBackedEnum helper', function () {

        expect(unwrapBackedEnum(value: 0))->toBe(0);
        expect(unwrapBackedEnum(value: 123))->toBe(123);
        expect(unwrapBackedEnum(value: 123.123))->toBe(123.123);

        expect(unwrapBackedEnum(value: ''))->toBe('');
        expect(unwrapBackedEnum(value: ' '))->toBe(' ');
        expect(unwrapBackedEnum(value: 'foobar'))->toBe('foobar');

        expect(unwrapBackedEnum(value: false))->toBeFalse();
        expect(unwrapBackedEnum(value: true))->toBeTrue();
        expect(unwrapBackedEnum(value: null))->toBeNull();

        expect(unwrapBackedEnum(value: []))->toBe([]);
        expect(unwrapBackedEnum(value: [123]))->toBe([123]);
        expect(unwrapBackedEnum(value: collect(['123'])))->toEqual(collect(['123']));

        $object = new stdClass;
        expect(unwrapBackedEnum(value: $object))->toBe($object);

        expect(unwrapBackedEnum(value: WrappedBackedEnum::ACTIVE))->toBe('ACTIVE');
        expect(unwrapBackedEnum(value: WrappedBackedEnum::INACTIVE))->toBe('INACTIVE');

        expect(unwrapBackedEnum(value: WrappedNumberBackedEnum::ONE))->toBe(1);
        expect(unwrapBackedEnum(value: WrappedNumberBackedEnum::TWO))->toBe(2);

        expect(unwrapBackedEnum(value: WrappedPureEnum::ACTIVE))->toBe(WrappedPureEnum::ACTIVE);
    });

    test('mergeArraysByKey helper', function () {

        expect(mergeArraysByKey())->toBe([]);

        expect(mergeArraysByKey(['foo' => ['a']]))->toBe(['foo' => ['a']]);

        expect(mergeArraysByKey(['key' => null]))->toBe(['key' => [null]]);

        expect(mergeArraysByKey(['key' => []]))->toBe(['key' => []]);

        expect(mergeArraysByKey(['x' => ['a' => 1]], ['x' => ['a' => 2]]))->toBe(['x' => ['a' => 2]]);

        $a = ['created' => ['aa', 'bb']];
        $b = ['created' => 'cc', 'updated' => 'gg'];
        $c = ['created' => ['dd', 'ee']];
        $d = ['updated' => ['ff']];
        $e = ['deleted' => 'hh'];

        expect(mergeArraysByKey($a, $b, $c, $d, $e))->toBe([
            'created' => ['aa', 'bb', 'cc', 'dd', 'ee'],
            'updated' => ['gg', 'ff'],
            'deleted' => ['hh'],
        ]);
    });

    test('removeComma helper', function () {

        expect(removeComma(value: 123))->toBe(123);
        expect(removeComma(value: 123.11))->toBe(123.11);

        expect(removeComma(value: ' foobar '))->toBe(' foobar ');
        expect(removeComma(value: '123,123'))->toBe('123123');
        expect(removeComma(value: '5,000,000', replace: '_'))->toBe('5_000_000');
        expect(removeComma(value: '123,test, '))->toBe('123test ');

        expect(removeComma(value: null))->toBe(null);
        expect(removeComma(value: true))->toBe(true);
        expect(removeComma(value: false))->toBe(false);

        expect(removeComma(value: ['123,123', '123,foobar, ']))->toBe(['123123', '123foobar ']);
        expect(removeComma(value: ['5,000,000', '123,foobar, '], replace: '_'))->toBe(['5_000_000', '123_foobar_ ']);

        expect(removeComma(value: [123, 'abc,def']))->toBe([123, 'abcdef']);

        expect(removeComma(value: [0, 1, 11.11, null, true, false, '123,123']))->toBe([0, 1, 11.11, null, true, false, '123123']);

        expect(removeComma(value: ','))->toBe('');
    });

    test('removeWhitespace helper', function () {

        expect(removeWhitespace(value: 12))->toBe(12);
        expect(removeWhitespace(value: 12.12))->toBe(12.12);

        expect(removeWhitespace(value: ''))->toBe('');
        expect(removeWhitespace(value: '  '))->toBe('');
        expect(removeWhitespace(value: 'foobar'))->toBe('foobar');
        expect(removeWhitespace(value: ' foobar'))->toBe('foobar');
        expect(removeWhitespace(value: 'foobar '))->toBe('foobar');
        expect(removeWhitespace(value: ' foobar '))->toBe('foobar');
        expect(removeWhitespace(value: ' 0912 123 1234 '))->toBe('09121231234');
        expect(removeWhitespace(value: ' 0912 123 1234 ', replace: "_"))->toBe('_0912_123_1234_');

        expect(removeWhitespace(value: null))->toBe(null);
        expect(removeWhitespace(value: true))->toBe(true);
        expect(removeWhitespace(value: false))->toBe(false);

        expect(removeWhitespace(value: [1, ' 0912 123 1234 ']))->toBe([1, '09121231234']);
        expect(removeWhitespace(value: [1, ' 0912 123 1234 '], replace: "_"))->toBe([1, '_0912_123_1234_']);

        expect(removeWhitespace(value: [0, 1, 11.11, null, true, false, ' 123 123 ']))->toBe([0, 1, 11.11, null, true, false, '123123']);

        expect(removeWhitespace(value: "foo\nbar"))->toBe('foobar');
        expect(removeWhitespace(value: "foo\tbar"))->toBe('foobar');
        expect(removeWhitespace(value: ["foo\nbar", "baz\tqux"]))->toBe(['foobar', 'bazqux']);
    });

    test('sanitizeNumber helper',  function () {

        expect(sanitizeNumber(123))->toBe(123);
        expect(sanitizeNumber(123.123))->toBe(123.123);

        expect(sanitizeNumber('+98 912 111 2222 '))->toBe('+989121112222');
        expect(sanitizeNumber(' 1,222 333,444'))->toBe('1222333444');
        expect(sanitizeNumber(' '))->toBe('');

        expect(sanitizeNumber(null))->toBe(null);
        expect(sanitizeNumber(true))->toBe(true);
        expect(sanitizeNumber(false))->toBe(false);

        expect(sanitizeNumber([1, '123,123 ', ' 0912 123 1234 ']))->toBe([1, '123123', '09121231234']);

        expect(sanitizeNumber([0, 1, 11.11, null, true, false, ' 1,234 ']))->toBe([0, 1, 11.11, null, true, false, '1234']);
    });

    test('replaceSlashWithDash helper', function () {

        expect(replaceSlashWithDash(value: 123))->toBe(123);
        expect(replaceSlashWithDash(value: 123.123))->toBe(123.123);

        expect(replaceSlashWithDash(value: '2023/01/02'))->toBe('2023-01-02');
        expect(replaceSlashWithDash(value: ''))->toBe('');
        expect(replaceSlashWithDash(value: ' foobar'))->toBe(' foobar');

        expect(replaceSlashWithDash(value: null))->toBe(null);
        expect(replaceSlashWithDash(value: true))->toBe(true);
        expect(replaceSlashWithDash(value: false))->toBe(false);

        expect(replaceSlashWithDash(value: ['hi/hello', '2023/01/02 11:00:00']))->toBe(['hi-hello', '2023-01-02 11:00:00']);
        expect(replaceSlashWithDash(value: [123]))->toBe([123]);

        expect(replaceSlashWithDash(value: [0, 1, 11.11, null, true, false, '2023/01/02']))->toBe([0, 1, 11.11, null, true, false, '2023-01-02']);

        expect(replaceSlashWithDash(value: '/'))->toBe('-');
        expect(replaceSlashWithDash(value: 'a//b'))->toBe('a--b');
    });

    test('setUserTimezone and getUserTimezone helper', function () {

        expect(config('user-timezone'))->toBeNull();

        setUserTimezone(timezone: 'Asia/Tehran');
        expect(config('user-timezone'))->toBe('Asia/Tehran');
        expect(getUserTimezone())->toBe('Asia/Tehran');

        config(['user-timezone' => null]);
        expect(getUserTimezone())->toBe('UTC');

        setUserTimezone(timezone: '');
        expect(config('user-timezone'))->toBe('');
        expect(getUserTimezone())->toBe('UTC');
    });

    test('setDefaultLocale and getDefaultLocale helper', function () {

        expect(config('app.locale'))->toBe('en');
        expect(getDefaultLocale())->toBe('en');

        setDefaultLocale(locale: 'fa');
        expect(config('app.locale'))->toBe('fa');
        expect(getDefaultLocale())->toBe('fa');

        config(['app.locale' => null]);
        expect(getDefaultLocale())->toBe('fa');

        setDefaultLocale(locale: '');
        expect(config('app.locale'))->toBe('');
        expect(getDefaultLocale())->toBe('fa');
    });

    test('perPage helper', function () {

        expect(perPage())->toBe(FOOINO_PER_PAGE);

        request()->merge(['per_page' => 10]);
        expect(perPage())->toBe(10);

        request()->merge(['per_page' => '10']);
        expect(perPage())->toBe(10);

        request()->merge(['per_page' => 0]);
        expect(perPage())->toBe(FOOINO_PER_PAGE);

        request()->merge(['per_page' => 301]);
        expect(perPage())->toBe(FOOINO_MAX_PER_PAGE);

        request()->merge(['per_page' => 'abc']);
        expect(perPage())->toBe(FOOINO_PER_PAGE);

        request()->merge(['per_page' => 1.5]);
        expect(perPage())->toBe(1);

        request()->merge(['per_page' => -5]);
        expect(perPage())->toBe(FOOINO_PER_PAGE);

        request()->merge(['limit' => 350]);
        expect(perPage(key: 'limit', maxPerPage: 500))->toBe(350);

        request()->merge(['limit' => 50]);
        expect(perPage(key: 'limit', maxPerPage: 100))->toBe(50);

        request()->merge(['limit' => 150]);
        expect(perPage(key: 'limit', maxPerPage: 100))->toBe(100);

        $customRequest = new Request(['per_page' => '25']);
        expect(perPage(request: $customRequest))->toBe(25);
    });

    test('currentDate and currentDateTime helper', function () {

        expect(currentDate())->toBe(date('Y-m-d'));
        expect(currentDateTime())->toBe(date('Y-m-d H:i:s'));
    });

    test('currentDateTs and currentDateTimeTs helper', function () {

        expect(currentDateTs())->toBe(strtotime(currentDate()));
        expect(currentDateTimeTs())->toBe(strtotime(currentDateTime()));

        expect(currentDateTs())->toBeInt();
        expect(currentDateTimeTs())->toBeInt();
    });

    test('strToDate helper', function () {

        expect(strToDate(str: '2026-06-27'))->toBe('2026-06-27');

        expect(strToDate(str: '2026-06-27 14:30:00'))->toBe('2026-06-27');

        expect(strToDate(str: 'next monday'))->toBe(date(STANDARD_DATE_FORMAT, strtotime('next monday')));

        expect(fn() => strToDate(str: 'not a date'))->toThrow(FooinoRuntimeException::class, 'msg.fooinoRunTimeExceptionInvalidDateString');

        try {

            strToDate(str: 'not a date');

            //
        } catch (FooinoRuntimeException $e) {

            expect($e->getMessage())->toBe('msg.fooinoRunTimeExceptionInvalidDateString');
            expect($e->getCode())->toBe(3);
            expect($e->reportable())->toBeTrue();
            expect($e->getLevel())->toBe('error');
            expect($e->getHttpStatusCode())->toBe(500);
            expect($e->getWith())->toBe([
                'method' => 'strToDate',
                'input'  => 'not a date',
            ]);
        }
    });

    test('strToDateTime helper', function () {

        expect(strToDateTime(str: '2026-06-27'))->toBe('2026-06-27 00:00:00');

        expect(strToDateTime(str: '2026-06-27 14:30:00'))->toBe('2026-06-27 14:30:00');

        expect(strToDateTime(str: 'next monday'))->toBe(date(STANDARD_DATE_TIME_FORMAT, strtotime('next monday')));

        expect(fn() => strToDateTime(str: 'not a date'))->toThrow(FooinoRuntimeException::class, 'msg.fooinoRunTimeExceptionInvalidDateString');

        try {

            strToDateTime(str: 'not a date');

            //
        } catch (FooinoRuntimeException $e) {

            expect($e->getMessage())->toBe('msg.fooinoRunTimeExceptionInvalidDateString');
            expect($e->getCode())->toBe(3);
            expect($e->reportable())->toBeTrue();
            expect($e->getLevel())->toBe('error');
            expect($e->getHttpStatusCode())->toBe(500);
            expect($e->getWith())->toBe([
                'method' => 'strToDateTime',
                'input'  => 'not a date',
            ]);
        }
    });

    test('callMethodIfExists helper', function () {

        expect(callMethodIfExists(object: new CustomClass, method: 'pi', fallback: 'fooino'))->toBe(3.14);

        expect(callMethodIfExists(object: CustomClass::class, method: 'getPrecision', fallback: 'fooino'))->toBe(0);
        expect(callMethodIfExists(object: CustomClass::class, method: 'getPrecision', fallback: 'fooino', constructorArgs: ['precision' => 2]))->toBe(2);
        expect(callMethodIfExists(object: new CustomClass(2), method: 'getPrecision', fallback: 'fooino'))->toBe(2);

        expect(callMethodIfExists(object: "foobar", method: 'abs', fallback: 'NOT EXIST'))->toBe('NOT EXIST');
        expect(callMethodIfExists(object: CustomClass::class, method: 'abs', fallback: 'fooino', methodArgs: ['a' => -5]))->toBe(5);

        expect(callMethodIfExists(object: CustomClass::class, method: 'power', fallback: 'NOT EXIST'))->toBe('NOT EXIST');
        expect(callMethodIfExists(object: CustomClass::class, method: 'power', fallback: fn($a) => $a * $a, methodArgs: ['a' => 5]))->toBe(25);

        expect(callMethodIfExists(object: new CustomClass, method: 'pi'))->toBe(3.14);

        expect(callMethodIfExists(object: new CustomClass, method: 'nonexistent', fallback: 'default'))->toBe('default');
    });

    test('percentageChange helper', function () {

        expect(percentageChange(from: 200, to: 50))->toBe('-75');
        expect(percentageChange(from: 50, to: 200))->toBe('300');

        expect(percentageChange(from: 20, to: 40))->toBe('100');
        expect(percentageChange(from: 40, to: 20))->toBe('-50');

        expect(percentageChange(from: 10, to: 12))->toBe('20');
        expect(percentageChange(from: 12, to: 10))->toBe('-16.66');

        expect(percentageChange(from: 12, to: 12))->toBe('0');
        expect(percentageChange(from: 12, to: -12))->toBe('-200');
        expect(percentageChange(from: -12, to: 12))->toBe('200');

        expect(percentageChange(from: 13, to: 14))->toBe('7.69');
        expect(percentageChange(from: 13, to: 14, precision: 12))->toBe('7.6923076923');

        $zeros = array_filter(Datasets::shuffleZeros(), 'is_numeric');

        foreach ($zeros as $zero) {

            expect(percentageChange(from: 12, to: $zero))->toBe('-100');
            expect(percentageChange(from: $zero, to: 12))->toBe('100');

            expect(percentageChange(from: -12, to: $zero))->toBe('-100');
            expect(percentageChange(from: $zero, to: -12))->toBe('100');

            expect(percentageChange(from: $zero, to: $zero))->toBe('0');
        }

        expect(percentageChange(from: 1, to: 3))->toBe('200');
        expect(percentageChange(from: -12, to: -24))->toBe('-100');
        expect(percentageChange(from: -24, to: -12))->toBe('50');

        expect(percentageChange(from: 10.5, to: 20.5))->toBe('95.23');
        expect(percentageChange(from: '10', to: '20'))->toBe('100');

        expect(percentageChange(from: 1000, to: 1001))->toBe('0.1');
        expect(percentageChange(from: 1001, to: 1000))->toBe('-0.09');

        expect(percentageChange(from: 1000000, to: 2000000))->toBe('100');
        expect(percentageChange(from: 0.0001, to: 0.0002))->toBe('100');
    });

    test('unitNumberFormat helper', function () {

        $trillion = __('msg.trillion');
        $billion = __('msg.billion');
        $million = __('msg.million');
        $thousand = __('msg.thousand');

        expect(unitNumberFormat(number: 1.e20, unit: 'Persons'))->toBe('100,000,000 ' . $trillion . ' Persons');

        expect(unitNumberFormat(number: 10_000_000_000,))->toBe('10 ' . $billion);

        expect(unitNumberFormat(number: -10_000_000_000,))->toBe('-10 ' . $billion);

        expect(unitNumberFormat(number: 1_000_000_000,))->toBe('1 ' . $billion);

        expect(unitNumberFormat(number: 123_000_000, unit: '$'))->toBe('123 ' . $million . ' $');

        expect(unitNumberFormat(number: -123_076_012, unit: '$'))->toBe('-123.076 ' . $million . ' $');

        expect(unitNumberFormat(number: 123_076_012, unit: '$', precision: 5))->toBe('123.07601 ' . $million . ' $');

        expect(unitNumberFormat(number: 1000, unit: 'Persons'))->toBe('1 ' . $thousand . ' Persons');

        expect(unitNumberFormat(number: 2501, unit: 'Persons'))->toBe('2.501 ' . $thousand . ' Persons');

        expect(unitNumberFormat(number: 100, unit: 'minute'))->toBe('100 minute');

        expect(unitNumberFormat(number: 0.011, unit: 'seconds'))->toBe('0.011 seconds');
        expect(unitNumberFormat(number: -0.011, unit: 'seconds'))->toBe('-0.011 seconds');
        expect(unitNumberFormat(number: 0.0112, unit: 'seconds'))->toBe('0.011 seconds');

        expect(unitNumberFormat(number: 0.0E+1, unit: 'seconds'))->toBe('0 seconds');
        expect(unitNumberFormat(number: 0, unit: 'seconds'))->toBe('0 seconds');
    });

    test('unitSizeFormat helper', function () {

        expect(unitSizeFormat(bytes: 1099511627776))->toBe('1 TB');
        expect(unitSizeFormat(bytes: 2199023255552))->toBe('2 TB');
        expect(unitSizeFormat(bytes: 1649267441664))->toBe('1.5 TB');

        expect(unitSizeFormat(bytes: 1073741824))->toBe('1 GB');
        expect(unitSizeFormat(bytes: 2147483648))->toBe('2 GB');
        expect(unitSizeFormat(bytes: 1610612736))->toBe('1.5 GB');

        expect(unitSizeFormat(bytes: 1048576))->toBe('1 MB');
        expect(unitSizeFormat(bytes: 2097152))->toBe('2 MB');
        expect(unitSizeFormat(bytes: 1572864))->toBe('1.5 MB');

        expect(unitSizeFormat(bytes: 1024))->toBe('1 KB');
        expect(unitSizeFormat(bytes: 2048))->toBe('2 KB');
        expect(unitSizeFormat(bytes: 1536))->toBe('1.5 KB');

        expect(unitSizeFormat(bytes: 500))->toBe('500 Bytes');
        expect(unitSizeFormat(bytes: 2))->toBe('2 Bytes');

        expect(unitSizeFormat(bytes: 1))->toBe('1 Byte');

        expect(unitSizeFormat(bytes: 0))->toBe('0 Byte');

        expect(unitSizeFormat(bytes: -10))->toBe('-10 msg.isInvalid');

        expect(unitSizeFormat(bytes: 1234567))->toBe('1.177 MB');
        expect(unitSizeFormat(bytes: 1234567, precision: 5))->toBe('1.17737 MB');

        expect(unitSizeFormat(bytes: 1234567, precision: 0))->toBe('1 MB');
        expect(unitSizeFormat(bytes: 1536, precision: 0))->toBe('1 KB');

        expect(unitSizeFormat(bytes: '1024'))->toBe('1 KB');
        expect(unitSizeFormat(bytes: '500'))->toBe('500 Bytes');
        expect(unitSizeFormat(bytes: '0'))->toBe('0 Byte');

        expect(unitSizeFormat(bytes: 5497558138880))->toBe('5 TB');
        expect(unitSizeFormat(bytes: 2199023255552, precision: 0))->toBe('2 TB');
    });

    test('sanitizeUrl helper', function () {

        expect(sanitizeUrl(value: 1))->toBe(1);
        expect(sanitizeUrl(value: 1.1))->toBe(1.1);
        expect(sanitizeUrl(value: null))->toBe(null);
        expect(sanitizeUrl(value: true))->toBe(true);
        expect(sanitizeUrl(value: false))->toBe(false);
        expect(sanitizeUrl(value: []))->toBe([]);
        expect(sanitizeUrl(value: ''))->toBe('');
        expect(sanitizeUrl(value: '0'))->toBe('0');

        expect(sanitizeUrl(value: "test / prettify canonical ? %& $ *"))->toBe("test / prettify canonical ? %& - -");

        expect(sanitizeUrl(value: "https://google.com/laravel_tips!for-2025"))->toBe("https://google.com/laravel_tips-for-2025");
        expect(sanitizeUrl(value: ["https://google.com/laravel_tips!for-2025", "https://fooino.com/I am_god"]))->toBe(["https://google.com/laravel_tips-for-2025", "https://fooino.com/I am_god"]);

        expect(sanitizeUrl(value: "https://example.com/search?q=hello&page=1"))->toBe("https://example.com/search?q=hello&page=1");
        expect(sanitizeUrl(value: "https://example.com/page#section"))->toBe("https://example.com/page#section");
        expect(sanitizeUrl(value: "https://user:pass@example.com/path"))->toBe("https://user:pass@example.com/path");
        expect(sanitizeUrl(value: "https://x.com/test 🎉"))->toBe("https://x.com/test -");

        expect(sanitizeUrl(value: "https://example.com/page;param=value"))->toBe("https://example.com/page-param=value");

        expect(sanitizeUrl(value: "https://example.com/(test)"))->toBe("https://example.com/-test-");
        expect(sanitizeUrl(value: "https://example.com/'test'"))->toBe("https://example.com/-test-");
        expect(sanitizeUrl(value: "https://example.com/<test>"))->toBe("https://example.com/-test-");

        expect(sanitizeUrl(value: "foo   bar   baz"))->toBe("foo   bar   baz");

        expect(sanitizeUrl(value: "https://github.com/user/repo_name"))->toBe("https://github.com/user/repo_name");
        expect(sanitizeUrl(value: "https://en.wikipedia.org/wiki/C_Sharp"))->toBe("https://en.wikipedia.org/wiki/C_Sharp");
        expect(sanitizeUrl(value: "https://example.com/search?q=foo_bar"))->toBe("https://example.com/search?q=foo_bar");

        expect(sanitizeUrl(value: ["https://example.com/a_b", "https://example.com/c_d"]))->toBe(["https://example.com/a_b", "https://example.com/c_d"]);

        expect(sanitizeUrl(value: "https://example.com/api/v1/../v2/resource"))->toBe("https://example.com/api/v1/../v2/resource");
        expect(sanitizeUrl(value: "https://example.com/a/../../c"))->toBe("https://example.com/a/../../c");


        expect(sanitizeUrl(value: "https://example.com/path with spaces"))->toBe("https://example.com/path with spaces");
        expect(sanitizeUrl(value: ["https://example.com/a b", "https://example.com/c d"]))->toBe(["https://example.com/a b", "https://example.com/c d"]);
    });

    test('sanitizeSlug helper', function () {

        expect(sanitizeSlug(value: 1))->toBe(1);
        expect(sanitizeSlug(value: 1.1))->toBe(1.1);
        expect(sanitizeSlug(value: null))->toBe(null);
        expect(sanitizeSlug(value: true))->toBe(true);
        expect(sanitizeSlug(value: false))->toBe(false);
        expect(sanitizeSlug(value: []))->toBe([]);

        expect(sanitizeSlug(value: "test / Prettify slug ? %& $ *"))->toBe('test-prettify-slug');
        expect(sanitizeSlug(value: "Laravel_tips!for-2025"))->toBe('laravel-tips-for-2025');
        expect(sanitizeSlug(value: ["Laravel_tips!for-2025", "I am_god"]))->toBe(["laravel-tips-for-2025", "i-am-god"]);

        expect(sanitizeSlug(value: "café"))->toBe('cafe');
        expect(sanitizeSlug(value: "straße"))->toBe('strasse');
        expect(sanitizeSlug(value: "über cool"))->toBe('uber-cool');
        expect(sanitizeSlug(value: "ñoño"))->toBe('nono');
        expect(sanitizeSlug(value: "München"))->toBe('munchen');

        expect(sanitizeSlug(value: ["café", "straße"]))->toBe(["cafe", "strasse"]);
        expect(sanitizeSlug(value: ["über cool", "hello world"]))->toBe(["uber-cool", "hello-world"]);

        expect(sanitizeSlug(value: "café 🎉"))->toBe('cafe');
        expect(sanitizeSlug(value: "hello 🎉 world"))->toBe('hello-world');

        expect(sanitizeSlug(value: "hello-world"))->toBe('hello-world');
        expect(sanitizeSlug(value: "foo_bar_baz"))->toBe('foo-bar-baz');
        expect(sanitizeSlug(value: "test123"))->toBe('test123');
    });

    test('jsonAttribute helper', function () {

        Schema::create('json_attr_table', function (Blueprint $table) {
            $table->id();
            $table->json('info')->nullable();
            $table->timestamps();
        });

        $model = new class extends Model {

            protected $guarded = ['id'];

            protected $table = 'json_attr_table';

            public function info(): Attribute
            {
                return jsonAttribute();
            }
        };

        $model->create(['info' => '   ']);
        expect($model->find(1)->info)->toBe([]);
        $this->assertDatabaseHas('json_attr_table', ['id' => 1, 'info' => null]);

        $data = ['foo' => 'bar', 123];
        $model->create(['info' => $data]);
        expect($model->find(2)->info)->toBe($data);
        $this->assertDatabaseHas('json_attr_table', ['id' => 2, 'info' => json_encode($data)]);

        $model->create(['info' => null]);
        expect($model->find(3)->info)->toBe([]);
        $this->assertDatabaseHas('json_attr_table', ['id' => 3, 'info' => null]);

        $model->create(['info' => [0]]);
        expect($model->find(4)->info)->toBe([0]);
        $this->assertDatabaseHas('json_attr_table', ['id' => 4, 'info' => '[0]']);

        $model->create(['info' => [false]]);
        expect($model->find(5)->info)->toBe([false]);
        $this->assertDatabaseHas('json_attr_table', ['id' => 5, 'info' => '[false]']);

        $model->create(['info' => [null]]);
        expect($model->find(6)->info)->toBe([null]);
        $this->assertDatabaseHas('json_attr_table', ['id' => 6, 'info' => '[null]']);

        $model->create(['info' => ['null']]);
        expect($model->find(7)->info)->toBe(['null']);
        $this->assertDatabaseHas('json_attr_table', ['id' => 7, 'info' => '["null"]']);

        $model->create(['info' => []]);
        expect($model->find(8)->info)->toBe([]);
        $this->assertDatabaseHas('json_attr_table', ['id' => 8, 'info' => null]);

        $model->create(['info' => '{"a":1}']);
        expect($model->find(9)->info)->toBe(['a' => 1]);
        $this->assertDatabaseHas('json_attr_table', ['id' => 9, 'info' => '{"a":1}']);

        $model->create(['info' => '{}']);
        expect($model->find(10)->info)->toBe([]);
        $this->assertDatabaseHas('json_attr_table', ['id' => 10, 'info' => '{}']);

        $nested = [['a' => 1], ['b' => 2]];
        $model->create(['info' => $nested]);
        expect($model->find(11)->info)->toBe($nested);
        $this->assertDatabaseHas('json_attr_table', ['id' => 11, 'info' => json_encode($nested)]);

        $m = $model->create(['info' => ['a' => 1]]);
        $m->update(['info' => ['b' => 2]]);
        expect($m->fresh()->info)->toBe(['b' => 2]);
        $this->assertDatabaseHas('json_attr_table', ['id' => 12, 'info' => '{"b":2}']);

        $m = $model->create(['info' => ['a' => 1, 'b' => 2]]);
        $m->update(['info' => ['a' => 1, 'b' => 3]]);
        expect($m->fresh()->info)->toBe(['a' => 1, 'b' => 3]);
        $this->assertDatabaseHas('json_attr_table', ['id' => 13, 'info' => '{"a":1,"b":3}']);

        $m = $model->create(['info' => ['a' => 1, 'b' => 2]]);
        $m->update(['info' => ['a' => 1]]);
        expect($m->fresh()->info)->toBe(['a' => 1]);
        $this->assertDatabaseHas('json_attr_table', ['id' => 14, 'info' => '{"a":1}']);

        $m = $model->create(['info' => ['a' => 1]]);
        $m->update(['info' => ['a' => 1, 'b' => 2]]);
        expect($m->fresh()->info)->toBe(['a' => 1, 'b' => 2]);
        $this->assertDatabaseHas('json_attr_table', ['id' => 15, 'info' => '{"a":1,"b":2}']);
    });

    test('resolveRequest helper', function () {

        expect(fn() => resolveRequest(request: TestFormRequest::class))->toThrow(ValidationException::class, 'The name field is required');

        $user = new class extends User {};

        $resolved = resolveRequest(
            request: TestFormRequest::class,
            data: [
                'name' => 'foobar',
                'email' => 'foobar@gmail.com'
            ],
            user: $user,
        );

        expect($resolved)->toBeInstanceOf(TestFormRequest::class);
        expect($resolved->safe()->name)->toBe('foobar');
        expect($resolved->safe()->email)->toBe('foobar@gmail.com');
        expect($resolved->getUserResolver()())->toBe($user);
        expect($resolved->user())->toBe($user);

        $resolved = resolveRequest(
            request: TestFormRequest::class,
            data: [
                'name' => 'foo',
                'email' => 'foo@bar.com'
            ],
        );

        expect($resolved)->toBeInstanceOf(TestFormRequest::class);
        expect($resolved->safe()->name)->toBe('foo');

        $validated = resolveRequest(
            request: TestFormRequest::class,
            data: [
                'name' => 'hello',
                'email' => 'hello@test.com'
            ]
        )
            ->validated();

        expect($validated)->toBe([
            'name' => 'hello',
            'email' => 'hello@test.com'
        ]);

        $filtered = resolveRequest(
            request: TestFormRequest::class,
            data: [
                'name' => 'test',
                'email' => 'test@test.com',
                'extra' => 'should_be_stripped'
            ]
        )
            ->validated();

        expect($filtered)->toBe([
            'name' => 'test',
            'email' => 'test@test.com'
        ]);
        expect(isset($filtered['extra']))->toBeFalse();

        expect(fn() => resolveRequest(request: UnauthorizedFormRequest::class, data: ['title' => 'anything']))->toThrow(AuthorizationException::class, 'This action is unauthorized.');

        $noUser = resolveRequest(
            request: TestFormRequest::class,
            data: [
                'name' => 'nouser',
                'email' => 'nouser@test.com'
            ]
        );

        expect($noUser->user())->toBeNull();
    });
});
