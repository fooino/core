<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Facades\Json;
use Fooino\Core\Tasks\Tools\PrettifyInputTask;
use Fooino\Core\Tests\TestCase;
use stdClass;

class PrettifyInputTaskUnitTest extends TestCase
{
    public function test_the_task()
    {

        $object = new stdClass;
        $object->number = '۰۱۲۳';

        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', 123), 123);
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', 123.123), 123.123);
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', 'foobar'), 'foobar');
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', 'foobar123'), 'foobar123');
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', []), []);
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', null), null);
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', true), true);
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', false), false);
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', $object), $object);
        $this->assertEquals(gettype(app(PrettifyInputTask::class)->run('phone', 123)), gettype(123));
        $this->assertEquals(gettype(app(PrettifyInputTask::class)->run('phone', 123.123)), gettype(123.123));

        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', 'علیك سلام'), 'علیک سلام');
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', 'عليك سلام'), 'علیک سلام');
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', 'باي بای علیك'), 'بای بای علیک');
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', '۰۱۲۳۴۵۶۷۸۹'), '0123456789');
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', '۰۱۲۳٤٥٦۷۸۹'), '0123456789');
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', 'foobar۰۱۲۳٤٥٦۷۸۹'), 'foobar0123456789');
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', ['۰۱۲۳']), ['0123']);
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', Json::encode(['۰۱۲۳'])), Json::encode(['0123']));
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', Json::encode(['۰۱۲۳', 'foobar4567۸'])), Json::encode(['0123', 'foobar45678']));

        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', 'foobar '), 'foobar');
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', ' foobar'), 'foobar');
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', ' foobar '), 'foobar');

        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', 'Hello 👋🏼'), 'Hello 👋🏼');
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', 'Hello <input name="password" value="123">'), 'Hello');
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', 'Hello <script>alert("XSS");</script> World'), 'Hello alert("XSS"); World');
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', ''), '');
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', '😊😎👍'), '😊😎👍');
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', '😊 <script>alert("XSS");</script> 😎'), '😊 alert("XSS"); 😎');
        $this->assertEquals(app(PrettifyInputTask::class)->run('phone', '<script>alert("XSS");</script>'), 'alert("XSS");');

        $this->assertEquals(
            app(PrettifyInputTask::class)->run('phone', [
                0       => 'bar۰۱۲۳',
                1       => '۱.۰',
                2       => true,
                3       => false,
                4       => null,
                5       => '۱٤۰۱/۱۰/۱٤',
                'foo'   => '۰۱۲۳',
                '2d'    => [
                    '123',
                    '۰۱۲۳'
                ],
                'withKey'    => [
                    'foo' => '123',
                    'bar' => '۰۱۲۳',
                    'third' => [
                        'foo'   => '123',
                        'bar'   => '۰۱۲۳',
                        'john'  => null,
                        'doe'   => true
                    ]
                ]
            ]),
            [
                0       => 'bar0123',
                1       => '1.0',
                2       => true,
                3       => false,
                4       => null,
                5       => '1401/10/14',
                'foo'   => '0123',
                '2d'    => [
                    '123',
                    '0123'
                ],
                'withKey'    => [
                    'foo' => '123',
                    'bar' => '0123',
                    'third' => [
                        'foo'   => '123',
                        'bar'   => '0123',
                        'john'  => null,
                        'doe'   => true
                    ]
                ]
            ]
        );

        $this->assertEquals(app(PrettifyInputTask::class)->run('description', '<script>alert("hi");</script> <h1>hi</h1>'), 'alert("hi"); <h1>hi</h1>');
    }
}
