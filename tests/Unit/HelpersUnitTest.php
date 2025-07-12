<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Tests\TestCase;
use stdClass;

class HelpersUnitTest extends TestCase
{
    public function test_trim_empty_string()
    {
        $stdClass = new stdClass;
        $this->assertTrue(trimEmptyString(12) == 12);
        $this->assertTrue(trimEmptyString(12.12) == 12.12);
        $this->assertTrue(trimEmptyString(true) == true);
        $this->assertTrue(trimEmptyString(false) == false);
        $this->assertTrue(trimEmptyString([1, 2]) == [1, 2]);
        $this->assertTrue(trimEmptyString($stdClass) == $stdClass);
        $this->assertTrue(trimEmptyString('foobar') == 'foobar');
        $this->assertTrue(trimEmptyString(' foobar') == 'foobar');
        $this->assertTrue(trimEmptyString('foobar ') == 'foobar');
        $this->assertTrue(trimEmptyString(' foobar ') == 'foobar');
    }
}
