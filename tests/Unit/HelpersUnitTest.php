<?php

describe('Helpers unit tests', function () {

    test('nullIfBlank returns value when it is filled', function () {

        expect(nullIfBlank(value: 0, fallback: 'fooino'))->toEqual(0);
        expect(nullIfBlank(value: 5, fallback: 'fooino'))->toEqual(5);
        expect(nullIfBlank(value: -5.5, fallback: 'fooino'))->toEqual(-5.5);

        expect(nullIfBlank(value: true, fallback: 'fooino'))->toEqual(true);
        expect(nullIfBlank(value: false, fallback: 'fooino'))->toEqual(false);

        expect(nullIfBlank(value: '0', fallback: 'fooino'))->toEqual('0');
        expect(nullIfBlank(value: '0.0', fallback: 'fooino'))->toEqual('0.0');
        expect(nullIfBlank(value: ' foobar ', fallback: 'fooino'))->toEqual(' foobar ');

        expect(nullIfBlank(value: [1, 'foobar', true], fallback: 'fooino'))->toEqual([1, 'foobar', true]);
        expect(nullIfBlank(value: collect([1, 'foobar', true]),  fallback: 'fooino'))->toEqual(collect([1, 'foobar', true]));

        expect(value(nullIfBlank(value: fn() => 'foobar')))->toEqual('foobar');

        $object = new class implements Stringable {
            public function __toString(): string
            {
                return 'foobar';
            }
        };

        expect((string) nullIfBlank(value: $object, fallback: 'fooino'))->toEqual('foobar');
    });

    test('nullIfBlank returns null when the value is blank', function () {

        expect(nullIfBlank(value: null))->toEqual(null);
        expect(nullIfBlank(value: 'null'))->toEqual(null);
        expect(nullIfBlank(value: 'NULL'))->toEqual(null);
        expect(nullIfBlank(value: 'NULl'))->toEqual(null);
        expect(nullIfBlank(value: 'NULl', fallback: 'fooino'))->toEqual('fooino');

        expect(nullIfBlank(value: ''))->toEqual(null);
        expect(nullIfBlank(value: '      '))->toEqual(null);
        expect(nullIfBlank(value: '  "" '))->toEqual(null);
        expect(nullIfBlank(value: '  " '))->toEqual(null);
        expect(nullIfBlank(value: "  ' "))->toEqual(null);
        expect(nullIfBlank(value: "  '' "))->toEqual(null);
        expect(nullIfBlank(value: "  ' \" ' "))->toEqual(null);
        expect(nullIfBlank(value: "  ' \" ' ", fallback: 'fooino'))->toEqual('fooino');

        expect(nullIfBlank(value: []))->toEqual(null);
        expect(nullIfBlank(value: [], fallback: 'fooino'))->toEqual('fooino');
        expect(nullIfBlank(value: collect([])))->toEqual(null);

        $object = new class implements Stringable {
            public function __toString(): string
            {
                return '';
            }
        };

        expect(nullIfBlank(value: $object))->toEqual(null);
    });

    test('nullIfBlankOrZero returns value when it is filled and not zero', function () {

        expect(nullIfBlankOrZero(value: 5, fallback: 'fooino'))->toEqual(5);
        expect(nullIfBlankOrZero(value: -5.5, fallback: 'fooino'))->toEqual(-5.5);

        expect(nullIfBlankOrZero(value: true, fallback: 'fooino'))->toEqual(true);
        expect(nullIfBlankOrZero(value: false, fallback: 'fooino'))->toEqual(false);

        expect(nullIfBlankOrZero(value: ' foobar ', fallback: 'fooino'))->toEqual(' foobar ');

        expect(nullIfBlankOrZero(value: [1, 'foobar', true], fallback: 'fooino'))->toEqual([1, 'foobar', true]);
        expect(nullIfBlankOrZero(value: collect([1, 'foobar', true]),  fallback: 'fooino'))->toEqual(collect([1, 'foobar', true]));

        expect(value(nullIfBlankOrZero(value: fn() => 'foobar')))->toEqual('foobar');

        $object = new class implements Stringable {
            public function __toString(): string
            {
                return 'foobar';
            }
        };

        expect((string) nullIfBlankOrZero(value: $object, fallback: 'fooino'))->toEqual('foobar');
    });

    test('nullIfBlankOrZero returns null when the value is blank or zero', function () {

        expect(nullIfBlankOrZero(value: 0))->toEqual(null);
        expect(nullIfBlankOrZero(value: 0.0))->toEqual(null);
        expect(nullIfBlankOrZero(value: '0'))->toEqual(null);
        expect(nullIfBlankOrZero(value: '0.0'))->toEqual(null);
        expect(nullIfBlankOrZero(value: '0', fallback: 'fooino'))->toEqual('fooino');

        expect(nullIfBlankOrZero(value: null))->toEqual(null);
        expect(nullIfBlankOrZero(value: 'null'))->toEqual(null);
        expect(nullIfBlankOrZero(value: 'NULL'))->toEqual(null);
        expect(nullIfBlankOrZero(value: 'NULl'))->toEqual(null);
        expect(nullIfBlankOrZero(value: 'NULl', fallback: 'fooino'))->toEqual('fooino');

        expect(nullIfBlankOrZero(value: ''))->toEqual(null);
        expect(nullIfBlankOrZero(value: '      '))->toEqual(null);
        expect(nullIfBlankOrZero(value: "  '' "))->toEqual(null);
        expect(nullIfBlankOrZero(value: "  ' \" ' "))->toEqual(null);
        expect(nullIfBlankOrZero(value: "  ' \" ' ", fallback: 'fooino'))->toEqual('fooino');

        expect(nullIfBlankOrZero(value: []))->toEqual(null);
        expect(nullIfBlankOrZero(value: [], fallback: 'fooino'))->toEqual('fooino');
        expect(nullIfBlankOrZero(value: collect([])))->toEqual(null);

        $object = new class implements Stringable {
            public function __toString(): string
            {
                return '';
            }
        };

        expect(nullIfBlankOrZero(value: $object))->toEqual(null);
    });

    test('removeComma returns string and array value without comma', function () {

        expect(removeComma(123))->toEqual(123);
        expect(removeComma(123.11))->toEqual(123.11);

        expect(removeComma(' foobar '))->toEqual(' foobar ');
        expect(removeComma('123,123'))->toEqual('123123');
        expect(removeComma('123,test, '))->toEqual('123test ');

        expect(removeComma(null))->toEqual(null);
        expect(removeComma(true))->toEqual(true);
        expect(removeComma(false))->toEqual(false);

        $stdClass = new stdClass;
        expect(removeComma(['123,123', '123,foobar, ']))->toEqual(['123123', '123foobar ']);
        expect(removeComma(collect([1, 2])))->toEqual(collect([1, 2]));
        expect(removeComma($stdClass))->toEqual($stdClass);
    });

    test('removeSpace returns string and array value without space', function () {

        expect(removeSpace(12))->toEqual(12);
        expect(removeSpace(12.12))->toEqual(12.12);
        
        expect(removeSpace('  '))->toEqual('');
        expect(removeSpace('foobar'))->toEqual('foobar');
        expect(removeSpace(' foobar'))->toEqual('foobar');
        expect(removeSpace('foobar '))->toEqual('foobar');
        expect(removeSpace(' foobar '))->toEqual('foobar');
        expect(removeSpace(' 0912 123 1234 '))->toEqual('09121231234');
        
        expect(removeSpace(null))->toEqual(null);
        expect(removeSpace(true))->toEqual(true);
        expect(removeSpace(false))->toEqual(false);
        
        $stdClass = new stdClass;
        expect(removeSpace([1, ' 0912 123 1234 ']))->toEqual([1, '09121231234']);
        expect(removeSpace(collect([1, 2])))->toEqual(collect([1, 2]));
        expect(removeSpace($stdClass))->toEqual($stdClass);
    });
});
