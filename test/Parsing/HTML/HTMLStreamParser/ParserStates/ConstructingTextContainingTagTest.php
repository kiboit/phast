<?php

namespace Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserStates;


use Kibo\Phast\Parsing\HTML\HTMLStreamElements\OpeningTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\TextContainingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamParser\ParserTestCase;

class ConstructingTextContainingTagTest extends ParserTestCase {

    /**
     * @var ConstructingTextContainingTag
     */
    private $state;

    public function setUp() {
        parent::setUp();
        $openingTag = new OpeningTag('style', ['class' => 'the-value']);
        $openingTag->setStreamOffsets(10, 15);
        $this->state = new ConstructingTextContainingTag($this->parser, $openingTag);
        $this->parser->setState($this->state);
    }

    public function testNormalProcessing() {
        $this->state->text('some-text');

        $currentState = $this->parser->getState();
        $this->assertInstanceOf(ConstructingTextContainingTag::class, $currentState);
        $this->assertEmpty($this->htmlStream->getElements());

        $this->state->endTag('style', 100, 200);
        $newState = $this->parser->getState();
        $this->assertInstanceOf(AwaitingTag::class, $newState);

        $elements = $this->htmlStream->getElements();
        $this->assertCount(1, $elements);

        /** @var TextContainingTag $tag */
        $tag = $elements[0];
        $this->assertInstanceOf(TextContainingTag::class, $tag);
        $this->assertEquals('style', $tag->getTagName());
        $this->assertFalse($tag->hasAttribute('src'));
        $this->assertTrue($tag->hasAttribute('class'));
        $this->assertEquals('the-value', $tag->getAttribute('class'));
        $this->assertEquals('some-text', $tag->getTextContent());
        $this->assertEquals(10, $tag->getStartStreamOffset());
        $this->assertEquals(200, $tag->getEndStreamOffset());
    }

    public function testResettingOnWrongClosingTag() {
        $this->state->endTag('script', 10, 20);
        $newState = $this->parser->getState();
        $this->assertInstanceOf(AwaitingTag::class, $newState);
        $this->assertEmpty($this->htmlStream->getElements());
    }

}
