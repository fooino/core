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

        expect(value(nullIfBlank(fn() => 'foobar')))->toEqual('foobar');

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
});
