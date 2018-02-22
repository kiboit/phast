<?php

namespace Kibo\Phast;


use Kibo\Phast\Common\OutputBufferHandler;

class OutputBufferHandlerTest extends \PHPUnit_Framework_TestCase {

    const MAX_BUFFER_SIZE_TO_APPLY = 1024;

    /** @var OutputBufferHandler */
    private $handler;

    public function setUp() {
        $filter = $this->createMock(Filters\HTML\Composite\Filter::class);
        $filter->method('apply')->willReturnCallback(function ($buffer, $offset) {
            return strtoupper(substr($buffer, $offset));
        });

        $this->handler = new OutputBufferHandler(self::MAX_BUFFER_SIZE_TO_APPLY, $filter);
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

    public function testShouldReturnOriginal() {
        $this->assertSame('nope', $this->handler->handleChunk('nope', PHP_OUTPUT_HANDLER_FINAL));
    }

    public function testShouldStopBufferingAfterMax() {
        $chunk = str_repeat('*', self::MAX_BUFFER_SIZE_TO_APPLY);
        $this->assertSame('', $this->handler->handleChunk($chunk, 0));
        $this->assertSame($chunk . '!', $this->handler->handleChunk('!', 0));
        $this->assertSame('?', $this->handler->handleChunk('?', 0));
        $this->assertSame('', $this->handler->handleChunk('', PHP_OUTPUT_HANDLER_FINAL));
    }

    /** @dataProvider shouldApplyData */
    public function testShouldApply($buffer) {
        $filter = $this->createMock(Filters\HTML\Composite\Filter::class);
        $filter->expects($this->once())->method('apply');

        $handler = new OutputBufferHandler(self::MAX_BUFFER_SIZE_TO_APPLY, $filter);
        
        $handler->handleChunk($buffer, PHP_OUTPUT_HANDLER_FINAL);
    }
    
    public function shouldApplyData() {
        yield ["<!DOCTYPE html>\n<html>\n<body></body>\n</html>"];
        yield ["<?xml version=\"1.0\"?\><!DOCTYPE html>\n<html>\n<body></body>\n</html>"];
        yield ["<!doctype html>\n<html>\n<body></body>\n</html>"];
        yield ["<html>\n<body></body>\n</html>"];
        yield ["    \n<!doctype       html>\n<html>\n<body></body>\n</html>"];
        yield ["<!doctype html>\n<!-- hello -->\n<html>\n<body></body>\n</html>"];
    }

    /** @dataProvider shouldNotApplyData */
    public function testShouldNotApply($buffer) {
        $filter = $this->createMock(Filters\HTML\Composite\Filter::class);
        $filter->expects($this->never())->method('apply');

        $handler = new OutputBufferHandler(self::MAX_BUFFER_SIZE_TO_APPLY, $filter);

        $handler->handleChunk($buffer, PHP_OUTPUT_HANDLER_FINAL);
    }
    
    public function shouldNotApplyData() {
        yield ["<html>\n<body>"];
        yield ['<?xml version="1.0"?\><tag>asd</tag>'];
        yield ["\0<html><body></body></html>"];
        yield [sprintf('<html><body>%s</body></html>', str_pad('', self::MAX_BUFFER_SIZE_TO_APPLY, 's'))];
        yield ['<!doctype html><html amp><body></body></html>'];
        yield ['<!doctype html><html ⚡><body></body></html>'];
    }

}
