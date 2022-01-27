<?php

namespace Kibo\Phast\Common;

use Kibo\Phast\Exceptions\UndefinedObjectifiedFunction;
use PHPUnit\Framework\TestCase;

class ObjectifiedFunctionsTest extends TestCase {
    private $funcs;

    public function setUp(): void {
        parent::setUp();
        $this->funcs = new ObjectifiedFunctions();
    }

    public function testCallingFunction() {
        $result = $this->funcs->substr('asdqwe', 0, 3);
        $this->assertEquals('asd', $result);
    }

    public function testCallingProperty() {
        $this->funcs->substr = function (...$args) {
            return $args;
        };
        $result = $this->funcs->substr('asdqwe', 0, 3);
        $this->assertEquals(['asdqwe', 0, 3], $result);
    }

    public function testNotCallingNotCallable() {
        $this->funcs->substr = 'substr';
        $result = $this->funcs->substr('asdqwe', 0, 3);
        $this->assertEquals('asd', $result);
    }

    public function testExceptionOnUndefined() {
        $this->expectException(UndefinedObjectifiedFunction::class);
        $this->funcs->nop();
    }
}
