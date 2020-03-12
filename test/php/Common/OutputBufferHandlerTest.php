<?php

namespace Kibo\Phast;

use Kibo\Phast\Common\OutputBufferHandler;

class OutputBufferHandlerTest extends \PHPUnit_Framework_TestCase {
    const MAX_BUFFER_SIZE_TO_APPLY = 1024;

    /** @var OutputBufferHandler */
    private $handler;

    public function setUp() {
        $this->handler = new OutputBufferHandler(self::MAX_BUFFER_SIZE_TO_APPLY, function ($buffer) {
            return strtoupper($buffer);
        });
    }

    public function testImmediateFinal() {
        $this->assertSame('<html></HTML>', $this->handler->handleChunk('<html></html>', PHP_OUTPUT_HANDLER_FINAL));
    }

    public function testNoFinal() {
        $this->assertSame('', $this->handler->handleChunk('Hello!', 0));
    }

    public function testImmediateOutput() {
        $this->assertSame('<html>', $this->handler->handleChunk('<html>', 0));
        $this->assertSame('', $this->handler->handleChunk('</html>', 0));
        $this->assertSame('</HTML>', $this->handler->handleChunk('', PHP_OUTPUT_HANDLER_FINAL));
    }

    public function testDataInFinalChunk() {
        $this->assertSame('<html>', $this->handler->handleChunk('<html>', 0));
        $this->assertSame('', $this->handler->handleChunk('Hey', 0));
        $this->assertSame('HEY</HTML>', $this->handler->handleChunk('</html>', PHP_OUTPUT_HANDLER_FINAL));
    }

    public function testDataAfterFinalChunk() {
        $this->assertSame('<html></HTML>', $this->handler->handleChunk('<html></html>', PHP_OUTPUT_HANDLER_FINAL));
        $this->assertSame('<html></html>', $this->handler->handleChunk('<html></html>', 0));
    }

    public function testMultipleFinalChunk() {
        $this->assertSame('<html></HTML>', $this->handler->handleChunk('<html></html>', PHP_OUTPUT_HANDLER_FINAL));
        $this->assertSame('<html></html>', $this->handler->handleChunk('<html></html>', PHP_OUTPUT_HANDLER_FINAL));
    }

    public function testSplitImmediateOutput() {
        $this->assertSame('', $this->handler->handleChunk('<html', 0));
        $this->assertSame('<html>', $this->handler->handleChunk('><head', 0));
        $this->assertSame('<head>', $this->handler->handleChunk('><body></body>', 0));
        $this->assertSame('<BODY></BODY>', $this->handler->handleChunk('', PHP_OUTPUT_HANDLER_FINAL));
    }

    public function testShouldStopBufferingAfterMax() {
        $chunk = str_repeat('*', self::MAX_BUFFER_SIZE_TO_APPLY);
        $this->assertSame('', $this->handler->handleChunk($chunk, 0));
        $this->assertSame($chunk . '!', $this->handler->handleChunk('!', 0));
        $this->assertSame('?', $this->handler->handleChunk('?', 0));
        $this->assertSame('', $this->handler->handleChunk('', PHP_OUTPUT_HANDLER_FINAL));
    }
}
