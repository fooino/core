<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Exceptions\CanNotConvertDateException;
use Fooino\Core\Exceptions\FooinoException;
use Fooino\Core\Exceptions\TransactionRollBackedException;
use Fooino\Core\Tests\Data\Datasets;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
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

describe('Helpers unit tests', function () {

    test('isZero returns true', function () {

        foreach (
            Datasets::merge(
                'zeros',
                new class implements Stringable {
                    public function __toString()
                    {
                        return '0';
                    }
                },
            ) as $zero
        ) {

            expect(isZero($zero))->toBeTrue();

            // 
        }

        // 
    });


    test('isZero returns false', function () {

        foreach (
            Datasets::merge(
                'nonZero',
                true,
                false,
                fn() => [],
                fn() => [0],
                fn() => fn() => 0,
                new stdClass,
                new class implements Stringable {
                    public function __toString()
                    {
                        return 'foobar';
                    }
                }
            ) as $nonZero
        ) {

            expect(isZero($nonZero))->toBeFalse();

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

    test('percentageChange method', function () {

        expect(percentageChange(from: 200, to: 50))->toBe('-75');
        expect(percentageChange(from: 20, to: 40))->toBe('100');
        expect(percentageChange(from: 40, to: 20))->toBe('-50');
        expect(percentageChange(from: 10, to: 12))->toBe('20');

        expect(percentageChange(from: 12, to: 12))->toBe('0');
        expect(percentageChange(from: 12, to: -12))->toBe('-200');
        expect(percentageChange(from: 12, to: 0))->toBe('-100');
        expect(percentageChange(from: 0, to: -12))->toBe('100');
        expect(percentageChange(from: -12, to: 12))->toBe('200');

        expect(percentageChange(from: 13, to: 14))->toBe('7.69');
        expect(percentageChange(from: 13, to: 14, precision: 12))->toBe('7.6923076923');

        $zeros = array_filter(Datasets::zeros(), 'is_numeric');
        $lastIndex = count($zeros) - 1;

        expect(percentageChange(from: 12, to: $zeros[rand(0, $lastIndex)]))->toBe('-100');
        expect(percentageChange(from: $zeros[rand(0, $lastIndex)], to: -12))->toBe('100');
    });

    test('unitNumberFormat method', function () {

        $trillion = __('msg.trillion');
        $billion = __('msg.billion');
        $million = __('msg.million');
        $thousand = __('msg.thousand');

        expect(unitNumberFormat(number: 1.e20, unit: 'Persons'))->toBe('100,000,000 ' . $trillion . ' Persons');

        expect(unitNumberFormat(number: 10_000_000_000,))->toBe('10 ' . $billion);

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

    test('unitSizeFormat method', function () {

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

        expect(unitSizeFormat(bytes: 500))->toBe('500 bytes');
        expect(unitSizeFormat(bytes: 2))->toBe('2 bytes');

        expect(unitSizeFormat(bytes: 1))->toBe('1 byte');

        expect(unitSizeFormat(bytes: 0))->toBe('0 byte');

        expect(unitSizeFormat(bytes: -10))->toBe('-10 msg.isInvalid');

        expect(unitSizeFormat(bytes: 1234567))->toBe('1.177 MB');
        expect(unitSizeFormat(bytes: 1234567, precision: 5))->toBe('1.17737 MB');
    });

    test('datesBetween method', function () {

        expect(datesBetween(from: '2024-01-01', to: '2024-01-05'))->toBe([
            '2024-01-01',
            '2024-01-02',
            '2024-01-03',
            '2024-01-04',
            '2024-01-05',
        ]);

        expect(datesBetween(from: '2024-06-01', to: '2024-06-01'))->toBe(['2024-06-01']);

        expect(datesBetween(from: '2024-01-01', to: '2024-01-03', format: 'Y/m/d'))->toBe([
            '2024/01/01',
            '2024/01/02',
            '2024/01/03',
        ]);

        expect(datesBetween(from: '2024-01-01 00:00:00', to: '2024-01-02 00:00:00', format: STANDARD_DATE_TIME_FORMAT, interval: 'PT4H'))->toBe([
            '2024-01-01 00:00:00',
            '2024-01-01 04:00:00',
            '2024-01-01 08:00:00',
            '2024-01-01 12:00:00',
            '2024-01-01 16:00:00',
            '2024-01-01 20:00:00',
            '2024-01-02 00:00:00',
        ]);

        expect(datesBetween(from: '2024-01-01 00:00:00', to: '2024-01-06 00:00:00', format: STANDARD_DATE_TIME_FORMAT, interval: 'P2DT4H'))->toBe([
            '2024-01-01 00:00:00',
            '2024-01-03 04:00:00',
            '2024-01-05 08:00:00',
        ]);

        expect(datesBetween(from: '2024-01-01 00:00:00', to: '2024-01-06 00:00:00', format: STANDARD_DATE_TIME_FORMAT, interval: 'P1W'))->toBe(['2024-01-01 00:00:00']);

        expect(datesBetween(from: strtotime('2024-01-01'), to: strtotime('2024-01-03')))->toBe([
            '2024-01-01',
            '2024-01-02',
            '2024-01-03',
        ]);

        expect(fn() => datesBetween(from: 'foobar', to: '2024-01-05'))->toThrow(CanNotConvertDateException::class);
        expect(fn() => datesBetween(from: '2024-01-05', to: 'foobar'))->toThrow(CanNotConvertDateException::class);

        expect(fn() => datesBetween(from: '2024-06-01', to: '2024-01-01'))->toThrow(FooinoException::class);

        try {

            datesBetween(from: '2024-06-01', to: '2024-01-01');

            // 
        } catch (FooinoException $e) {

            expect($e->getMessage())->toBe('msg.invalidPeriodForDateRange');
            expect($e->getCode())->toBe(1001);
            expect($e->reportable())->toBeTrue();
            expect($e->getLevel())->toBe('warning');
            expect($e->getWith())->toBe([
                'from'      => '2024-06-01',
                'to'        => '2024-01-01',
                'format'    => STANDARD_DATE_FORMAT,
                'interval'  => 'P1D',
            ]);
        }
    });

    test('sanitizeUrl and sanitizeSlug method', function () {

        expect(sanitizeUrl(value: 1))->toBe(1);
        expect(sanitizeUrl(value: 1.1))->toBe(1.1);
        expect(sanitizeUrl(value: null))->toBe(null);
        expect(sanitizeUrl(value: true))->toBe(true);
        expect(sanitizeUrl(value: false))->toBe(false);
        expect(sanitizeUrl(value: []))->toBe([]);

        expect(sanitizeUrl(value: "test / prettify canonical ? %& $ *"))->toBe("test-/-prettify-canonical-?-%&-");

        expect(sanitizeUrl(value: "https://google.com/laravel_tips!for-2025"))->toBe("https://google.com/laravel-tips-for-2025");
        expect(sanitizeUrl(value: ["https://google.com/laravel_tips!for-2025", "https://fooino.com/I am_god"]))->toBe(["https://google.com/laravel-tips-for-2025", "https://fooino.com/I-am-god"]);


        expect(sanitizeSlug(value: 1))->toBe(1);
        expect(sanitizeSlug(value: 1.1))->toBe(1.1);
        expect(sanitizeSlug(value: null))->toBe(null);
        expect(sanitizeSlug(value: true))->toBe(true);
        expect(sanitizeSlug(value: false))->toBe(false);
        expect(sanitizeSlug(value: []))->toBe([]);

        expect(sanitizeSlug(value: "test / Prettify slug ? %& $ *"))->toBe('test-prettify-slug');
        expect(sanitizeSlug(value: "Laravel_tips!for-2025"))->toBe('laravel-tips-for-2025');
        expect(sanitizeSlug(value: ["Laravel_tips!for-2025", "I am_god"]))->toBe(["laravel-tips-for-2025", "i-am-god"]);
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
        expect($model->find(1)->getRawOriginal('info'))->toBeNull();

        $data = ['foo' => 'bar', 123];
        $model->create(['info' => $data]);
        expect($model->find(2)->info)->toBe($data);
        expect($model->find(2)->getRawOriginal('info'))->toBe(json_encode($data));

        $model->create(['info' => null]);
        expect($model->find(3)->info)->toBe([]);
        expect($model->find(3)->getRawOriginal('info'))->toBeNull();
    });

    test('resolveRequest helper', function () {

        expect(fn() => resolveRequest(request: TestFormRequest::class))
            ->toThrow(ValidationException::class, 'The name field is required');

        $user = new class extends User {};

        $resolved = resolveRequest(
            request: TestFormRequest::class,
            data: ['name' => 'foobar', 'email' => 'foobar@gmail.com'],
            user: $user,
        );

        expect($resolved)->toBeInstanceOf(TestFormRequest::class);
        expect($resolved->safe()->name)->toBe('foobar');
        expect($resolved->safe()->email)->toBe('foobar@gmail.com');
        expect($resolved->getUserResolver()())->toBe($user);

        $resolved = resolveRequest(
            request: TestFormRequest::class,
            data: ['name' => 'foo', 'email' => 'foo@bar.com'],
        );

        expect($resolved)->toBeInstanceOf(TestFormRequest::class);
        expect($resolved->safe()->name)->toBe('foo');
    });


    test('dbTransaction helper', function () {

        Schema::create('tx_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        $user = new class extends User {
            protected $guarded = ['id'];
            protected $table = 'tx_users';
        };

        $result = dbTransaction(function () use ($user) {
            $user->create(['name' => 'foo']);
            $user->find(1)->update(['name' => 'foobar']);

            return $user->find(1);
        });

        expect($result)->toBeInstanceOf(User::class);
        expect($result->name)->toBe('foobar');

        expect(fn() => dbTransaction(function () use ($user) {
            $user->findOrFail(999);
        }))->toThrow(TransactionRollBackedException::class);
    });

    test('userInfo helper', function () {

        Schema::create('ui_blogs', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('creatorable');
            $table->timestamps();
        });

        Schema::create('ui_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('country_code')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('full_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->timestamps();
        });

        $blog = new class extends Model {

            protected $guarded = ['id'];

            protected $table = 'ui_blogs';

            public function creatorable(): MorphTo
            {
                return $this->morphTo('creatorable');
            }
        };

        $user = new class extends User {
            protected $guarded = ['id'];
            protected $table = 'ui_users';

            public function objectName()
            {
                return ['type' => 'user'];
            }
        };

        $user->create([]);
        $blog->create(['creatorable_type' => get_class($user), 'creatorable_id' => 1]);

        $b = $blog->find(1);
        expect(userInfo($b, 'creatorable'))->toBe([
            'id' => 0,
            'country_id' => 0,
            'full_name' => '',
            'country_code' => '',
            'phone_number' => '',
            'phone_number_original' => '',
            'type' => __('msg.unknown'),
        ]);

        $b = $blog->with('creatorable')->find(1);
        expect(userInfo($b, 'creatorable'))->toBe([
            'id' => 1.0,
            'country_id' => 0.0,
            'full_name' => '',
            'country_code' => '',
            'phone_number' => '',
            'phone_number_original' => '',
            'type' => 'user',
        ]);

        $user->find(1)->delete();
        $b = $blog->with('creatorable')->find(1);
        expect(userInfo($b, 'creatorable'))->toBe([
            'id' => 0,
            'country_id' => 0,
            'full_name' => '',
            'country_code' => '',
            'phone_number' => '',
            'phone_number_original' => '',
            'type' => __('msg.unknown'),
        ]);

        $user->create(['country_id' => 105, 'country_code' => 'IR', 'first_name' => 'foo', 'last_name' => 'ino', 'phone_number' => '09121231234']);
        $blog->create(['creatorable_type' => get_class($user), 'creatorable_id' => 2]);
        $b = $blog->with('creatorable')->find(2);
        expect(userInfo($b, 'creatorable'))->toBe([
            'id' => 2.0,
            'country_id' => 105.0,
            'full_name' => 'foo ino',
            'country_code' => 'IR',
            'phone_number' => '09121231234',
            'phone_number_original' => '09121231234',
            'type' => 'user',
        ]);
    });

    test('getUserable helper', function () {
        Schema::create('gu_users', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        expect(getUserable(able: 'removerable'))->toBe([
            'removerable_type' => null,
            'removerable_id' => null,
        ]);

        expect(fn() => getUserable(able: 'removerable', throwException: true))
            ->toThrow(Exception::class, 'The user is empty');

        $user = new class extends User {
            protected $guarded = ['id'];
            protected $table = 'gu_users';
        };

        expect(getUserable(able: 'removerable', user: $user->find(999)))->toBe([
            'removerable_type' => null,
            'removerable_id' => null,
        ]);

        $user->create();

        expect(getUserable(able: 'removerable', user: $user->find(1)))->toBe([
            'removerable_type' => get_class($user),
            'removerable_id' => 1,
        ]);

        request()->setUserResolver(fn() => $user->find(1));

        expect(getUserable(able: 'removerable'))->toBe([
            'removerable_type' => get_class($user),
            'removerable_id' => 1,
        ]);

        $resolved = resolveRequest(
            request: TestFormRequest::class,
            data: ['name' => 'foobar', 'email' => 'foobar@gmail.com'],
            user: $user->find(1),
        );

        expect(getUserable(able: 'removerable', user: $resolved))->toBe([
            'removerable_type' => get_class($user),
            'removerable_id' => 1,
        ]);
    });
});
