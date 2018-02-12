<?php

namespace Kibo\Phast\Parsing\HTML\HTMLStreamParser;


use Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserStates\AwaitingTag;

class ParserTest extends ParserTestCase {

    public function testInitialState() {
        $this->assertInstanceOf(AwaitingTag::class, $this->parser->getState());
    }

    public function testReset() {
        $this->parser->setState($this->createMock(ParserState::class));
        $this->assertNotInstanceOf(AwaitingTag::class, $this->parser->getState());
        $this->parser->reset();
        $this->assertInstanceOf(AwaitingTag::class, $this->parser->getState());
    }

    public function testReturnsValueFromStartTag() {
        $state = $this->createMock(ParserState::class);
        $state->expects($this->once())
            ->method('startTag')
            ->willReturn('the-return-value');
        $this->parser->setState($state);
        $returnValue = $this->parser->startTag('tag', [], 0, 10);
        $this->assertEquals('the-return-value', $returnValue);
    }

}
