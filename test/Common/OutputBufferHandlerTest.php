<?php

namespace Kibo\Phast;


use Kibo\Phast\Common\OutputBufferHandler;

class OutputBufferHandlerTest extends \PHPUnit_Framework_TestCase {

    /** @var PhastDocumentFilters */
    private $handler;

    public function setUp() {
        $filter = $this->createMock(Filters\HTML\Composite\Filter::class);
        $filter->method('apply')->willReturnCallback(function ($buffer, $offset) {
            return strtoupper(substr($buffer, $offset));
        });

        $this->handler = new OutputBufferHandler($filter);
    }

    public function testImmediateFinal() {
        $this->assertSame('HELLO!', $this->handler->handleChunk('Hello!', PHP_OUTPUT_HANDLER_FINAL));
    }

    public function testNoFinal() {
        $this->assertSame('', $this->handler->handleChunk('Hello!', 0));
    }

    public function testBuffering() {
        $this->assertSame('', $this->handler->handleChunk('Hello, ', 0));
        $this->assertSame('', $this->handler->handleChunk('World!', 0));
        $this->assertSame('HELLO, WORLD!', $this->handler->handleChunk('', PHP_OUTPUT_HANDLER_FINAL));
    }

    public function testDataInFinalChunk() {
        $this->assertSame('', $this->handler->handleChunk('Hello, ', 0));
        $this->assertSame('', $this->handler->handleChunk('World!', 0));
        $this->assertSame('HELLO, WORLD! HEY!', $this->handler->handleChunk(' Hey!', PHP_OUTPUT_HANDLER_FINAL));
    }

    public function testDataAfterFinalChunk() {
        $this->assertSame('HELLO, WORLD!', $this->handler->handleChunk('Hello, World!', PHP_OUTPUT_HANDLER_FINAL));
        $this->assertSame('hey', $this->handler->handleChunk('hey', 0));
    }

    public function testMultipleFinalChunk() {
        $this->assertSame('HELLO, WORLD!', $this->handler->handleChunk('Hello, World!', PHP_OUTPUT_HANDLER_FINAL));
        $this->assertSame('hey', $this->handler->handleChunk('hey', PHP_OUTPUT_HANDLER_FINAL));
    }

}
