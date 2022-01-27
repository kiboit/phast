<?php

namespace Kibo\Phast;

use Kibo\Phast\Common\OutputBufferHandler;

class OutputBufferHandlerTest extends \PHPUnit\Framework\TestCase {
    const MAX_BUFFER_SIZE_TO_APPLY = 1024;

    /** @var OutputBufferHandler */
    private $handler;

    public function setUp(): void {
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

    /** @dataProvider immediateOutputData */
    public function testImmediateOutput($chunk) {
        $this->assertSame($chunk, $this->handler->handleChunk($chunk, 0));
        $this->assertSame('', $this->handler->handleChunk('</html>', 0));
        $this->assertSame('</HTML>', $this->handler->handleChunk('', PHP_OUTPUT_HANDLER_FINAL));
    }

    public function immediateOutputData() {
        yield ['<html>'];
        yield ['<!doctype html>'];
        yield ['<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml">'];
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
